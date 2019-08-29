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

use Sdds\Packet\PacketEncoder;

class Encoder extends PacketEncoder
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
     * @param $data_array
     * @throws \Sdds\Exceptions\SchemaException
     * @return mixed;
     */
    public function encode($data_array){
        return parent::encode($data_array);
    }
}