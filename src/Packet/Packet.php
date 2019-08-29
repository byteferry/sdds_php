<?php

/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\Packet;

use Sdds\Dispatcher\DispatcherTrait;

/**
 * Class Packet
 * @package Sdds\Packet
 */
class Packet
{
    use DispatcherTrait;

    /**
     * @var string
     * @desc give the name part of function in the extension.
     */
    public $event_type = 'packet';
    /**
     * @var object
     */
    public $Schema;

    /**
     * @var object
     */
    public $Stream;

    /**
     * @var object
     */
    public $Bitwise;


    /**
     * Packet constructor.
     */
    public function __construct($channel_name)
    {
        $this->channel_name = $channel_name;
    }


    /**
     * @param $schema
     * @param $stream
     */
    public function init($schema, $stream)
    {
        $this->Schema = $schema;
        $this->Stream = $stream;
     }

    /**
     * @param $stream
     * @return array
     */
    public function streamToByteArray($stream){
        $byteArr = str_split($stream);
        return $byteArr;
    }

}