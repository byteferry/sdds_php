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
 * Class PacketDecoder
 * @package Sdds\Packet
 */
class PacketDecoder extends Packet
{

    /**
     * PacketDecoder constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param \Sdds\Schema\InputSchema $schema
     * @param \Sdds\Stream\InputStream $stream
     */
    public function init($schema, $stream)
    {
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