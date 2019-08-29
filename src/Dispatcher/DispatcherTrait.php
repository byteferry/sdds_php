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

use Sdds\Exceptions\RuntimeException;
use Sdds\Dispatcher\Dispatcher;

trait DispatcherTrait
{
    public $channel_name;

    /**
     * @param $channel_name
     */
    public function setChannelName($channel_name){
        $this->channel_name = $channel_name;
    }
    /**
     * @return mixed
     */
    public function getEventType(){
        if(property_exists($this,'event_type')){
            return $this->event_type;
        }
        if(method_exists($this,'eventType')){
            return $this->eventType();
        }
        throw RuntimeException::eventTypeNotFound();
    }

    /**
     * @return mixed
     */
    public function getDispatcherType(){
        if(property_exists($this,'dispatcher_type')){
            return $this->dispatcher_type;
        }
        if(method_exists($this,'dispatcherType')){
            return $this->dispatcherType();
        }
        throw RuntimeException::dispatcherTypeNotFound();
    }

    /**
     * @return mixed
     */
    public function getDispatcher(){
        return Dispatcher::getInstance($this->channel_name,$this->getDispatcherType());
    }

    /**
     * @param $method_name
     * @return bool
     */
    public function hasListener($method_name){
        return $this->getDispatcher()
            ->hasListener($this->getEventType(),$method_name);
    }

    /**
     * @param ...$argument
     * @return mixed
     */
    public function triggerEvent(...$argument){
        $method_name = array_shift($argument);
        if(!$this->hasListener($method_name)){
            throw RuntimeException::MethodNotExists($method_name);
        }
        return $this->getDispatcher()->dispatch($this->getEventType(),$method_name,$argument);
    }
}