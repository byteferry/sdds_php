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
use Sdds\Constants\SelectorConstants;
use Sdds\Exceptions\RuntimeException;
use Sdds\FormulaEngine\FormulaEngine;
use Sdds\Constants\NodeTypeConstants;
use Sdds\Registry\Registry;

/**
 * Class OutputNode
 * @package Sdds\DataDiagram
 */
class OutputNode extends DataNode
{

    /**
     * OutputNode constructor.
     */
    public function __construct(){
        parent::__construct();
        $this->_registry = Registry::getInstanceByType(Registry::OUTPUT_STORAGE);
    }

    /**
     * @param array $packet_data
     * @return string
     * @desc the entrance function
     */
    public function encode($packet_data){
        //Initialise the object
        $this->_source_data = $packet_data;
        $this->_parent = null;
        $this->_level = 1;
        //$this->showTrace("this->length ::" . $this->length);
        //Initialise the stream
        if(empty($this->length)){
            $this->length = $this->_options['min_length'];
        }
        //$this->showTrace("this->length ::" . $this->length);
        $string_buffer = str_repeat("\x00",$this->length);
        $this->_stream->ofMemory($string_buffer,$this->_options);
        //Begin to write from the top node
        $this->write($this->_stream,$packet_data);
        //Get the string buffer which is the result of writing
        $value = $this->toBuffer();
        //Destroy all child for save memory.
        $this->destroy();
        //return the string buffer
        return $value;
    }

    /**
     * @desc Read the packet data to value, if the variable name has set.
     */
    private function initValue(){
        //if the variable name has not set
        if(empty($this->name)){
            return;
        }
        //if there is none date in the _source_data
        if(isset($this->_source_data[$this->name])){
            $this->value = $this->_source_data[$this->name];
            return;
        }
        //if there is a default value
        if(isset($this->default)){
            $this->value = $this->default;
            return;
        }
        //if the variable is required
        if(true === $this->required){
            if(false===$this->isIgnoreErrors()) {
                throw RuntimeException::ValueIsRequired($this->name);
            }
        }

    }

    /**
     * @param $Bitwise
     * @return $this
     * @Desc Do the write operation when the node is a field of bits.
     */
    public function writeBits(&$Bitwise){
        //Initialise the Bitwise
        $this->_bitwise = $Bitwise;

        $this->initValue();

        $this->beforeWrite();

        //write the data
        $Bitwise->writeByType($this->type,$this->value,$this->getLength());

        $this->afterWrite();
        return $this;
    }

    /**
     * @return mixed
     * @desc Show debug information if debug option is true
     */
    public function showDebug(){
        if(false === $this->_options['debug']){
            return;
        }
        if(1 === $this->_level){
            //Read the contents with hex string from the stream.
            $hex_string = $this->_stream->debug();
            //show hex string
            d($hex_string);
            //show data array
            dd($this->toDebugArray());
        }else{
            $this->_parent->showDebug();
        }
    }


    /**
     * @param $Stream
     * @param $source_data
     * @throws \Sdds\Exceptions\SchemaException
     * @desc main function for writing.
     */
    public function write(&$Stream,$source_data){
        //Initialise the object
        $this->_stream = $Stream;
        $this->_source_data = $source_data;

        // Read the packet data to value, if the variable name has set.
        $this->initValue();

        $this->beforeWrite();

        // Deal the process. Include before_change, byte_fields,
        // bit_fields, one_of ,repeat
        $this->triggerBeforeChange()->dealByteFields()->dealBitFields()
            ->dealOneOf()->dealRepeat();

        //deal the base type node
        if($this->isBaseType($this->type)){
            $this->writeBaseType();
        }else{ //deal the custom type node
            if(!empty($this->type)){
                if($this->_child_type != NodeTypeConstants::OF_REPEAT){
                    $this->_children[$this->type] = $this->_schema->getCustomNode($this->type);
                    $this->initChild($this->_children[$this->type]);
                    //Write the data
                    $this->_children[$this->type]->write($this->_stream,$source_data);
                }
            }
        }
        $this->afterWrite();
    }

    /**
     * @return $this
     * @desc deal the repeat nodes, only support the byte node now.
     */
    private function dealRepeat(){
        if(true !== $this->repeat){
            return $this;
        }

        //read the count
        $this->count = count($this->_source_data['children']);
        $length = 0;
        $start_position = $this->_stream->offset();
        $this->_child_type = NodeTypeConstants::OF_REPEAT;
        if(!empty($this->trace)){
            $this->showTrace();
        }
        //the condition could be length, count, or the end of buffer
        for($i=0;$this->hasNext($i,$length);$i++){
            //deal the base type
            if($this->isBaseType($this->type)){
                $this->writeBaseType();
            }else{ //deal the custom nodes
                if($this->_stream->isCallable('write',$this->type)){
                    $method = 'write' . ucfirst($this->type);
                    $this->_stream->$method();
                }else {
                    $node = $this->_schema->getCustomNode($this->type);
                    $this->_children[$i] = $node;
                    $this->initChild($this->_children[$i]);
                    $this->_children[$i]->_index = $i;
                    //Write the data
                    $this->_children[$i]->write($this->_stream,$this->_source_data['children'][$i]);
                }
            }
            //update the length for check if end.
            $length = $this->_stream->offset() - $start_position;
        }
        $this->_done = true;


        return $this;
    }

