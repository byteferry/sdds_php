<?php

/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\DataDiagram;
use ByteFerry\Exceptions\RuntimeException;
use Sdds\Constants\SelectorConstants;
use Sdds\Constants\NodeTypeConstants;
use Sdds\Dispatcher\Dispatcher;
use Sdds\FormulaEngine\FormulaEngine;
use Sdds\Registry\Registry;

/**
 * Class InputNode
 * @package Sdds\DataDiagram
 */
class InputNode extends DataNode
{
    public $action_type = Dispatcher::INPUT;

    /**
     * InputNode constructor.
     * @param $channel_name
     */
    public function __construct($channel_name){
        parent::__construct($channel_name);
        $this->_registry = Registry::getInstanceByType(Registry::INPUT_STORAGE,$channel_name);
    }

    /**
     * @desc decode the string buffer, and this is a entrance function
     *
     * @param string $string_buffer
     * @return array
     */
    public function decode($string_buffer){
        $this->_source_data = $string_buffer;
        $this->_parent = null;
        $this->_level = 1;
        //initialise the input stream using the string buffer
        $this->_stream->ofMemory($string_buffer,$this->_options);
        //read the root node and children
        $this->read($this->_stream);
        //dd($this->toDebugArray());
        $return_array = [];
        //get the value with array
        $this->toArray($return_array);
        //destroy the objects
        $this->destroy();
        return $return_array;
    }

    /**
     * @param array $byte_array
     * @return $this
     */
    private function initBitwise($byte_array){
        $bitwise = $this->_bitwise;
        $this->_bitwise = $bitwise->ofByte($byte_array);
        return $this;
    }

    /**
     * @desc read the bit and bits type using the Bitwise class
     *
     * @param $Bitwise
     * @return $this
     */
    public function readBits(&$Bitwise){
        $this->_bitwise = $Bitwise;
        $this->beforeRead();
        $this->value = $Bitwise->readByType($this->type,$this->getLength());
        $this->afterRead();
        return $this;
    }

    /**
     * @return mixed
     * @desc show the information of debug if the debug option is true
     */
    public function showDebug(){
        if(false === $this->_options['debug']){
            return;
        }
        if(1 === $this->_level){
            dd($this->toDebugArray());
        }else{
            $this->_parent->showDebug();
        }
    }

    /**
     * @desc This is the main function for the reading.
     *
     * @param $Stream
     */
    public function read(&$Stream){

        $this->_stream = $Stream;
        $this->beforeRead();
        // Deal the process. Include before_change, byte_fields,
        // bit_fields, one_of, repeat
        $this->dealByteFields()->dealBitFields()->dealOneOf()->dealRepeat();

        //deal the base type node
        if($this->isBaseType($this->type)){
            //Read the data
            $this->readBaseType();
        }else{ //deal the custom type node
            if($this->_stream->isCallable('read',$this->type)){
                $method = 'read' . ucfirst($this->type);
                $this->_stream->$method();
            }else{
                if ($this->_child_type != NodeTypeConstants::OF_REPEAT) {
                    $this->_children[$this->type] = $this->_schema->getCustomNode($this->type);
                    $this->initChild($this->_children[$this->type]);
                    $this->_children[$this->type]->read($this->_stream);
                }
            }
        }
        $this->afterRead();

    }

    /**
     * @desc deal the repeat nodes, only support the byte node now.
     *
     * @return $this
     */
    private function dealRepeat(){
        if(true !== $this->repeat){
            return $this;
        }
        //$length = $this->getNodeValue($this->length);
        $length=0;
        $start_position = $this->_stream->offset();
        $this->_child_type = NodeTypeConstants::OF_REPEAT;
        //the condition could be length, count, or the end of buffer
        for($i=0;$this->hasNext($i,$length);$i++){
            //deal the base type
            if($this->isBaseType($this->type)){
                $this->readBaseType();
            }else{ //deal the custom nodes
                if($this->_stream->isCallable('read',$this->type)){
                    $method = 'read' . ucfirst($this->type);
                    $this->_stream->$method();
                }else {
                    $node = $this->_schema->getCustomNode($this->type);
                    $this->_children[$i] = $node;
                    $this->initChild($this->_children[$i]);
                    $this->_children[$i]->_index = $i;
                    $this->_children[$i]->read($this->_stream);
                }
            }
            //update the length for check if end.
            $length = $this->_stream->offset() - $start_position;
        }
        $this->_done = true;

        return $this;
    }

