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
use Sdds\Exceptions\RuntimeException;



/**
 * Class StreamIo
 * @package Sdds\Stream
 */
class Stream
{
    /**
     * Endianness constant;
     */
    const MACHINE_ENDIAN = 0;

    /**
     * Endianness constant;
     */
    const LITTLE_ENDIAN = 1;

    /**
     * Endianness constant;
     */
    const BIG_ENDIAN = 2;

    /**
     *
     * Endianness constant;
     */
    const LOW_WORD_FIRST = 4;

    /**
     * Endianness constant;
     */
    const HI_WORD_FIRST = 8;

    /**
     * When bytes for little endian are in 'ABCD' order
     * then Big Endian Low Word First is in 'BADC' order
     * Endianness constant;
     */
    const BIG_ENDIAN_LOW_WORD_FIRST = self::BIG_ENDIAN | self::LOW_WORD_FIRST;

    /**
     * Stream type:Resource
     */
    const STREAM_RESOURCE = 0;

    /**
     * Stream type:Memory
     */
    const STREAM_MEMORY =1;

    /**
     * Stream type:string
     */
    const STREAM_STRING =2;

    /**
     * Stream type:file
     */
    const STREAM_FILE =3;

    /**
     * Stream type:temp file
     */
    const STREAM_TEMP_FILE =4;

    /**
     * Stream type:object
     */
    const STREAM_OBJECT =4;


    /**
     * @var array
     * example:[
     *  'charset'=>'UTF-8',
     *  'endianness' => 0,
     *  'digit' = 4
     * ]
     */
    public $options = [];

    /**
     * @var int
     */
    public $machine_endianness = self::BIG_ENDIAN;

    /**
     * @var int
     */
    public $system_endianness = self::BIG_ENDIAN;

    /**
     * @var resource
     */
    protected $_stream_handle = null;


    /**
     * alias of base type
     * @var array
     */
    protected $type_alias = [
        'bool'=>'int8',
        'char'=>'int8',
        'byte'=>'int8',
        'short'=>'int16',
        'word'=>'int16',
        'int'=>'int32',
        'dword'=>'int32',
        'long'=>'int64',
        'float'=>'float32',
        'float32'=>'single',
        'float64'=>'double',
    ];

    /**
     * @var int
     */
    private $stream_type = 0;

    /**
     * @var array
     * @desc keep the byte length of the base data type supported.
     */
    protected $length_map = [
        'bcd8421'=>-1,
        'bool'	=>1,
        'char'	=>1,
        'byte'	=>1,
        'bytes'	=>-1,
        'int8'	=>1,
        'short'	=>2,
        'word'	=>2,
        'int16'	=>2,
        'int24'	=>3,
        'int'	=>4,
        'int32'	=>4,
        'dword'	=>4,
        'long'	=>8,
        'int64'	=>8,
        'float'=>4,
        'float32'=>4,
        'single'=>4,
        'float64'=>8,
        'double'=>8,
        'string'=>-1,
        'skip'=>-1
    ];

    /**
     * Stream constructor.
     */
    protected function __construct()
    {

    }

    /**
     * @param resource $stream Stream resource to wrap.
     * @param array $options Associative array of options.
     * @param string $stream_type Stream type(RESOURCE,OBJECT,STRING,FILE,TEMP_FILE,MEMORY)
     * @return $this
     */
    public function init_stream($stream, $options = [], $stream_type = null)
    {
        if (!is_resource($stream)) {
            throw InvalidArgumentException::StreamMustBeResource();
        }
        $this->_stream_handle = $stream;
        $this->options = $options;
        $this->stream_type = $stream_type;
        $this->system_endianness = $options['endianness'];
        $this->machine_endianness = $this->getMachineEndianness();
        return $this;
    }