    /**
     * @return $this
     * @desc deal the one_of structure.
     */
    private function dealOneOf(){
        // $this->one_of is read from the schema.
        if(0 == count($this->one_of)){
            return $this;
        }

        //read the key name properties
        $node = $this->one_of['key'];
        //read the value of the key
        $selector = $node['value'];
        $key = $this->getValueBySelector($selector[0],$selector);
        //format the key
        if(isset($node['format'])){
            $key = $this->formatValue($key,$node['format']);
        }
        //initialise the children
        if(isset($this->one_of['list'])){
            $node = $this->one_of['list'];
            if(isset($node[$key])){
                $this->_child_type = NodeTypeConstants::OF_SELECTOR;
                $child = $this->_schema->getOneOfNode($key,$node[$key]);
                $this->_children[$key] = $child;
                $this->initChild($this->_children[$key]);
                //write the data
                $this->_children[$key]->write($this->_stream,$this->_source_data);
            }
        }

        return $this;
    }

    /**
     * @param $length
     * @return $this
     * @desc Initialise the Bitwise
     */
    private function initBitwise($length){
        $Bitwise = $this->_bitwise;
        $this->_bitwise = $Bitwise->ofByte($length);
        return $this;
    }

    /**
     * @return $this
     * @desc For bit_fields, we must initialise the Bitwise first.
     *
     */
    private function dealBitFields(){
        if(0 == count($this->bit_fields)){
            return $this;
        }

        $this->_child_type = NodeTypeConstants::OF_BIT_FIELDS;
        if(empty($this->type)){
            $this->type = 'bytes';
        }
        // initialise the Bitwise first.
        $this->initBitwise($this->getBitFieldsLength());
        //Deal the children
        foreach($this->bit_fields as $key => $value){
            $this->_children[$key] = $this->_schema->getBitNode($key,$value);
            $node = $this->_children[$key];
            $this->initChild($node);
            $node->_source_data = $this->_source_data;
            //write the data
            $node->writeBits($this->_bitwise);
         }
        $this->value = $this->_bitwise->getValue();
        //Write to stream.
        $this->writeBaseType();

        return $this;
    }

    /**
     * @return $this
     * @desc handle the children of byte_fields
     */
    private function dealByteFields(){
        if(0 == count($this->byte_fields)){
            return $this;
        }

        $this->_child_type = NodeTypeConstants::OF_BYTE_FIELDS;
        foreach($this->byte_fields as $key => $value){
            $type = $value['type'];
            if($this->isBaseType($type)){
                $this->_children[$key] = $this->_schema->getNormalNode($value);
                $this->initChild($this->_children[$key]);
            }else{
                $this->_children[$key] = $this->_schema->getByteNode($key,$value);
                $this->initChild($this->_children[$key]);
            }
            //Write the data
            $this->_children[$key]->write($this->_stream,$this->_source_data);
        }

        return $this;

    }

    /**
     * @desc read the data of base type
     */
    public function writeBaseType(){

        if($this->_done){
            return;
        }
        $this->_stream->writeByType($this->type,$this->value,$this->unsigned,$this->getLength());
        $this->_done = true;
    }


    /**
     * @return int
     * @
     */
    public function getLength(){
        if(0 < $this->length){
            $this->_data_length = $this->length;
            return $this->length;
        }
        if(0==$this->_data_length){
            $this->_data_length = 0;
            if (null != $this->length){
                $this->_data_length = $this->length;
            }
            if (count($this->_children)>0){
                foreach($this->_children as $value){
                    $this->_data_length += $value->getLength();
                }
            }
         }
        return $this->_data_length;
    }

    /**
     * Convert the tree object to string buffer recursively.
     * @return string
     */
    public function toBuffer(){
        return $this->_stream->toBuffer();
    }

    /**
     * @return mixed
     * @throws \Exception
     * @desc Calculate the value with given formula
     */
    public function encodeWithFormula(){
        if(!isset($this->value)){
           return null;
        }
        if (!empty($this->formula)){
            return $this->value = FormulaEngine::calculate($this->formula,['A' => $this->value]);
        }
        return $this->value;
    }

    /**
     * @return int
     */
    private function getBitFieldsLength(){
        $length = 0;
        foreach($this->bit_fields as $field){
            $length += $field['length'];
        }
        return $length;
    }

    /**
     * @desc Do some operations after the writing.
     */
    public function afterWrite(){
        //deal the after_change Make the function call
        $this->triggerAfterChange();

        //Write to the Id array
        if(SelectorConstants::BY_ID == $this->selector){
            $this->setById($this->id,$this);
        }
        //Write to the item share array
        if(SelectorConstants::BY_INDEX == $this->selector){
            $this->setByIndex($this->_index,$this,$this->name);
        }

        //Show trace information if debug.
        $this->showTrace();
    }

    /**
     * @desc Do some operations before the writing.
     */
    public function beforeWrite(){
        //Write the offset();
        $this->writeOffset();

        $this->encodeWithFormula();

        //show debug
        if(true == $this->debug){
            $this->showDebug();
        }

        //Make the function call
        $this->triggerBeforeChange();
    }

}