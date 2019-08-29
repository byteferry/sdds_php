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

use Sdds\Dispatcher\Dispatcher;
use Sdds\Exceptions\InvalidArgumentException;

/**
 * Methods of primitive types
 *
 * @method mixed readByte()	    Alias of method readInt8()
 * @method mixed readChar()	    Alias of method readInt8()
 * @method mixed readShort()	Alias of method readInt16()
 * @method mixed readShortBE()	Alias of method readInt16BE()
 * @method mixed readShortLE()	Alias of method readInt16LE()
 * @method mixed readWord()	    Alias of method readInt16()
 * @method mixed readWordBE()	Alias of method readInt16BE()
 * @method mixed readWordLE()	Alias of method readInt16LE()
 * @method mixed readDWord()	Alias of method readInt32()
 * @method mixed readDWordBE()	Alias of method readInt32BE()
 * @method mixed readDWordLE()	Alias of method readInt32LE()
 * @method mixed readLong()	    Alias of method readInt64()
 * @method mixed readLongBE()	Alias of method readInt64BE()
 * @method mixed readLongLE()	Alias of method readInt64LE()
 * @method mixed readUShort()	Alias of method readUInt16()
 * @method mixed readUShortBE()	Alias of method readUInt16BE()
 * @method mixed readUShortLE()	Alias of method readUInt16LE()
 * @method mixed readUByte()	Alias of method readUInt8()
 * @method mixed readUChar()	Alias of method readUInt8()
 * @method mixed readUWord()	Alias of method readUInt16()
 * @method mixed readUWordBE()	Alias of method readUInt16BE()
 * @method mixed readUWordLE()	Alias of method readUInt16LE()
 * @method mixed readUDWord()	Alias of method readUInt32()
 * @method mixed readUDWordBE()	Alias of method readUInt32BE()
 * @method mixed readUDWordLE()	Alias of method readUInt32LE()
 * @method mixed readULong()	Alias of method readUInt64()
 * @method mixed readULongBE()	Alias of method readUInt64BE()
 * @method mixed readULongLE()	Alias of method readUInt64LE()
 * @method mixed skipChar()     Alias of method skipInt8()
 * @method mixed skipByte()     Alias of method skipInt8()
 * @method mixed skipShort()    Alias of method skipInt8()
 * @method mixed skipWord()     Alias of method skipInt16()
 * @method mixed skipInt()      Alias of method skipInt32()
 * @method mixed skipDword()    Alias of method skipInt32()
 * @method mixed skipLong()     Alias of method skipInt64()
 * @method mixed skipFloat()    Alias of method skipFloat32()
 * @method mixed skipSingle()   Alias of method skipFloat32()
 * @method mixed skipDouble()   Alias of method skipFloat64()
 *
 */


/**
 * Class StreamInput
 * @package Sdds\Stream
 */

class InputStream extends Stream
{

    /**
     * @var string
     */
    public $action_type = Dispatcher::INPUT;

    /**
     * InputStream constructor.
     * @param $channel_name
     */
    public  function __construct($channel_name)
    {
        parent::__construct($channel_name);
    }

    /**
     * Returns binary data as 8-bit integer.
     *
     * @param string $value The binary data string.
     * @return integer
     */
    private function _fromInt8($value) {
        return unpack("c", $value)[1];
    }

    /**
     * Returns binary data as unsigned 8-bit integer.
     *
     * @param string $value The binary data string.
     * @return integer
     */
    private function _fromUint8($value) {
        return unpack("C", $value)[1];
    }

    /**
     * Returns machine endian ordered binary data as signed 16-bit integer.
     *
     * @param string $value The binary data string.
     * @return integer
     */
    private function _fromInt16($value){
        return  unpack('s*', $value)[1];
    }

    /**
     * Returns target endian ordered binary data as unsigned 16-bit integer.
     *
     * @param string  $value The binary data string.
     * @param int $endian The byte order of the binary data string.
     * @return integer
     */
    private function _fromUInt16($value,$endian = null)
    {
        if(null == $endian){
            $endian = $this->options['endianness'];
        }
        $unpack_cmd =['S','v','n'];
        return  unpack($unpack_cmd[$endian].'*', $value)[1];
    }

