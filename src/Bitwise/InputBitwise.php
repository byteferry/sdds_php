<?php

/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\Bitwise;

/**
 * Class InputBitwise
 * @package Sdds\Bitwise
 */
class InputBitwise extends Bitwise
{

    /**
     * InputBitwise constructor.
     * @param $bin
     */
    protected function __construct($bin)
    {
        parent::__construct($bin);
    }

    /**
     * @param string $str_buffer
     * @return InputBitwise
     * @desc Initialise the object with string buffer.
     */
    public function ofString($str_buffer){
        $byte_array = str_split($str_buffer);
        $bin='';
        foreach ($byte_array as $character) {
            $bin .= sprintf('%08b', ord($character));
        }
        return new self($bin);
    }

    /**
     * @param array $byte_array
     * @return InputBitwise
     * @desc Initialise the object with byte array.
     */
    public function ofByte($byte_array){
        $bin='';
        if(!is_array($byte_array)){
            $byte_array = array($byte_array);
        }
        foreach ($byte_array as $num){
            $bin .= sprintf('%08b', $num);
        }
        return new self($bin);
    }

    /**
     * @param int $length
     * @return int
     * @desc Read the bits with given length.
     */
    public function read($length=1){
        if(1 == $length){
            return intval($this->bin_array[$this->position++]);
        }else{
            $int = bindec(implode('', array_reverse($this->bin_array)));
            $ret = $int & $this->getMask($length);
            $this->position += $length;
            return $ret;
        }
    }

    /**
     * @param int $length
     * @return int
     * @desc Read a integer value by given length.
     */
    public function readInt($length=1){
         return $this->read($length);
    }

    /**
     * @return int
     * @desc Read a bit.
     */
    public function readBit(){
        return $this->read(1);
    }

    /**
     * @param int $length
     * @return int
     * @desc Read bits by given length.
     */
    public function readBits($length=1){
        return $this->read($length);
    }

    /**
     * @param int $length
     * @return int
     * @desc skip the length given, and return 0.
     */
    public function readNull($length=1){
        $this->skip($length);
        return 0;
    }

    /**
     * @param int $length
     * @return int
     * @desc skip the length given, and return 0.
     */
    public function skipBits($length=1){
        $this->skip($length);
            return 0;
    }

    /**
     * @param $type_name
     * @param int $length
     * @return mixed
     * @desc Read the value by given type and length.
     */
    public function readByType($type_name,$length=0){

        if('skip'==$type_name){
            $this->skip($length);
            return 0;
        }

        $method = 'read' . ucfirst($type_name);
        if('bits'==$type_name){
            return $this->$method($length);
        }
        return $this->$method();

    }


}