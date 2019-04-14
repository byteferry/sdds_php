<?php

/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\Schema;

use Sdds\Exceptions\RuntimeException;
use Sdds\Exceptions\SchemaException;
use Sdds\Constants\NodeTypeConstants;

/**
 * Class Schema
 * @package Sdds\Schema
 */
abstract class Schema
{

    /**
     * @var array of schema data
     */
    protected $schema_definition = [];

    /**
     * @var array
     */
    protected $type_list = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    public $top_node_name = '';

    /**
     * @var \Sdds\DataDiagram\DataNode
     */
    protected $data_node;

    /**
     * @var array of DataNode with key of data_type
     */
    protected $custom_node_list=[];

    /**
     * @var array of DataNode with key of byte field name
     */
    protected $byte_node_list=[];

    /**
     * @var array of DataNode with key of bit field name
     */
    protected $bit_node_list=[];

    /**
     * @var array of DataNode with key of "One of"
     */
    protected $one_of_node_list=[];


    /**
     * Schema constructor.
     * @param \Sdds\DataDiagram\DataNode $data_node
     */
    public function __construct($data_node)
    {
        $this->data_node = $data_node;        
    }

    /**
     * @param $schema_file
     * @return $this
     * @throws SchemaException
     * @desc Initialise the object with Schema file
     * @thow RuntimeException
     */
    public function init($schema_file){
        if (is_file($schema_file)){
            $this->schema_definition = $this->readSchemaFile($schema_file);
        }else{
            Throw SchemaException::schemaFileReadFail();
        }
        if(isset($this->schema_definition['options'])){
            $this->options = $this->schema_definition['options'];
            $this->data_node->_options = $this->options;
            if(!isset($this->options['endianness'])){
                throw RuntimeException::optionItemUndefined("endianness");
            }
            if(!isset($this->options['min_length'])){
                throw RuntimeException::optionItemUndefined("min_length");
            }
        }else{
            throw RuntimeException::sddsSchemaItemIsUndefined('options');
        }
    }

    /**
     * @return $this
     */
    protected function setTopNode(){
        $this->top_node_name = null;
        if(isset($this->options['top_node'])){
            $this->top_node_name = $this->options['top_node'];
            return $this;
        }
        if(isset($this->type_list['document'])){
            $this->top_node_name = 'document';
            return $this;
        }
        if(isset($this->type_list['message'])){
            $this->top_node_name = 'message';
            return $this;
        }
        if(null == $this->top_node_name){
            throw RuntimeException::optionItemUndefined("top_node");
        }
        return $this;
    }

    /**
     * @param string $path
     * @return bool
     * @throws SchemaException
     * @desc Read the schema file
     */
    private function readSchemaFile($path){
        try {
            $content = file_get_contents($path);
            if (false === $content ) {
                // Handle the error
                Throw SchemaException::schemaFileReadFail();
            }
        } catch (\Exception $e) {
            // Handle exception
            Throw SchemaException::schemaFileReadFail($e->getMessage());
        }
        $schema_array = json_decode($content,true);
        if (false === $schema_array ) {
            // Handle the error
            Throw SchemaException::schemaIsNotValid();
        }
        return $schema_array;
    }

    /**
     * @param $node_type
     * @param null $type_name
     * @param array $json_array
     * @return mixed
     */
    public function factory($node_type, $type_name = null, $json_array = []){
        switch($node_type){
            case NodeTypeConstants::OF_NORMAL:{
                return $this->getNormalNode($json_array);
            }
            case NodeTypeConstants::OF_CUSTOM:{
                return $this->getCustomNode($type_name);
            }
            case NodeTypeConstants::OF_BYTE_FIELDS:{
                return $this->getByteNode($type_name,$json_array);
            }
            case NodeTypeConstants::OF_BIT_FIELDS:{
                return $this->getBitNode($type_name,$json_array);
            }
            case NodeTypeConstants::OF_SELECTOR:{
                return $this->getOneOfNode($type_name,$json_array);
            }
            case NodeTypeConstants::OF_REPEAT:{
                return $this->getCustomNode($type_name);
            }
        }
        return null;
    }

    /**
     * @param string $type_name
     * @return bool
     * @desc Check if the type name is in the list of schema
     */
    public function typeIsSet($type_name){
        return isset($this->type_list[$type_name]);
    }

    /**
     * @param $type_name
     * @return bool
     * @throws SchemaException
     * @desc Get the data of Schema by type name.
     */
    public function getType($type_name){
        if(!isset($this->type_list[$type_name])){
            Throw SchemaException::typeNotFound($type_name);
        }
        return $this->type_list[$type_name];
    }

    /**
     * @param $type_name
     * @return bool
     * @throws SchemaException
     * @desc Get the node from the node list of Schema
     */
    public function getCustomNode($type_name){
        if(!isset($this->custom_node_list[$type_name])){
            if(!isset($this->type_list[$type_name])){
                Throw SchemaException::typeNotFound($type_name);
            }
            $this->initNode($type_name);
        }
        return clone($this->custom_node_list[$type_name]);
    }

    /**
     * @param $type_name
     * @return bool
     * @throws SchemaException
     * @desc Initialise the node with schema data
     */
    private function initNode($type_name){
        $def_array = $this->getType($type_name);
        $node = clone($this->data_node);
        $node->initData($def_array);
        $this->custom_node_list[$type_name] = $node;
        return true;
    }

    /**
     * @param $key
     * @param $json_array
     * @return mixed
     * @desc Get the node of byte fields type
     */
    public function getByteNode($key,$json_array){
        if(!isset($this->byte_node_list[$key])){
            $node = clone($this->data_node);
            $node->initData($json_array);
            $this->byte_node_list[$key] = $node;
        }
        return clone($this->byte_node_list[$key]);
    }

    /**
     * @param $key
     * @param $json_array
     * @return mixed
     * @desc Get the node of bits type
     */
    public function getBitNode($key,$json_array){
        if(!isset($this->bit_node_list[$key])){
            $node = clone($this->data_node);
            $node->initData($json_array);
            $this->bit_node_list[$key] = $node;
        }
        return clone($this->bit_node_list[$key]);
    }

    /**
     * @param $key
     * @param $json_array
     * @return mixed
     * @desc Get the node of "One_Of"
     */
    public function getOneOfNode($key,$json_array){
        if(!isset($this->one_of_node_list[$key])){
            $node = clone($this->data_node);
            $node->initData($json_array);
            $this->one_of_node_list[$key] = $node;
        }
        return clone($this->one_of_node_list[$key]);
    }

    /**
     * @param $json_array
     * @return mixed
     * @desc Get the node of base type.
     */
    public function getNormalNode($json_array){
        $node = clone($this->data_node);
        $node->initData($json_array);
        return $node;
    }

}