    /**
     * Returns machine endian ordered binary data as signed 24-bit integer.
     *
     * @param string $value The binary data string.
     * @return integer
     */
    private function _fromInt24($value)    {

        return unpack('l*', $this->_isLittleEndian() ? ("\x00" . $value) : ($value . "\x00"))[1];
    }

    /**
     * Returns machine endian ordered binary data as unsigned 24-bit integer.
     *
     * @param string  $value The binary data string.
     * @param int $endian The byte order of the binary data string.
     * @return integer
     */
    private function _fromUInt24($value,$endian = null)
    {
        if(null == $endian){
            $endian = $this->options['endianness'];
        }
        if(self::LITTLE_ENDIAN == $endian){
            $value = array_reverse($value);
        }
        return ($value[0] <<16) | ($value[1] <<8) | $value[2];
    }

    /**
     * Returns machine-endian ordered binary data as signed 32-bit integer.
     *
     * @param string $value The binary data string.
     * @return integer
     */
    private function _fromInt32($value)
    {
        return unpack('l*', $value)[1];
    }

    /**
     * Returns machine endian ordered binary data as unsigned 32-bit integer.
     *
     * @param string  $value The binary data string.
     * @param int $endian The byte order of the binary data string.
     * @return integer
     */
    private function _fromUint32($value,$endian = null)
    {
        $cmd=[['L','v','n'],['L','V','N']];
        if(null == $endian){
            $endian = $this->options['endianness'];
        }
        if (PHP_INT_SIZE < 8) {
            list(, $hi, $lo) = unpack($cmd[0][$endian].'*', $value);
            return $hi * (0xffff+1) + $lo; // eq $hi << 16 | $lo
        } else {
            list(, $int) = unpack($cmd[1][$endian].'*', $value);
            return $int;
        }
    }

    /**
     * Returns machine endian ordered binary data as unsigned 64-bit integer.
     *
     * @param string  $value The binary data string.
     * @param int $endian The byte order of the binary data string.
     * @return integer
     */
    private function _fromUint64($value,$endian = null)
    {
        if(null == $endian){
            $endian = $this->options['endianness'];
        }
        if(PHP_INT_SIZE<8){
            $param = ($endian == self::BIG_ENDIAN)?1:-1;
            $hi = substr($value,0,4*$param);
            $lo = substr($value,0,-4*$param);
            $hiInt = $this->_fromUint32($hi,$endian);
            $loInt = $this->_fromUint32($lo,$endian);
            return $hiInt * (0xffffffff+1) + $loInt;
        }
        $unpack_cmd =['Q','P','J'];
        return  unpack($unpack_cmd[$endian].'*', $value)[1];
    }

    /**
     * Returns machine endian ordered binary data as a 32-bit floating point
     * number as defined by IEEE 754.
     *
     * @param string $value The binary data string.
     * @param int $endian The byte order of the binary data string.
     * @return float
     */
    private function _fromFloat32($value,$endian = null)
    {
        if(null == $endian){
            $endian = $this->options['endianness'];
        }
        $unpack_cmd =['f','g','G'];
        return  unpack($unpack_cmd[$endian].'*', $value)[1];
    }

    /**
     * Returns machine endian ordered binary data as a 64-bit floating point
     * number as defined by IEEE754.
     *
     * @param string $value The binary data string.
     * @param int $endian The byte order of the binary data string.
     * @return float
     */
    private function _fromFloat64($value,$endian = null)
    {
        if(null == $endian){
            $endian = $this->options['endianness'];
        }
        $unpack_cmd =['d','e','E'];
        return  unpack($unpack_cmd[$endian].'*', $value)[1];
    }

    /**
     * Reads <var>length</var> amount of bytes from the stream and returns
     * binary data as hexadecimal string having high nibble first.
     *
     * @param integer $length The amount of bytes.
     * @return string
     */
    public function readHHex($length)
    {
        list($hex) = unpack('H*0', $this->read($length));
        return $hex;
    }

