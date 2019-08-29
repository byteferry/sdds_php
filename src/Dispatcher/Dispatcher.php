<?php
/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\Dispatcher;


class Dispatcher
{

    /**
     * @var self, Dispatcher instance
     */
    public static $instance = [];

    /**
     * @var array, event listeners
     */
    public $handlers = [];

    /**
     * Dispatcher constructor.
     */
    final private function __construct()
    {

    }

    /**
     * @param $channel_name
     * @param $action_type
     * @return mixed
     */
    final public static function getInstance($channel_name,$action_type){
        if(!isset(self::$instance[$channel_name][$action_type])) {
            self::$instance[$channel_name][$action_type] = new Dispatcher();
        }
        return self::$instance[$channel_name][$action_type];
    }

    /**
     * clone is not allowed!
     */
    final private function __clone(){}

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name){
        return $this->$name;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name,$value){
        $this->$name = $value;
    }

    /**
     * @param $Handler
     */
    public function registerHandler($Handler){
        $this->handlers[$Handler->name] = $Handler;
    }

    /**
     * @param $Handler
     */
    public function removeHandler($Handler){
        unset($this->handlers[$Handler->name]);
    }

    /**
     * @param $event_type
     * @param $event
     * @return bool
     */
    public function hasListener($event_type,$event){
        foreach($this->handlers as $handler){
            if($handler->hasListener($event_type,$event)){
                return true;
            }
        }
        return false;
    }

    /**
     * @param $event_type
     * @param $event
     * @param $params
     * @return mixed
     */
    public function dispatch($event_type,$event,$params){
        foreach($this->handlers as $handler){
            if($handler->hasListener($event_type,$event)){
                return call_user_func_array([$handler,$event],[$event_type,$params]);
            }
        }
    }

}