    /**
     * @desc deal the one_of structure.
     *
     * @return $this
     */
    private function dealOneOf(){
        // $this->one_of is read from the schema.
        if(0==count($this->one_of)){
            return $this;
        }

        $one_of = $this->one_of;
        //read the key name properties
        $node = $one_of['key'];
        //read the value of the key
        $selector = $node['value'];
        $key = $this->getValueBySelector($selector[0],$selector);
        //format the key
        if(isset($node['format'])){
            $key = $this->formatValue($key,$node['format']);
        }
        //initialise the children
        if(isset($one_of['list'])){
            $node = $one_of['list'];
            if(isset($node[$key])){
                $this->_child_type = NodeTypeConstants::OF_SELECTOR;
                $child = $this->_schema->getOneOfNode($key,$node[$key]);
                $this->_children[$key] = $child;
                $this->initChild($this->_children[$key]);
                $this->_children[$key]->read($this->_stream);
            }else{
                throw RuntimeException::keyNotfount($key);
            }
        }else{
            throw RuntimeException::listBranchIsNotSet($this->name);
        }
        //dd($this->children);

        return $this;
    }



    /**
     * @desc For bit_fields, we must initialise the Bitwise first.
     *
     * @return $this
     */
    private function dealBitFields(){
        if(0==count($this->bit_fields)){
            return $this;
        }

        $this->_child_type = NodeTypeConstants::OF_BIT_FIELDS;
        if(empty($this->type)){
            $this->type = 'bytes';
        }
        // initialise the Bitwise first.
        $this->readBaseType();
        $this->initBitwise($this->value);
        $bit_fields = $this->bit_fields;
        //Deal the children
        foreach($bit_fields as $key => $value){
            $this->_children[$key] = $this->_schema->getBitNode($key,$value);
            $this->initChild($this->_children[$key]);
            $this->_children[$key]->readBits($this->_bitwise);
        }

        return $this;
    }

    /**
     * @return $this
     * @desc handle the children of byte_fields
     */
    private function dealByteFields(){
        if(0==count($this->byte_fields)){
            return $this;
        }

        $byte_fields = $this->byte_fields;
        $this->_child_type = NodeTypeConstants::OF_BYTE_FIELDS;
        foreach($byte_fields as $key => $value){
            $type = $value['type'];
            if($this->isBaseType($type)){
                $this->_children[$key] = $this->_schema->getNormalNode($value);
                $this->initChild($this->_children[$key]);
            }else{
                $this->_children[$key] = $this->_schema->getByteNode($key,$value);
                $this->initChild($this->_children[$key]);
            }
            $this->_children[$key]->read($this->_stream);
        }

        return $this;
    }

    /**
     * @desc read the data of base type
     */
    public function readBaseType(){
        if($this->_done){
            return;
        }
        $this->value = $this->_stream->readByType(
            $this->type,
            $this->unsigned,
            $this->getLength());

        $this->_done = true;
    }

    /**
     * @return mixed
     * @throws \Exception
     * @desc Calculate the value with given formula
     */
    public function decodeWithFormula(){
        if(null === $this->value){
            return null;
        }
        if (!empty($this->formula)){
            return $this->value = FormulaEngine::calculate($this->formula,
                ['A' => $this->value]);
        }
        return $this->value;
    }

    /**
     * @desc Do some operations before the reading.
     */
    public function beforeRead(){
        //Write the offset();
        $this->writeOffset();
        if(true == $this->debug){
            $this->showDebug();
        }
        $this->triggerBeforeChange();
    }

    /**
     * @desc Do some operations after the reading.
     */
    public function afterRead(){

        $this->decodeWithFormula();

        // deal the after_change
        $this->triggerAfterChange();

        if(SelectorConstants::BY_ID == $this->selector){
            $this->setById($this->id,$this);
        }

        if(SelectorConstants::BY_INDEX == $this->selector){
            $this->setByIndex($this->_index,$this,$this->name);
        }

        $this->showTrace();
    }

}