    /**
     * Reads <var>length</var> amount of bytes from the stream and returns
     * binary data as hexadecimal string having low nibble first.
     *
     * @param integer $length The amount of bytes.
     * @return string
     */
    public function readLHex($length)
    {
        list($hex) = unpack('h*0', $this->read($length));
        return $hex;
    }

    /**
     * Reads $length bytes from an input stream.
     * @param $length
     * @return array|false
     */
    public function readBytes($length)
    {
        $bytes = $this->read($length);
        if (false !== $bytes) {
            return array_values(unpack('C*', $bytes));
        }
        return false;
    }

    /**
     * Read one line from the stream.
     * @param int $length Maximum number of bytes to read.
     * @param string $ending Line ending to stop at. Defaults to "\n".
     * @return string The data read from the stream
     */
    public function readLine($length = null, $ending = "\n")
    {
        if ($length === null) {
            $length = $this->size();
        }
        return stream_get_line($this->_stream_handle, $length, $ending);
    }

    /**
     * Read bytes as string
     * @param int $length
     * @return string
     */
    public function readString($length)
    {
        $str = $this->read($length);
        return trim($str);
    }

    /**
     * @return int
     */
    public function readBool()
    {
        return (0 != $this->readInt8());
    }

    /**
     * Read a Binary Coded Decimals(8421code)
     * Note: There is No sign code in the string!
     * @param int $length
     * @return string
     */
    public function readBcd8421($length){

        $bytes = $this->readBytes($length);
        $bcd_string = '';
        for ($i = 0,$j = count($bytes); $i < $j; $i++) {
             $bcd_string .= ($bytes[$i] & 0xf0) >> 4;
             $bcd_string .= $bytes[$i] & 0x0f;
        }
        return $bcd_string;

    }

    /**
     * @param $length
     * @return int
     */
    public function readNull($length){
        return $this->skip($length);
    }

    /**
     * Reads 1 byte from the stream and returns binary data as an 8-bit integer.
     *
     * @return integer
     */
    public function readInt8(){
        $data = $this->read(1);
        return $this->_fromInt8($data);
    }
    /**
     * Reads 1 byte from the stream and returns binary data as an unsigned 8-bit
     * integer.
     *
     * @return integer
     */
    public function readUint8(){
        $data = $this->read(1);
        return $this->_fromUint8($data);
    }

    /**
     * Reads 2 bytes from the stream and returns machine ordered binary data
     * as signed 16-bit integer.
     *
     * @return integer
     */
    public function readInt16()
    {
        return $this->_fromInt16($this->read(2));
    }

    /**
     * Reads 2 bytes from the stream and returns big-endian ordered binary data
     * as signed 16-bit integer.
     *
     * @return integer
     */
    public function readInt16BE(){
        if ($this->_isLittleEndian()) {
            return $this->_fromInt16(strrev($this->read(2)));
        } else {
            return $this->_fromInt16($this->read(2));
        }
    }

    /**
     * Reads 2 bytes from the stream and returns little-endian ordered binary
     * data as signed 16-bit integer.
     *
     * @return integer
     */
    public function readInt16LE(){
        if ($this->_isBigEndian()) {
            return $this->_fromInt16(strrev($this->read(2)));
        } else {
            return $this->_fromInt16($this->read(2));
        }
    }

    /**
     * Reads 2 bytes from the stream and returns machine ordered binary data
     * as unsigned 16-bit integer.
     *
     * @return integer
     */
    public function readUint16()
    {
        return $this->_fromUInt16($this->read(2), self::MACHINE_ENDIAN);
    }

    /**
     * Reads 2 bytes from the stream and returns big-endian ordered binary data
     * as unsigned 16-bit integer.
     *
     * @return integer
     */
    public function readUint16BE(){
        return $this->_fromUInt16($this->read(2), self::BIG_ENDIAN);
    }

    /**
     * Reads 2 bytes from the stream and returns little-endian ordered binary data
     * as unsigned 16-bit integer.
     *
     * @return integer
     */
    public function readUint16LE(){
        return $this->_fromUInt16($this->read(2), self::LITTLE_ENDIAN);
    }

