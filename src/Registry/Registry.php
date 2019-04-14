<?php

/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\Registry;

use Sdds\Exceptions\RuntimeException;

/**
 * Class Registry
 * @package Sdds\Registry
 */
class Registry
{
    /**
     * input storage constants
     */
    const INPUT_STORAGE ='INPUT';

    /**
     * output storage constants
     */
    const OUTPUT_STORAGE ='INPUT';
    /**
     * object array with id key
     * @var array
     */
    private $id_registry = [];

    /**
     * object array with name and index key
     * @var array
     */
    private $index_registry = [];

    /**
     * Registry instance
     * @var null
     */
    private static $instance = null;

    /**
     * @param $type_name
     * @return mixed
     * @desc Using own instance for input and output.
     */
    public static function getInstanceByType($type_name) {
        if(!isset(self::$instance[$type_name])) {
            self::$instance[$type_name] = new Registry();
        }
        return self::$instance[$type_name];
    }

    /**
     * Registry constructor.
     */
    final private function __construct() {}

    /**
     * Registry clone.
     */
    final private function __clone() {}

    /**
     * @param $id
     * @param $value
     * @return true
     * @desc set the object by id
     */
    public function setById($id, $value) {
        if (isset($this->id_registry[$id])) {
            Throw RuntimeException::ThereIsAlreadyAnEntryForId($id);
        }
        $this->id_registry[$id] = $value;
        return true;
    }

    /**
     * @param $id
     * @return mixed
     * @desc Get the object by id
     */
    public function getById($id) {
        if (!isset($this->id_registry[$id])) {
            Throw RuntimeException::ThereIsNoEntryForId($id);
        }
        return $this->id_registry[$id];
    }

    /**
     * @param $name
     * @param $index
     * @param $value
     * @return true
     * @desc Set the object by name and index
     */
    public function setByIndex($name, $index, $value) {
        if (isset($this->index_registry[$name][$index])) {
            Throw RuntimeException::ThereIsAlreadyAnEntryForIndex($name,$index);
        }
        $this->index_registry[$name][$index] = $value;
        return true;
    }

    /**
     * @param $name
     * @param $index
     * @return mixed
     * @desc Get the object by name and index
     */
    public function getByIndex($name,$index ) {
        if (!isset($this->index_registry[$name][$index])) {
            Throw RuntimeException::ThereIsNoEntryForIndex($name,$index);
        }
        return $this->index_registry[$name][$index];
    }

    /**
     * @return $this
     * @desc clear the storage array for next operation.
     */
    public function clear(){
        $this->id_registry = [];
        $this->index_registry = [];
        return $this;
    }
}