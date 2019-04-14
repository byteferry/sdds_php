<?php

/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\Stream;

use Sdds\Exceptions\InvalidArgumentException;


/**
 * Methods of primitive types
 *
 * @method mixed writeByte() Alias of method writeInt8()
 * @method mixed writeChar() Alias of method writeInt8()
 * @method mixed writeShort() Alias of method writeInt16()
 * @method mixed writeWord() Alias of method writeInt16()
 * @method mixed writeInt() Alias of method writeInt16()
 * @method mixed writeDword() Alias of method writeInt32()
 * @method mixed writeLong() Alias of method writeInt64()
 * @method mixed insertByte() Alias of method insertInt8()
 * @method mixed insertChar() Alias of method insertInt8()
 * @method mixed insertShort() Alias of method insertInt16()
 * @method mixed insertWord() Alias of method insertInt16()
 * @method mixed insertInt() Alias of method insertInt32()
 * @method mixed insertDword() Alias of method insertInt32()
 * @method mixed insertLong() Alias of method insertInt64()
 * @method mixed replaceByte() Alias of method replaceInt8()
 * @method mixed replaceChar() Alias of method replaceInt8()
 * @method mixed replaceShort() Alias of method replaceInt16()
 * @method mixed replaceWord() Alias of method replaceInt16()
 * @method mixed replaceInt() Alias of method replaceInt32()
 * @method mixed replaceDword() Alias of method replaceInt32()
 * @method mixed replaceLong() Alias of method replaceInt64()
 * @method mixed writeUByte() Alias of method writeUInt8()
 * @method mixed writeUChar() Alias of method writeUInt8()
 * @method mixed writeUShort() Alias of method writeUInt16()
 * @method mixed writeUWord() Alias of method writeUInt16()
 * @method mixed writeUInt() Alias of method writeUInt16()
 * @method mixed writeUDword() Alias of method writeUInt32()
 * @method mixed writeULong() Alias of method writeUInt64()
 * @method mixed insertUByte() Alias of method insertUInt8()
 * @method mixed insertUChar() Alias of method insertUInt8()
 * @method mixed insertUShort() Alias of method insertUInt16()
 * @method mixed insertUWord() Alias of method insertUInt16()
 * @method mixed insertUInt() Alias of method insertUInt32()
 * @method mixed insertUDword() Alias of method insertUInt32()
 * @method mixed insertULong() Alias of method insertUInt64()
 * @method mixed replaceUByte() Alias of method replaceUInt8()
 * @method mixed replaceUChar() Alias of method replaceUInt8()
 * @method mixed replaceUShort() Alias of method replaceUInt16()
 * @method mixed replaceUWord() Alias of method replaceUInt16()
 * @method mixed replaceUInt() Alias of method replaceInt32()
 * @method mixed replaceUDword() Alias of method replaceUInt32()
 * @method mixed replaceULong() Alias of method replaceUInt64()
 * @method mixed skipChar() Alias of method replaceInt8()
 * @method mixed skipByte() Alias of method skipInt8()
 * @method mixed skipInt8() Alias of method skipInt8()
 * @method mixed skipShort() Alias of method skipInt8()
 * @method mixed skipWord() Alias of method skipInt16()
 * @method mixed skipInt16() Alias of method skipInt16()
 * @method mixed skipInt() Alias of method skipInt32()
 * @method mixed skipInt32() Alias of method skipInt32()
 * @method mixed skipDword() Alias of method skipInt32()
 * @method mixed skipLong() Alias of method skipInt64()
 * @method mixed skipInt64() Alias of method skipInt64()
 *
 */

/**
 * Class OutputStream
 * @package Sdds\Stream
 */
class OutputStream extends Stream
{
    /**
     * @var int $length, the length of the output stream.
     */
    protected $length;

    /**
     * Stream constructor.
     */
    public  function __construct( )
    {
        parent::__construct();
    }

    /**
     * Returns signed 8-bit integer as binary data.
     *
     * @param integer $value The input value.
     * @return string
     */
    private function _toInt8($value) {
        return pack("c", $value) ;
    }

