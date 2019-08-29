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
use Sdds\Constants\NodeTypeConstants as NodeType;
use Sdds\DataDiagram\OutputNode;
use Sdds\Dispatcher\Dispatcher;
use Sdds\Schema\OutputSchema;
use Sdds\Stream\OutputStream;
use Sdds\Constants\ActionTypeContants;
/**
 * Class PacketEncoder
 * @package Sdds\Packet
 */
class PacketEncoder extends Packet
{
    public $action_type = ActionTypeContants::OUTPUT;
    /**
     * PacketEncoder constructor.
     * @param $schema_file
     */
    public function __construct($channel_name,$schema_file)
    {
        parent::__construct($channel_name);
        $inputNode = new OutputNode($channel_name);
        $schema = new OutputSchema($channel_name,$inputNode);
        $schema->init($schema_file);
        $stream = new OutputStream($channel_name);
        parent::init($schema,$stream);
    }

    /**
     * @param $data_array
     * @throws \Sdds\Exceptions\SchemaException
     * @return mixed: binary string buffer or false
     */
    public function encode($data_array){

        $message = $this->Schema->factory(NodeType::OF_CUSTOM,$this->Schema->top_node_name);
        $message->_schema = $this->Schema;
        $message->_stream = $this->Stream;
        $string_buffer = $message->encode($data_array);

        return $string_buffer;
    }

}