    /**
     * Create a new stream based on the input type.
     * @param mixed $resource Entity body data
     * @param array $options Additional options
     * @return static
     */
    public static function factory($resource = '', $options = [])
    {
        $type = gettype($resource);
        switch ($type) {
            case 'string':
                return (new static())->ofString($resource, $options);
            case 'resource':
                return (new static())->ofResource($resource, $options);
            case 'object':
                return (new static())->ofObject($resource, $options);
        }
        throw InvalidArgumentException::InvalidResourceType($type);
    }

    /**
     * Create a new stream based on given string.
     * @param string $path_file_name
     * @param string $string_buffer
     * @param array $options Additional options
     * @return static
     */
    public function ofFile($path_file_name, $string_buffer = '', $options = []){

        $stream = fopen($path_file_name, 'W+b');
        if ('' !== $string_buffer) {
            fwrite($stream, $string_buffer);
            fseek($stream, 0);
        }
        return $this->init_stream($stream, $options,self::STREAM_FILE);

    }

    /**
     * Create a new stream based on given string.
     * @param string $string_buffer
     * @param array $options Additional options
     * @param int $stream_type
     * @return static
     */
    public function ofString($string_buffer = '', $options = [],$stream_type=null){

        $stream = fopen('php://temp', 'r+');
        if ($string_buffer !== '') {
            fwrite($stream, $string_buffer);
            fseek($stream, 0);
        }
        if(null == $stream_type){
            $stream_type = self::STREAM_STRING;
        }
        return $this->init_stream($stream, $options,$stream_type);

    }

    /**
     * Create a new stream based on given resource.
     * @param resource $resource
     * @param array $options Additional options
     * @return static
     */
    public function ofResource($resource, $options = []){

        return $this->init_stream($resource, $options,self::STREAM_RESOURCE);

    }

    /**
     * Create a new stream based on given string.
     * @param string $string_buffer
     * @param array $options Additional options
     * @return static
     */
    public function ofTempFile($string_buffer = '', $options = []){

        $stream = fopen('php://temp', 'W+b');
        if ($string_buffer !== '') {
            fwrite($stream, $string_buffer);
            fseek($stream, 0);
        }
        return $this->init_stream($stream, $options,self::STREAM_TEMP_FILE);

    }

    /**
     * Create a new stream based on given string.
     * @param string $string_buffer
     * @param array $options Additional options
     * @return static
     */
    public function ofMemory($string_buffer = '', $options = []){

        $stream = fopen('php://memory', 'W+b');
        if ($string_buffer !== '') {
            fwrite($stream, $string_buffer);
            fseek($stream, 0);
        }
        return $this->init_stream($stream, $options,self::STREAM_MEMORY);
    }

    /**
     * Create a new stream based on given object.
     * @param object $obj
     * @param array $options Additional options
     * @return static
     */
    public function ofObject($obj, $options = []){

        if (method_exists($obj, '__toString')) {
            return $this->ofString((string)$obj, $options,self::STREAM_OBJECT);
        }
        throw InvalidArgumentException::InvalidResourceType();
    }

    /**
     * @param $options
     * @desc Initialise the stream with options.
     */
    public function init($options){
        $this->options = $options;
        $this->system_endianness = $options['endianness'];
        $this->machine_endianness = $this->getMachineEndianness();
    }


    /**
     * Test the Endianness of the machine.
     * @return int
     */
    public function getMachineEndianness() {
        return (unpack('S',"\x01\x00")[1] === 1)? self::LITTLE_ENDIAN : self::BIG_ENDIAN;
    }

    /**
     * Check current system Endianness
     * @return bool
     */
    public function sysIsLittleEndian(){
        return (self::LITTLE_ENDIAN == $this->system_endianness);
    }

    /**
     * Check current machine Endianness
     * @return bool
     */
    protected function _isLittleEndian(){
        return (self::LITTLE_ENDIAN == $this->machine_endianness);
    }

    /**
     * Check current system Endianness
     * @return bool
     */
    public function sysIsBigEndian(){
        return (self::BIG_ENDIAN == $this->system_endianness);
    }

