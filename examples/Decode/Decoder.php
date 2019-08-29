<?php
/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\Examples;

use Sdds\Packet\PacketDecoder;

class Decode extends PacketDecoder
{
    /**
     * PacketDecoder constructor.
     * @param $schema_file
     */
    public function __construct($schema_file)
    {
        parent::__construct($schema_file);
    }

    /**
     * @param $string_buffer
     * @throws \Sdds\Exceptions\SchemaException
     * @return mixed;
     */
    public function decode($string_buffer){
        return parent::decode($string_buffer);
    }
}