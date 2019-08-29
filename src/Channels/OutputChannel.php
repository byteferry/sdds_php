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
use Sdds\Packet\PacketEncoder;

class OutputChannel extends ChannelAggregate
{
    public $action_type = ActionTypeContants::OUTPUT;

    public $channel_name;
    /**
     * @param $channel_name
     * @param $schema_file
     * @return mixed
     */
    public function getEncoder($channel_name,$schema_file){
        $this->channel_name = $channel_name;
        if(!isset(self::$packet_instance[$this->action_type][$channel_name])){
            self::$packet_instance[$this->action_type][$channel_name] = new PacketEncoder($channel_name,$schema_file);
        }
        return self::$packet_instance[$this->action_type][$channel_name];
    }
}