    /**
     * Returns unsigned 8-bit integer as binary data.
     *
     * @param integer $value The input value.
     * @return string
     */
    private function _toUint8($value) {
        return pack("C", $value);
    }

    /**
     * Returns signed 16-bit integer as machine endian ordered binary data.
     *
     * @param integer $value The input value.
     * @return string
     */
    private function _toInt16($value)
    {
        return pack('s*', $value);
    }

    /**
     * Returns unsigned 16-bit integer as machine endian ordered binary data.
     *
     * @param integer $value The input value.
     * @param int $endian The byte order of the binary data string.
     * @return string
     */
    private function _toUint16($value,$endian=null)
    {
        if(null === $endian){
            $endian = $this->options['endianness'];
        }
        $unpack_cmd =['S','v','n'];
        return  pack($unpack_cmd[$endian].'*', $value);
    }

    /**
     * Returns signed 24-bit integer as machine endian ordered binary data.
     *
     * @param integer $value The input value.
     * @return string
     */
    private function _toInt24($value)
    {
        $string_buffer = $this->_toInt32($value);
        if ($this->_isLittleEndian()){
            $string_buffer = substr($string_buffer,0,3);
        }else{
            $string_buffer = substr($string_buffer,1,3);
        }
        return $string_buffer;
    }

    /**
     * Returns unsigned 24-bit integer as machine endian ordered binary data.
     *
     * @param integer $value The input value.
     * @param int $endian The byte order of the binary data string.
     * @return string
     */
    private function _toUint24($value,$endian=null)
    {
        $string_buffer = $this->_toUint32($value,$endian);
        if (self::LITTLE_ENDIAN == $endian){
            $string_buffer = substr($string_buffer,0,3);
        }else{
            $string_buffer = substr($string_buffer,1,3);
        }
        return $string_buffer;
    }

    /**
     * Returns signed 32-bit integer as machine endian ordered binary data.
     *
     * @param integer $value The input value.
     * @return string
     */
    private function _toInt32($value)
    {
        return pack('l*', $value);
    }

    /**
     * Returns unsigned 32-bit integer as machine endian ordered binary data.
     *
     * @param integer $value The input value.
     * @param int $endian The byte order of the binary data string.
     * @return string
     */
    private function _toUint32($value,$endian = null)
    {
        if(null === $endian){
            $endian = $this->options['endianness'];
        }
        $unpack_cmd =['L','V','N'];
        return  pack($unpack_cmd[$endian].'*', $value);
    }

    /**
     * Returns signed 64-bit integer as machine endian ordered binary data.
     *
     * @param integer $value The input value.
     * @param integer $endian The value of endianness.
     * @return string
     */
    private function _toInt64($value,$endian=null)
    {
        if(null === $endian){
            $endian = $this->options['endianness'];
        }
        if(PHP_INT_SIZE<8){
            if(self::BIG_ENDIAN == $endian){
                return pack('N*', $value / (0xffffffff+1), $value & 0xffffffff);
            }else{
                return pack('V*', $value / (0xffffffff+1), $value & 0xffffffff);
            }
        }else{
            return pack('q', $value);
        }

    }

    /**
     * Returns unsigned 64-bit integer as machine endian ordered binary data.
     *
     * @param integer $value The input value.
     * @param int $endian The byte order of the binary data string.
     * @return string
     */
    private function _toUint64($value,$endian = null)
    {
        if(null === $endian){
            $endian = $this->options['endianness'];
        }
        if(PHP_INT_SIZE<8){
            $lo = $value & 0xffffffff;
            $hi = $value /(0xffffffff+1);
            $hiValue = $this->_toUint32($hi,$endian);
            $loValue = $this->_toUint32($lo,$endian);
            return  [$hiValue,$loValue];
        }
        $unpack_cmd =['Q','P','J'];
        return  pack($unpack_cmd[$endian].'*', $value);
    }

