```
  /$$$$$$  /$$$$$$$  /$$$$$$$   /$$$$$$ 
 /$$__  $$| $$__  $$| $$__  $$ /$$__  $$
| $$  \__/| $$  \ $$| $$  \ $$| $$  \__/
|  $$$$$$ | $$  | $$| $$  | $$|  $$$$$$ 
 \____  $$| $$  | $$| $$  | $$ \____  $$
 /$$  \ $$| $$  | $$| $$  | $$ /$$  \ $$
|  $$$$$$/| $$$$$$$/| $$$$$$$/|  $$$$$$/
 \______/ |_______/ |_______/  \______/ 
 
```
# SDDS for PHP

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

基于SDDS规范的二进制流数据编码与解码快速开发工具。
  
[English Document](https://github.com/byteferry/sdds_php)

## 简介

### 什么是SDDS?
    
- 数据结构
  
SDDS是"stream data dynamic structure"的缩写。
  
- DSL
  
SDDS是一种DSL (domain-specific language)。
  
SDDS schema 是人类可读且机器要理解的。 
  
- 解析引擎
  
SDDS是基于SDDS规范编写的解析引擎，用它可以实现二进制通讯程序的快速开发。
   


### SDDS PHP特性

- 多协议支持
 
一套程序可以支持多套应用层通讯协议。每一个协议即是一个通道(Channel)。
 
- 数据类型 
    
支持SDDS规范要求的所有数据类型。

- 大小端支持

你只需要在SDDS Schema中配置一下。如果有个别大小端不一致，你也可以配置到对应的节点。
    
- Charset支持
   
默认使用utf-8，你也可以指定不同的编码。      
    
- 事件扩展
    
所以自定义函数可以通过对应的EventHandler实现。
    
- 公式表达式
 
支持编码前，或解码后通过公式进行计算。

- 调试方便

默认集成larapack/dd，你可以使用浏览器进行调试请求，并通过dd输出到页面。


### 为什么要用SDDS?
   
通常我们都要为不同的通讯协议编写不同的程序。但是，你若使用SDDS，则你基本使用的程序都是一样的。并且，SDDS已为你实现了。
    
你所要做的只是，SDDS未实现的数据流操作，数据类型，位操作等扩展。并且，通过SDDS Schema的JSON定义，很多时候，你只要修改SDDS Schema，而不需要修改你的代码。
    
SDDS可以大大加快你的开发。
 
## 快速向导
  
### 安装

使用Composer安装

``` bash
$ composer require byteferry/sdds_php:dev-master
```

### 使用

- 第一步：参考[SDDS规范](https://github.com/byteferry/sdds/blob/master/README_cn.md)，编写SDDS Schema.
  
- 第二步：编写代码，调用SDDS PHP
  
1、实现Channel类：
    
比如：名为DecoderChannel
    
```
namespace Sdds\Examples\Decode;

use Sdds\Channels\InputChannel;
use Sdds\Channels\ChannelInterface;

class DecodeChannel extends InputChannel implements ChannelInterface
{
    /**
     * @return mixed
     */
    public function registerHandlers(){
        //如果没有对应的EventsHandler,那就直接返回
        return;
    }
}
```    
  
2、调用你的DecodeChannel
 
```
    $channel = DecodeChannel::getInstance();
    $packet_decoder = $channel->getDecoder($your_channel_name,$your_schema_file);
    $data = $packet_decoder->decode($stream_data);
    
```  
    
3、通过浏览器使用http方式，利用DD调试你的SDDS Schema。（为方便快速开发，我们集成了Larapack/dd）  
  
4、与你的通讯程序实现集成。
  
### 进阶使用
     
有时，现有的代码不能完全满足您的需求。这时，您需要通过EventHandler来扩展SDDS引擎。
     
一个典型的EventHandler看起来是以下这样的：
   
```
namespace Sdds\Examples\Decode;

use Sdds\Constants\ActionTypeContants;
use Sdds\Constants\EventTypeConstants;
use Sdds\Dispatcher\EventHandler;
use Sdds\Dispatcher\EventHandlerInterface;

/**
 * Class StreamHandler
 * @package Sdds\Examples\Decode
 * @desc 注意：类名最好写明你要侦听哪一类的事件
 */
class StreamHandler extends EventHandler implements EventHandlerInterface
{
    /**
     * Listener constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function eventType(){
        //这里返回你要侦听事件的类型
        return EventTypeConstants::EVENT_STREAM;
    }

    /**
     * @return mixed
     */
    public function actionType(){
        //这里返回操作类型，是解码(INPUT)
        return ActionTypeContants::INPUT;
    }

    /**
     * 因为，这里是解码，即侦听的是InputStream, 所以，当前实现的函数肯定全部都是以read开头。
     * 但是，如果是实现的是before_action或after_action则不是必须。
     * @param $stream
     * @param $length
     * @desc 第一个参数总是你要侦听的对象本身，后面则是函数必须的参数
     */
    public function readSome($stream, $length){
        //这里实现你的代码
    }
     

}
``` 
   
EventHandler有四种类型，具体可以参见Sdds\Constants\EventTypeConstants
    
分别用来满足不同的需求：
    
- DateNode: 当我们需要增加一种节点的逻辑处理的时候。
- Packet：当我们要扩展数据包处理的时候。
- Stream：当我们要增加一种自定类型，或者要增加before_action或after_action时
- Bitwise：当我们想对某节点增加一种位操作时。   

当我们有了这些EventHandler之后，我们就要在使用时，注册这些EventHandler。
    
这时，DecoderChannel像下面这样；
    
```
namespace Sdds\Examples\Decode;

use Sdds\Channels\InputChannel;
use Sdds\Channels\ChannelInterface;

class DecodeChannel extends InputChannel implements ChannelInterface
{
    /**
     * @return bool
     */
    public function registerHandlers(){
        //获取事件分发器
        $dispatcher = $this->getDispatcher();
        //创建并注册已经实现的EventHandler 
        $stream_handler = new $StreamHandler();
        $dispatcher->registerHandler($stream_handler)
    }
}
```       

更多请参考examples目录中的源码。
   
## 贡献
    
详细请参阅 [CONTRIBUTING](CONTRIBUTING.md) 和 [CONDUCT](CONDUCT.md) 。
    
## 安全
    
如果你发现任何安全相关缺陷，请在issue跟踪中提交缺陷
    
## Credits

- [ByteFerry][link-author]
- [All Contributors][link-contributors]

## 版权
   
本开源使用 MIT License (MIT)。 更多信息请参阅 [License File](LICENSE.md)。
    
[ico-version]: https://img.shields.io/packagist/v/byteferry/sdds_php.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/byteferry/sdds_php/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/byteferry/sdds_php.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/byteferry/sdds_php.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/byteferry/sdds_php.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/byteferry/sdds_php
[link-travis]: https://travis-ci.org/byteferry/sdds_php
[link-scrutinizer]: https://scrutinizer-ci.com/g/byteferry/sdds_php/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/byteferry/sdds_php
[link-downloads]: https://packagist.org/packages/byteferry/sdds_php
[link-author]: https://github.com/byteferry
[link-contributors]: ../../contributors
[link-pecl-php-operator]:https://github.com/php/pecl-php-operator
