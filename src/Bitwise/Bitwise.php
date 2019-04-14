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

use Sdds\Exceptions\RuntimeException;
use ArrayAccess;
use Iterator;
use Countable;

/**
 * Class Bitwise
 * @desc A package for reading and writing of bits. With this package, you need not use bit operator of php. And you can more easily process bit data.
 * @package Sdds\Bitwise
 */
class Bitwise implements ArrayAccess,Iterator,Countable
{

    /**
     * @var array $bin_array
     * @desc keep the binary in the array.
     */
    protected $bin_array=[];

    /**
     * @var int $position
     * @desc Keep the current position of reading or writing.
     */
    protected $position;


    /**
     * Bitwise constructor.
     * @param $bin
     */
    protected function __construct($bin)
    {
        $this->bin_array = array_reverse(str_split($bin)) ;
        $this->position = 0;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return \Sdds\Bitwise\Bitwise
     */
    public function offsetSet($offset, $value) {
        if (is_subclass_of($this,"InputBitwise")){
            if (is_null($offset)) {
                $this->bin_array[] = $value;
            } else {
                $this->bin_array[$offset] = $value;
            }
        }else{
            Throw RuntimeException::ObjectIsReadOnly();
        }
        return $this;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->bin_array[$offset]);
    }

     /**
     * @param mixed $offset
     * @return \Sdds\Bitwise\Bitwise
     */
    public function offsetUnset($offset) {
        if (is_subclass_of($this,"InputBitwise")){
            unset($this->bin_array[$offset]);
        }else{
            Throw RuntimeException::ObjectIsReadOnly();
        }
        return $this;
    }

    /**
     * @param mixed $offset
     * @return null
     */
    public function offsetGet($offset) {
        return isset($this->bin_array[$offset]) ? $this->bin_array[$offset] : null;
    }

    /**
     * @return int
     */
    public function count(){
        return count($this->bin_array);
    }

    /**
     * @return \Sdds\Bitwise\Bitwise
     */
    function rewind() {
        $this->position = 0;
        return $this;
    }

    /**
     * @return mixed
     */
    function current() {
        return $this->bin_array[$this->position];
    }

    /**
     * @return int
     */
    function key() {
        return $this->position;
    }

    /**
     * @return \Sdds\Bitwise\Bitwise
     */
    function next() {
        ++$this->position;
        return $this;
    }

    /**
     * @return bool
     */
    function valid() {
        return isset($this->bin_array[$this->position]);
    }

    /**
     * @param int $length
     * @return \Sdds\Bitwise\Bitwise
     */
    public function skip($length=1){
        $this->position+=$length;
        return $this;
    }

    /**
     * @return int
     */
    public function offset(){
        return $this->position;
    }

    /**
     * @param $pos
     * @return $this
     */
    public function seek($pos){
        $this->position = $pos;
        return $this;
    }

    /**
     * @param $length
     * @return number
     * @desc Get the binary mask such as "1111" by given length.
     */
    protected function getMask($length){
        if($this->position + $length > count($this->bin_array)){
            throw RuntimeException::DataIsEndOfTheBits();
        }
        $bin = str_repeat('0',$this->position);
        $bin  .= str_repeat('1',$length);
        if(count($this->bin_array)-strlen($bin)>0){
            $bin .= str_repeat('0',count($this->bin_array)-strlen($bin));
        }
        return bindec(strrev($bin));
    }

    /**
     * @param $action
     * @param $type
     * @param array $args
     * @return mixed
     * @desc Call the target method via argument.
     */
    public function callByType($action,$type='',$args=[]){

		$method = $action. ucfirst($type);
		if(method_exists(get_called_class(),$method)){
            $this->$method(...$args);
        }else{
            throw RuntimeException::MethodNotExists($method);
        }

    }

    /**
     * @return string
     * @desc Return the binary string.
     */
    public function __toString()
    {
        return implode('',array_reverse($this->bin_array));
    }

}