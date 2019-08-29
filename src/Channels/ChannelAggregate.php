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
    public function getDispatch(){
        return Dispatcher::getInstance($this->channel_name,$this->action_type);
    }
}