    /**
     * Check current machine Endianness
     * @return bool
     */
    protected function _isBigEndian(){
        return (self::BIG_ENDIAN == $this->machine_endianness);
    }

    /**
     * Get stream meta data
     * @return array
     */
    public function getMetaData()
    {
        return stream_get_meta_data($this->_stream_handle);
    }

    /**
     * Get stream resource
     * @return resource
     */
    public function getResource()
    {
        return $this->_stream_handle;
    }

    /**
     * Get stream size
     * @return int
     */
    public function size()
    {
        $currPos = ftell($this->_stream_handle);
        fseek($this->_stream_handle, 0, SEEK_END);
        $length = ftell($this->_stream_handle);
        fseek($this->_stream_handle, $currPos, SEEK_SET);
        return $length;
    }

    /**
     * allocate new stream from current stream
     * @param int $length
     * @param bool $skip
     * @return static
     * @throws RuntimeException
     */
    public function allocate($length, $skip = true)
    {
        $stream = fopen('php://memory', 'r+');
        if (stream_copy_to_stream($this->_stream_handle, $stream, $length)) {
            if ($skip) {
                $this->skip($length);
            }
            return new static($stream);
        }
        throw RuntimeException::BufferAllocationFailed();
    }

    /**
     * Copies data from $resource to stream
     * @param resource $resource
     * @param int $length Maximum bytes to copy
     * @return int
     */
    public function pipe($resource, $length = null)
    {
        if (!is_resource($resource)) {
            throw InvalidArgumentException::InvalidResourceType();
        }
        if ($length) {
            return stream_copy_to_stream($resource, $this->_stream_handle, $length);
        } else {
            return stream_copy_to_stream($resource, $this->_stream_handle);
        }
    }

    /**
     * Returns the current position of the stream pointer
     * @return int
     */
    public function offset()
    {
        $this->_checkStreamHandle();
        return ftell($this->_stream_handle);
    }

    /**
     * @desc Check if the stream handle is valid.
     * @return void
     * @throw \Sdds\Exceptions\RuntimeException;
     */
    protected function _checkStreamHandle(){
        if (null == $this->_stream_handle){
            throw RuntimeException::StreamIsClosed();
        }
    }

    /**
     * Move the stream pointer to a new position
     * @param int $offset
     * @param int $whence Accepted values are:
     *  - SEEK_SET - Set position equal to $offset bytes.
     *  - SEEK_CUR - Set position to current location plus $offset.
     *  - SEEK_END - Set position to end-of-stream plus $offset.
     * @return int
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $this->_checkStreamHandle();
        return fseek($this->_stream_handle, $offset, $whence);
    }

    /**
     * Rewind the position of a stream pointer
     * @return bool true on success or false on failure.
     */
    public function rewind()
    {
        $this->_checkStreamHandle();
        return rewind($this->_stream_handle);
    }

    /**
     * @param int $length The count will skipped.
     * @return int
     * @desc Skip the byte read the count given by length used by input stream.
     */
    public function skip($length=1)
    {
        if (0 == $length){
            return $this->offset();
        }
        $this->_checkStreamHandle();
        return $this->seek($length, SEEK_CUR);
    }

    /**
     * @param int $length The maximum bytes to read. Defaults to -1 (read all the remaining buffer).
     * @return string a string or false on failure.
     * @desc Reads remainder of a stream into a string
     */
    public final function read($length = 0)
    {
        $this->_checkStreamHandle();
        if (0 == $length) {
            return '';
        }
        if($this->isEof()){
            throw RuntimeException::StreamIsEnd();
        }
        $result = stream_get_contents($this->_stream_handle, $length, $this->offset());

        if(strlen($result) < $length){
            throw RuntimeException::StreamReadFail(count($result),$length);
        }

        return $result;
    }

