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

/**
 * Class PacketEncoder
 * @package Sdds\Packet
 */
class PacketEncoder extends Packet
{

    /**
     * PacketEncoder constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param \Sdds\Schema\Schema $schema
     * @param \Sdds\Stream\Stream $stream
     */
    public function init($schema, $stream)
    {
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