    /**
     * Reads 4 bytes from the stream and returns machine ordered binary data
     * as signed 32-bit integer.
     *
     * @return integer
     */
    public function readInt(){
        return $this->readInt32();
    }

    /**
     * Reads 4 bytes from the stream and returns machine ordered binary data
     * as unsigned 32-bit integer.
     *
     * @return integer
     */
    public function readUint(){
        return $this->readUint32();
    }

    /**
     * Reads 4 bytes from the stream and returns machine ordered binary data
     * as signed 32-bit integer.
     *
     * @return integer
     */
    public function readInt32(){
        return $this->_fromInt32($this->read(4));
    }

    /**
     * Reads 4 bytes from the stream and returns machine ordered binary data
     * as unsigned 32-bit integer.
     *
     * @return integer
     */
    public function readUint32(){
        return $this->_fromUint32($this->read(4));
    }

    /**
     * Reads 4 bytes from the stream and returns big-endian ordered binary data
     * as signed 32-bit integer.
     *
     * @return integer
     */
    public function readInt32BE(){
        if ($this->_isLittleEndian())
            return $this->_fromInt32(strrev($this->read(4)));
        else
            return $this->_fromInt32($this->read(4));
    }

    /**
     * Reads 4 bytes from the stream and returns little-endian ordered binary
     * data as signed 32-bit integer.
     *
     * @return integer
     */
    public function readInt32LE(){
        if ($this->_isBigEndian())
            return $this->_fromInt32(strrev($this->read(4)));
        else
            return $this->_fromInt32($this->read(4));
    }

    /**
     * Reads 4 bytes from the stream and returns big-endian ordered binary data
     * as unsigned 32-bit integer.
     *
     * @return integer
     */
    public function readUint32BE(){
        return $this->_fromUint32($this->read(4),self::BIG_ENDIAN);
    }

    /**
     * Reads 4 bytes from the stream and returns little-endian ordered binary data
     * as unsigned 32-bit integer.
     *
     * @return integer
     */
    public function readUint32LE(){
        return $this->_fromUint32($this->read(4),self::LITTLE_ENDIAN);
    }

    /**
     * Reads 4 bytes from the stream and returns machine ordered binary data
     * as signed 24-bit integer.
     *
     * @return integer
     */
    public function readInt24(){
        return $this->_fromInt24($this->read(3));
    }

    /**
     * Reads 4 bytes from the stream and returns machine ordered binary data
     * as unsigned 24-bit integer.
     *
     * @return integer
     */
    public function readUint24(){
        return $this->_fromUint24($this->readBytes(3));
    }

    /**
     * Reads 4 bytes from the stream and returns big-endian ordered binary data
     * as signed 24-bit integer.
     *
     * @return integer
     */
    public function readInt24BE(){
        if ($this->_isLittleEndian())
            return $this->_fromInt24(strrev($this->read(3)));
        else
            return $this->_fromInt24($this->read(3));
    }

    /**
     * Reads 4 bytes from the stream and returns little-endian ordered binary
     * data as signed 24-bit integer.
     *
     * @return integer
     */
    public function readInt24LE(){
        if ($this->_isBigEndian())
            return $this->_fromInt24(strrev($this->read(3)));
        else
            return $this->_fromInt24($this->read(3));
    }

    /**
     * Reads 4 bytes from the stream and returns big-endian ordered binary data
     * as unsigned 24-bit integer.
     *
     * @return integer
     */
    public function readUint24BE(){
        return $this->_fromUInt24($this->readBytes(3),self::BIG_ENDIAN);
    }

    /**
     * Reads 4 bytes from the stream and returns little-endian ordered binary data
     * as unsigned 24-bit integer.
     *
     * @return integer
     */
    public function readUint24LE(){
        return $this->_fromUInt24($this->readBytes(3),self::LITTLE_ENDIAN);
    }

    /**
     * Reads 4 bytes from the stream and returns machine ordered binary data
     * as signed 64-bit integer.
     *
     * @return integer
     */
    public function readInt64(){
        if ($this->_isLittleEndian())
            return $this->readInt64LE();
        else
            return $this->readInt64BE();
    }

