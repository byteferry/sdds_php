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
use Sdds\Constants\ActionTypeContants;
use Sdds\Packet\PacketDecoder;

class InputChannel extends ChannelAggregate
{
    public $action_type = ActionTypeContants::INPUT;

    public $channel_name;

    public $register_finised = false;
    /**
     * @param $channel_name
     * @param $schema_file
     * @return mixed
     */
    public function getDecoder($channel_name,$schema_file){
        if(false === $this->register_finised){
            $this->registerHandlers();
        }
        $this->channel_name = $channel_name;
        if(!isset(self::$packet_instance[$this->action_type][$channel_name])){
            $packet_decoder  = new PacketDecoder($channel_name,$schema_file);
            self::$packet_instance[$this->action_type][$channel_name] = $packet_decoder;
        }
        return self::$packet_instance[$this->action_type][$channel_name];
    }


}