    /**
     * Returns a 32-bit floating point number as machine endian ordered binary data.
     *
     * @param float $value The input value.
     * @param int $endian The byte order of the binary data string.
     * @return string
     */
    private function _toFloat32($value,$endian = null)
    {
        if(null === $endian){
            $endian = $this->options['endianness'];
        }
        $unpack_cmd =['f','g','G'];
        return  pack($unpack_cmd[$endian].'*', $value);
    }

    /**
     * Returns a 64-bit floating point number as machine endian ordered binary data.
     *
     * @param float $value The input value.
     * @param int $endian The byte order of the binary data string.
     * @return string
     */
    private function _toFloat64($value,$endian = null)
    {
        if(null === $endian){
            $endian = $this->options['endianness'];
        }
        $unpack_cmd =['d','e','E'];
        return  pack($unpack_cmd[$endian].'*', $value);
    }

    /**
     * Write data to stream.
     * @param string $data
     * @param int $length
     * @return int
     */
    public function write($data, $length = null)
    {
        $this->_checkStreamHandle();
        if (null === $length) {
            return fwrite($this->_stream_handle, $data);
        } else {
            return fwrite($this->_stream_handle, $data, $length);
        }
    }

    /**
     * Write array bytes
     * @param array $bytes
     * @return int
     */
    public function writeBytes($bytes)
    {
        array_unshift($bytes, 'C*');
        return $this->write(call_user_func_array('pack', $bytes));
    }

    /**
     * @param $value
     * @return int
     */
    public function writeBool($value)
    {
        return $this->writeInt8($value ? 1 : 0);
    }

    /**
     * write a Binary Coded Decimals(8421code).
     * Note: There is No sign code in the string!
     * @param $value
     * @return int
     */
    public function writeBcd8421($value){

        $len = strlen($value);
        //If is odd, patch 0 as the first char
        if (($len & 0x1) == 1) {
            $value = str_split( "0" . $value);
        }else{
        	$value = str_split( "" . $value);
        }
        $bytes=[];
        for ($i = 0; $i < $len; $i+=2) {
            $high = $value[$i];
            $low = $value[$i+1];
            $bytes[] =  ($high << 4) | $low;
        }
        return $this->writeBytes($bytes);

    }

    /**
     * @param $length
     * @return int
     */
    public function writeNull($length)
    {
        return $this->write(pack('x' . $length));
    }

    /**
     * @param string $str_buffer
     * @return int
     */
    public function writeLine($str_buffer){

        return $this->write($str_buffer . '\n');
    }

    /**
     * Writes hexadecimal string having high nibble first as binary data to the
     * stream.
     *
     * @param string $value The input value.
     * @return int
     */
    public function writeHHex($value)
    {
        return $this->write(pack('H*', $value));
    }

    /**
     * Writes hexadecimal string having low nibble first as binary data to the
     * stream.
     *
     * @param string $value The input value.
     * @return int
     */
    public function writeLHex($value)
    {
        return $this->write(pack('h*', $value));
    }

    /**
     * Write string data
     * @param string $value
     * @param int $length
     * @return int
     */
    public function writeString($value, $length = 0)
    {
        if ($length!=0){
            if (strlen($value)<$length){
                //add blank space to the string
                $value .= str_repeat("\x20",$length-strlen($value));
            }
        }
        return $this->write($value);
    }

    /**
     * Writes an 8-bit integer as binary data to the stream.
     *
     * @param $value
     * @return int
     */
    public function writeInt8($value){
        return $this->write($this->_toInt8($value));
    }

    /**
     *  Writes an 8-bit unsigned integer as binary data to the stream.
     *
     * @param $value
     * @return int
     */
    public function writeUint8($value){
        return $this->write($this->_toUint8($value));
    }

    /**
     * Returns signed 16-bit integer as machine-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return int
     */
    public function writeInt16($value)
    {
        return $this->write($this->_toInt16($value));
    }

    /**
     * Returns signed 16-bit integer as big-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return int
     */
    public function writeInt16BE($value){
        if ($this->_isLittleEndian()) {
            return $this->write(strrev($this->_toInt16($value)));
        } else {
            return $this->write($this->_toInt16($value));
        }
    }