    /**
     * @return string
     * @desc Function for debug. It will output the content of the stream.
     */
    public function debug(){

        $pos = $this->offset();
        $this->seek(0,SEEK_SET);
        $length = $this->size();
        $buffer = $this->read($length);
        $this->seek($pos,SEEK_SET);
        $hex_string = 'Stream Content: ' . unpack("H*", $buffer)[1];
        return $hex_string;

    }


    /**
     * @return void
     * @desc close the handle of stream.
     */
    public function close()
    {
        if (is_resource($this->_stream_handle)) {
            fclose($this->_stream_handle);
        }
    }


    /**
     * @param $method_name
     * @param $args
     * @return mixed
     * @throws RuntimeException
     * @desc Method for calling with alias:
     */
    public function __call($method_name,$args){

        if (method_exists($this,$method_name)){
            return call_user_func_array([$this,$method_name],$args);
        }

        if (preg_match('~^(read|write|insert|replace|skip)(U{0,1})([A-Z])([a-z0-9]*)([LBE]*)$~', $method_name, $matches)) {
            $action = $matches[1];
            $sign = $matches[2];
            $type = strtolower($matches[3]) . $matches[4];
            $suffix =  $matches[5];

            if (isset($this->type_alias[$type])) {
                $type = $this->type_alias[$type];
            }

            if ($matches[1] == 'skip'){
                $length = $this->length_map[$type];
                return $this->skip($length);
            }

            $method = $action .$sign .ucfirst($type).$suffix ;

            if (method_exists(get_called_class(),$method)){
                return call_user_func_array([$this,$method],$args);
            }else{
                throw RuntimeException::MethodNotExists($method);
            }
        }
        return false;
    }


    /**
     * Method for calling via program code
     * @param $action
     * @param $unsigned
     * @param $type
     * @param $endian
     * @param ...$args
     * @return mixed
     * @desc Dynamic call a function with parameters and arguments.
     */
    public function callByType($action,$unsigned,$type,$endian,...$args){
		if (isset($this->type_alias[$type])) {
            $type = $this->type_alias[$type];
        }
        $suffix = '';
        if(self::BIG_ENDIAN == $endian){
            $suffix = 'BE';
        }elseif(self::LITTLE_ENDIAN == $endian){
            $suffix = 'LE';
        }
        if($this->length_map[$type]<2){
            $suffix = '';
        }
        $method = $action . $unsigned .  ucfirst($type) .$suffix;
        if(method_exists(get_called_class(),$method)){
            return $this->$method(...$args);
        }else{
            throw RuntimeException::MethodNotExists($method);
        }
    }

    /**
     * @param $action
     * @param $type_name
     * @return bool
     * @desc Check if the method is callable by the action and data type name.
     */
    public function isCallable($action, $type_name){
        if ('skip'==$type_name){
            return true;
        }
        $method_name = $action. ucfirst($type_name);
        if (method_exists(get_called_class(),$method_name)){
            return true;
        }

        return false;
    }

    /**
     * @return bool
     * @desc Check if the offset is to the end of stream.
     */
    public function isEof(){
         return ($this->offset() >= $this->size());
    }

    /**
     * @param $type_name
     * @return bool
     * @desc Check the type_name is a base type
     */
    public function isBaseType($type_name){
        $type_name = trim($type_name,'LUBE');
        return isset($this->length_map[$type_name]);
    }

    /**
     * @param $type_name
     * @return int
     */
    public function getTypeLength($type_name){
        $type_name = trim($type_name,'LUBE');
        if(isset($this->length_map[$type_name])){
            return $this->length_map[$type_name];
        }
        return 0;
    }

    /**
     * @param $type_name
     * @param $length
     * @desc Add a base type to the length map
     */
    public function addType($type_name,$length){
        if(!isset($this->length_map[$type_name])){
            $this->length_map[$type_name]=$length;
        }
    }

    /**
     * @param $alias
     * @param $type_name
     * @desc add a alias of a base data type.
     */
    public function addAlias($alias,$type_name){
        if(!isset($this->type_alias[$alias])){
            $this->type_alias[$alias]=$type_name;
        }
    }

}