    /**
     * Reads 4 bytes from the stream and returns machine ordered binary data
     * as unsigned 64-bit integer.
     *
     * @return integer
     */
    public function readUint64(){
        return $this->_fromUint64($this->read(8));
    }

    /**
     * Reads 4 bytes from the stream and returns big-endian ordered binary data
     * as signed 64-bit integer.
     *
     * @return integer
     */
    public function readInt64BE(){
        /*if ($this->_isLittleEndian())
            return $this->_fromInt64(strrev($this->read(8)));
        else
            return $this->_fromInt64($this->read(8));
        */
        list(, $hihi, $hilo, $lohi, $lolo) = unpack('n*', $this->read(8));
        return ($hihi * (0xffff+1) + $hilo) * (0xffffffff+1) +
        ($lohi * (0xffff+1) + $lolo);
    }

    /**
     * Reads 4 bytes from the stream and returns little-endian ordered binary
     * data as signed 64-bit integer.
     *
     * @return integer
     */
    public function readInt64LE(){
        /*if ($this->_isBigEndian())
            return $this->_fromInt64(strrev($this->read(8)));
        else
            return $this->_fromInt64($this->read(8));*/
        list(, $lolo, $lohi, $hilo, $hihi) = unpack('v*', $this->read(8));
        return ($hihi * (0xffff+1) + $hilo) * (0xffffffff+1) +
        ($lohi * (0xffff+1) + $lolo);
    }

    /**
     * Reads 4 bytes from the stream and returns big-endian ordered binary data
     * as unsigned 64-bit integer.
     *
     * @return integer
     */
    public function readUint64BE(){
        return $this->_fromUInt64($this->read(8),self::BIG_ENDIAN);
    }

    /**
     * Reads 4 bytes from the stream and returns little-endian ordered binary data
     * as unsigned 64-bit integer.
     *
     * @return integer
     */
    public function readUint64LE(){
        return $this->_fromUInt64($this->read(8),self::LITTLE_ENDIAN);
    }

    /**
     * Reads 4 bytes from the stream and returns system-endian ordered binary
     * data as a 32-bit float point number as defined by IEEE 754.
     *
     * @return float
     */
    public function readFloat32(){
        if($this->options['endianness']==self::BIG_ENDIAN){
            return $this->readFloat32BE();
        }else{
            return $this->readFloat32LE();
        }
    }

    /**
     * Reads 4 bytes from the stream and returns little-endian ordered binary
     * data as a 32-bit float point number as defined by IEEE 754.
     *
     * @return float
     */
    public function readFloat32LE()
    {
        if ($this->_isBigEndian()) {
            return $this->_fromFloat32(strrev($this->read(4)));
        } else {
            return $this->_fromFloat32($this->read(4));
        }
    }

    /**
     * Reads 4 bytes from the stream and returns big-endian ordered binary data
     * as a 32-bit float point number as defined by IEEE 754.
     *
     * @return float
     */
    public function readFloat32BE()
    {
        if ($this->_isLittleEndian()) {
            return $this->_fromFloat32(strrev($this->read(4)));
        } else {
            return $this->_fromFloat32($this->read(4));
        }
    }

    /**
     * Reads 4 bytes from the stream and returns system-endian ordered binary
     * data as a 32-bit float point number as defined by IEEE 754.
     *
     * @return float
     */
    public function readFloat(){
         return $this->readFloat32();
    }

    /**
     * Reads 4 bytes from the stream and returns little-endian ordered binary
     * data as a 32-bit float point number as defined by IEEE 754.
     *
     * @return float
     */
    public function readFloatLE()
    {
        return $this->readFloat32LE();
    }

    /**
     * Reads 4 bytes from the stream and returns big-endian ordered binary data
     * as a 32-bit float point number as defined by IEEE 754.
     *
     * @return float
     */
    public function readFloatBE()
    {
        return $this->readFloat32BE();
    }