    /**
     * Writes a signed 16-bit integer as little-endian ordered binary data to
     * the stream.
     *
     * @param integer $value The input value.
     * @return int
     */
    public function writeInt16LE($value){
        if ($this->_isBigEndian()) {
            return $this->write(strrev($this->_toInt16($value)));
        } else {
            return $this->write($this->_toInt16($value));
        }
    }

    /**
     * Returns unsigned 16-bit integer as machine-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return int
     */
    public function writeUint16($value)
    {
        return $this->write($this->_toUInt16($value));
    }

    /**
     * Returns unsigned 16-bit integer as big-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return int
     */
    public function writeUint16BE($value){
        return $this->write($this->_toUInt16($value,self::BIG_ENDIAN));
    }

    /**
     * Returns unsigned 16-bit integer as little-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return int
     */
    public function writeUint16LE($value){
        return $this->write($this->_toUInt16($value,self::LITTLE_ENDIAN));
    }

    /**
     * Writes signed 32-bit integer as machine-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeInt($value){
        return $this->writeInt32($value);
    }

    /**
     * writes 4 bytes from the stream and returns machine ordered binary data
     * as unsigned 32-bit integer.
     *
     * @return integer
     */
    public function writeUint(){
        return $this->writeUint32();
    }

    /**
     * Writes signed 32-bit integer as machine-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeInt32($value){
        return $this->write($this->_toInt32($value));
    }

    /**
     * writes 4 bytes from the stream and returns machine ordered binary data
     * as unsigned 32-bit integer.
     *
     * @return integer
     */
    public function writeUint32(){
        return $this->_toUint32($this->write(4));
    }

    /**
     * Writes signed 32-bit integer as big-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeInt32BE($value){
        if ($this->_isLittleEndian()) {
            return $this->write(strrev($this->_toInt32($value)));
        } else {
            return $this->write($this->_toInt32($value));
        }
    }

    /**
     * Writes signed 32-bit integer as little-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeInt32LE($value){
        if ($this->_isBigEndian()) {
            return $this->write(strrev($this->_toInt32($value)));
        } else {
            return $this->write($this->_toInt32($value));
        }
    }

    /**
     * Writes unsigned 32-bit integer as big-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeUint32BE($value){
        return $this->write($this->_toUInt32($value,self::BIG_ENDIAN));
    }

    /**
     * Writes unsigned 32-bit integer as little-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeUint32LE($value){
        return $this->write($this->_toUInt32($value,self::LITTLE_ENDIAN));
    }

    /**
     * Writes signed 24-bit integer as machine-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeInt24($value){
        return $this->write($this->_toInt24($value));
    }

    /**
     * Writes unsigned 24-bit integer as machine-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeUint24($value){
        return $this->write($this->_toUint24($value,self::MACHINE_ENDIAN));
    }

    /**
     * Writes signed 24-bit integer as big-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeInt24BE($value){
        if ($this->_isLittleEndian()) {
            return $this->write(strrev($this->_toInt24($value)));
        } else {
            return $this->write($this->_toInt24($value));
        }
    }

    /**
     * Writes signed 24-bit integer as little-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeInt24LE($value){
        if ($this->_isBigEndian()) {
            return $this->write(strrev($this->_toInt24($value)));
        } else {
            return $this->write($this->_toInt24($value));
        }
    }

    /**
     * Writes unsigned 24-bit integer as big-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeUint24BE($value){
        return $this->write($this->_toUInt24($value,self::BIG_ENDIAN));
    }

    /**
     * Writes unsigned 24-bit integer as little-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeUint24LE($value){
        return $this->write($this->_toUInt24($value,self::LITTLE_ENDIAN));
    }

    /**
     * Writes signed 64-bit integer as machine-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeInt64($value){
        if($this->sysIsBigEndian()){
            return $this->writeInt64BE($value);
        }else{
            return $this->writeInt64LE($value);
        }
    }

    /**
     * Writes unsigned 64-bit integer as system-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeUint64($value){
        if($this->sysIsBigEndian()){
            return $this->writeUInt64BE($value);
        }else{
            return $this->writeUInt64LE($value);
        }
    }

    /**
     * Writes signed 64-bit integer as big-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeInt64BE($value){
        return $this->write($this->_toInt64($value,self::BIG_ENDIAN));
    }

    /**
     * Writes signed 64-bit integer as little-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeInt64LE($value){
         return $this->write($this->_toInt64($value,self::LITTLE_ENDIAN));
    }

    /**
     * Writes unsigned 64-bit integer as big-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeUint64BE($value){
        $buffer = $this->_toUint64($value,self::BIG_ENDIAN);
        if(is_array($buffer)){
            $this->write($buffer[0]);
            return $this->write($buffer[1]);
        }
        return $this->write($this->_toUint64($value,self::BIG_ENDIAN));
    }

    /**
     * Writes unsigned 64-bit integer as little-endian ordered binary data to the
     * stream.
     *
     * @param integer $value The input value.
     * @return integer
     */
    public function writeUint64LE($value){
        $buffer = $this->_toUint64($value,self::LITTLE_ENDIAN);
        if(is_array($buffer)){
            $this->write($buffer[1]);
            return $this->write($buffer[0]);
        }
        return $this->write($this->_toUint64($value,self::LITTLE_ENDIAN));
    }

