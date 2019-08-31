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

use Sdds\Constants\ActionTypeContants;
use Sdds\Constants\NodeTypeConstants as NodeType;
use Sdds\DataDiagram\InputNode;
use Sdds\Schema\InputSchema;
use Sdds\Stream\InputStream;
use Sdds\Dispatcher\Dispatcher;
use Sdds\Packet\PacketInterface;
/**
 * Class PacketDecoder
 * @package Sdds\Packet
 */
class PacketDecoder extends Packet
{
    public $action_type = ActionTypeContants::INPUT;
    /**
     * PacketDecoder constructor.
     * @param $channel_name
     * @param $schema_file
     */
    public function __construct($channel_name,$schema_file)
    {
        parent::__construct($channel_name);
        $inputNode = new InputNode($channel_name);
        $schema = new InputSchema($channel_name,$inputNode);
        $schema->init($schema_file);
        $stream = new InputStream($channel_name);
        parent::init($schema,$stream);

    }

    /**
     * @param $string_buffer
     * @throws \Sdds\Exceptions\SchemaException
     * @return mixed;
     */
    public function decode($string_buffer){
        $message = $this->Schema->factory(NodeType::OF_CUSTOM,$this->Schema->top_node_name);
        $message->_schema = $this->Schema;
        $message->_stream = $this->Stream;
        $data_array = $message->decode($string_buffer);
        return $data_array;
    }

}