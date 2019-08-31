<?php
/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\Channels;


use Sdds\Dispatcher\Dispatcher;

/**
 * Class ChannelAggregate
 * @package Sdds\Channels
 */
class ChannelAggregate
{

    /**
     * @var null
     */
    public static $packet_instance = null;


    /**
     * Dispatcher constructor.
     */
    final private function __construct()
    {

    }

    /**
     * clone is not allowed!
     */
    final private function __clone(){}


    /**
     * @return mixed
     */
    final public static function getInstance()
    {
        static $instances = array();

        $calledClass = get_called_class();

        if (!isset($instances[$calledClass]))
        {
            $instances[$calledClass] = new $calledClass();
        }

        return $instances[$calledClass];
    }

    /**
     * @return mixed
     */
    public function getDispatcher(){
        return Dispatcher::getInstance($this->channel_name,$this->action_type);
    }
}