    /**
     * Writes a 32-bit floating point number as system-endian ordered binary data to the
     * stream.
     *
     * @param float $value The input value.
     * @return integer
     */
    public function writeFloat32($value)
    {
        if ($this->sysIsBigEndian()) {
            return $this->writeFloat32BE($value);
        } else {
            return $this->writeFloat32LE($value);
        }
    }

    /**
     * Writes a 32-bit floating point number as little-endian ordered binary data to the
     * stream.
     *
     * @param float $value The input value.
     * @return integer
     */
    public function writeFloat32LE($value)
    {
        if ($this->_isBigEndian()) {
            return $this->write(strrev($this->_toFloat32($value)));
        } else {
            return $this->write($this->_toFloat32($value));
        }
    }

    /**
     * Writes a 32-bit floating point number as big-endian ordered binary data to the
     * stream.
     *
     * @param float $value The input value.
     * @return integer
     */
    public function writeFloat32BE($value)
    {
        if ($this->_isLittleEndian()) {
            return $this->write(strrev($this->_toFloat32($value)));
        } else {
            return $this->write($this->_toFloat32($value));
        }
    }

    /**
     * Writes a 32-bit floating point number as little-endian ordered binary data to the
     * stream.
     *
     * @param float $value The input value.
     * @return integer
     */
    public function writeFloatLE($value)
    {
        return $this->writeFloat32LE($value);
    }

    /**
     * Writes a 32-bit floating point number as big-endian ordered binary data to the
     * stream.
     *
     * @param float $value The input value.
     * @return integer
     */
    public function writeFloatBE($value)
    {
        return $this->writeFloat32BE($value);
    }

    /**
     * Writes a 32-bit floating point number as little-endian ordered binary data to the
     * stream.
     *
     * @param float $value The input value.
     * @return integer
     */
    public function writeSingleLE($value)
    {
        return $this->writeFloat32LE($value);
    }

    /**
     * Writes a 32-bit floating point number as big-endian ordered binary data to the
     * stream.
     *
     * @param float $value The input value.
     * @return integer
     */
    public function writeSingleBE($value)
    {
        return $this->writeFloat32BE($value);
    }


    /**
     * Writes a 64-bit floating point number as system-endian ordered binary data to the
     * stream.
     *
     * @param float $value The input value.
     * @return integer
     */
    public function writeFloat64($value)
    {
        if ($this->sysIsBigEndian()) {
            return $this->writeFloat64BE($value);
        } else {
            return $this->writeFloat64LE($value);
        }
    }

    /**
     * Writes a 64-bit floating point number as little-endian ordered binary data to the
     * stream.
     *
     * @param float $value The input value.
     * @return integer
     */
    public function writeFloat64LE($value)
    {
        if ($this->_isBigEndian()) {
            return $this->write(strrev($this->_toFloat64($value)));
        } else {
            return $this->write($this->_toFloat64($value));
        }
    }

