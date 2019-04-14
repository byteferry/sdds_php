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
use Sdds\Exceptions\InvalidArgumentException;

/**
 * Class OutputBitwise
 * @package Sdds\Bitwise
 */
class OutputBitwise extends Bitwise
{

    /**
     * OutputBitwise constructor.
     * @param $bin
     */
    protected function __construct($bin)
    {
        parent::__construct($bin);
    }

    /**
     * @param $length
     * @return OutputBitwise
     * @desc Initialise the object by given length.
     */
    public function ofByte($length){
        $bin=str_repeat('0',$length);
        return new self($bin);
    }

    /**
     * @param $value
     * @param $length
     * @return $this
     * @desc Write by given value and length.
     */
    public function write($value,$length){
        if(0==$length){
            throw InvalidArgumentException::LengthCanNotBeZero();
        }
        for($i=0;$i<$length;$i++){
            $this->bin_array[$this->position++] = ($value & 1);
            $value = ($value >> 1);
        }
        return $this;
    }

    /**
     * @return number
     * @desc Return the integer value for stream writing.
     */
    public function getValue(){
        $length = count($this->bin_array);
        $patch_count = 0;
        if(0!=($length % 8)){
            $patch_count = 8 - ($length % 8);
        }
        $bin_array = $this->bin_array;
        if($patch_count >0){
            $bin_array = array_merge($this->bin_array,str_split(str_repeat('0',$patch_count)));
        }
        $count = count($bin_array)/8;
        $bin_array = array_reverse($bin_array);

        $chunk_array = array_chunk($bin_array,8);
        $bytes = [];
        for ($i=0;$i<$count;$i++){
            $bytes[] = bindec(implode("",$chunk_array[$i]));
        }

        return $bytes;
    }

    /**
     * @param $value
     * @return $this
     * @desc Write the given value that must be 0 or 1.
     */
    public function writeBit($value){
        $this->write($value,1);
        return $this;
    }

    /**
     * @param $value
     * @param $length
     * @return $this
     * @desc Write the given value by given length.
     */
    public function writeBits($value,$length){
        $this->write($value,$length);
        return $this;
    }

    /**
     * @param $type_name
     * @param $value
     * @param int $length
     * @return mixed
     * @desc Write the value by given type and length.
     */
    public function writeByType($type_name,$value,$length=0){
        if(null === $value){
            return $this->skip($length);
        }
        if('skip'==$type_name){
            return $this->skip($length);
        }
        $method = 'write' . ucfirst($type_name);
        if('bits'==$type_name){
            return $this->$method($value,$length);
        }
        return $this->$method($value);
    }

    /**
     * @param $value
     * @param int $length
     * @return OutputBitwise
     * @desc Write a integer value by given length.
     */
    public function writeInt($value,$length=0){
        if(0 == $length){
            for(;;){
                $this->bin_array[$this->position++] = ($value & 1);
                $value = ($value >> 1);
                if(0 == $value){
                    break;
                }
            }
            return $this;
        }
        return $this->write($value,$length);
    }

    /**
     * @param int $length
     * @return int
     * @desc Skip the writing by given length.
     */
    public function writeNull($length=1){
        $this->skip($length);
        return 0;
    }

    /**
     * @param int $length
     * @return int
     * @desc Skip the writing by given length.
     */
    public function skipBits($length=1){
        $this->skip($length);
        return 0;
    }

    /**
     * @param $type_name
     * @param $value
     * @param $position
     * @param int $length
     * @return mixed
     * @desc Write the value by given type, value, position and length. And after finish the position will reset to original location.
     */
    public function insertByType($type_name,$value, $position,$length=0){
        $offset = $this->offset();
        $this->position = $position;
        $result = $this->writeByType($type_name,$value,$length);
        $this->position = $offset;
        return $result;
    }

    /**
     * @param $type_name
     * @param $value
     * @param $position
     * @param int $length
     * @return mixed
     * @desc Replace the value by given type, value, position and length. And after finish the position will reset to original location.
     */
    public function replaceByType($type_name,$value, $position,$length=0){
        return $this->insertByType($type_name,$value, $position,$length);
    }

}