    /**
     * Reads 4 bytes from the stream and returns system-endian ordered binary
     * data as a 32-bit float point number as defined by IEEE 754.
     *
     * @return float
     */
    public function readSingle(){
        return $this->readFloat32();
    }

    /**
     * Reads 8 bytes from the stream and returns system-endian ordered binary
     * data as a 32-bit float point number as defined by IEEE 754.
     *
     * @return float
     */
    public function readFloat64(){
        if($this->options['endianness']==self::BIG_ENDIAN){
            return $this->readFloat64BE();
        }else{
            return $this->readFloat64LE();
        }
    }

    /**
     * Reads 8 bytes from the stream and returns system-endian ordered binary
     * data as a 32-bit float point number as defined by IEEE 754.
     *
     * @return float
     */
    public function readDouble(){
        if($this->options['endianness']==self::BIG_ENDIAN){
            return $this->readFloat64BE();
        }else{
            return $this->readFloat64LE();
        }
    }


    /**
     * Reads 4 bytes from the stream and returns little-endian ordered binary
     * data as a 32-bit float point number as defined by IEEE 754.
     *
     * @return float
     */
    public function readSingleLE()
    {
        return $this->readFloat32LE();
    }

    /**
     * Reads 4 bytes from the stream and returns big-endian ordered binary data
     * as a 32-bit float point number as defined by IEEE 754.
     *
     * @return float
     */
    public function readSingleBE()
    {
        return $this->readFloat32BE();
    }

    /**
     * Reads 8 bytes from the stream and returns little-endian ordered binary
     * data as a 64-bit floating point number as defined by IEEE 754.
     *
     * @return float
     */
    public function readFloat64LE()
    {
        if ($this->_isBigEndian()) {
            return $this->_fromFloat64(strrev($this->read(8)));
        } else {
            return $this->_fromFloat64($this->read(8));
        }
    }

    /**
     * Reads 8 bytes from the stream and returns big-endian ordered binary data
     * as a 64-bit float point number as defined by IEEE 754.
     *
     * @return float
     */
    public function readFloat64BE()
    {
        if ($this->_isLittleEndian()) {
            return $this->_fromFloat64(strrev($this->read(8)));
        } else {
            return $this->_fromFloat64($this->read(8));
        }
    }

    /**
     * Reads 8 bytes from the stream and returns little-endian ordered binary
     * data as a 64-bit floating point number as defined by IEEE 754.
     *
     * @return float
     */
    public function readDoubleLE()
    {
         return $this->readFloat64LE();
    }

    /**
     * Reads 8 bytes from the stream and returns big-endian ordered binary data
     * as a 64-bit float point number as defined by IEEE 754.
     *
     * @return float
     */
    public function readDoubleBE()
    {
        return $this->readFloat64BE();
    }

    /**
     * Default destructor. Close the stream handle
     */
    public function __destruct()
    {
        parent::close();
    }


    /**
     * @param $type_name
     * @param $unsigned
     * @param int $length
     * @return int
     */
    public function readByType($type_name,$unsigned,$length=0){
        $param_length = null;
        $suffix = '';
        $read_length = $this->getTypeLength($type_name);
        if(0 == $read_length){
            throw InvalidArgumentException::LengthCanNotBeZero();
        }
        if($read_length>1){
            if(true == $this->sysIsBigEndian()){
                $suffix = 'BE';
            }else{
                $suffix = 'LE';
            }
        }
        if($type_name != rtrim($type_name,'LBE')){
            $suffix = '';
        }
        if (-1 == $read_length){
            $param_length = $length;
            $suffix = '';
        }
        if('skip'==$type_name){
            return $this->skip($param_length);
        }
        $read_type = $type_name;
        if(isset($this->type_alias[$type_name])){
            $read_type = $this->type_alias[$type_name];
        }
        $sign ='';
        if(true == $unsigned){
            $sign = "U";
        }
        $method = 'read' . $sign . ucfirst($read_type) . $suffix;

        if(null != $param_length){
            return $this->$method($param_length);
        }
        if (-1 == $this->length_map[$type_name]){
            return $this->$method($param_length);
        }
        return $this->$method();
    }

}