    /**
     * Writes a 64-bit floating point number as big-endian ordered binary data to the
     * stream.
     *
     * @param float $value The input value.
     * @return integer
     */
    public function writeFloat64BE($value)
    {
        if ($this->_isLittleEndian()) {
            return $this->write(strrev($this->_toFloat64($value)));
        } else {
            return $this->write($this->_toFloat64($value));
        }
    }

    /**
     * Writes a 64-bit floating point number as little-endian ordered binary data to the
     * stream.
     *
     * @param float $value The input value.
     * @return integer
     */
    public function writeDoubleLE($value)
    {
        return $this->writeFloat64LE($value);
    }

    /**
     * Writes a 64-bit floating point number as big-endian ordered binary data to the
     * stream.
     *
     * @param float $value The input value.
     * @return integer
     */
    public function writeDoubleBE($value)
    {
        return $this->writeFloat64BE($value);
    }


    /**
     * @return void
     */
    public function __destruct()
    {
        parent::close();
    }

    /**
     * @param $type_name
     * @param $value
     * @param $unsigned
     * @param int $length
     * @return int
     */
    public function writeByType($type_name,$value,$unsigned,$length=0){
        if(null === $value){
            return $this->writeNull($length);
        }
        $param_length = null;
        $suffix = '';
        $write_length = $this->getTypeLength($type_name);
        if(0 == $write_length){
            throw InvalidArgumentException::LengthCanNotBeZero();
        }
        if($write_length>1){
            if(true == $this->sysIsBigEndian()){
                $suffix = 'BE';
            }else{
                $suffix = 'LE';
            }
        }
        if($type_name != rtrim($type_name,'LBE')){
            $suffix = '';
        }
        if (-1 == $write_length){
            $param_length = $length;
            $suffix = '';
        }
        if('skip'==$type_name){
            return $this->skip($param_length);
        }
        $write_type = $type_name;
        if(isset($this->type_alias[$type_name])){
            $write_type = $this->type_alias[$type_name];
        }
        $sign = '';
        if(true === $unsigned){
            $sign = 'U';
        }
        $method = 'write' .$sign . ucfirst($write_type) . $suffix;
        if(null != $param_length){
            return $this->$method($value,$param_length);
        }
        if (-1 == $this->length_map[$type_name]){
            return $this->$method($value,$param_length);
        }
        return $this->$method($value);
    }

    /**
     * @param $type_name
     * @param $value
     * @param $position
     * @param $unsigned
     * @param int $length
     * @return int
     */
    public function insertByType($type_name,$value,$position,$unsigned,$length=0){
        $offset = $this->offset();
        $this->seek($position+$length,SEEK_SET);
        $buffer = $this->read(-1);
        if (0 == $length){
            $length = $this->length_map[$type_name];
            if (-1==$length){
                throw InvalidArgumentException::LengthCanNotBeZero();
            }
        }
        $this->seek($position,SEEK_SET);
        $result = $this->writeByType($type_name,$value,$unsigned,$length);
        if(strlen($buffer)>0){
            $this->seek($position+$length,SEEK_SET);
            $this->write($buffer);
        }
        $this->seek($offset,SEEK_SET);
        return $result;
    }

    /**
     * @param $type_name
     * @param $value
     * @param $position
     * @param $unsigned
     * @param int $length
     * @return int
     */
    public function replaceByType($type_name,$value,$position,$unsigned,$length=0){
        return $this->insertByType($type_name,$value,$position,$unsigned,$length);
    }

    /**
     * @return string
     */
    public function getContents(){
        $this->seek(0,SEEK_SET);
        $stream_buffer = stream_get_contents($this->_stream_handle);
        return $stream_buffer;
    }

    /**
     * @return string
     */
    public function toBuffer(){
        return $this->getContents();
    }

    /**
     * Override the function size().
     * @return int
     */
    public function size(){
        if ($this->length != 0){
            return $this->length;
        }else{
            return parent::size();
        }
    }

}