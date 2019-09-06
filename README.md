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

A PHP library to work with stream decode and encode according to the SDDS Specification.
  
## Introduction

[中文文档](https://github.com/byteferry/sdds_php/blob/master/README_cn.md)

### What is SDDS?
    
* Data Structure
 
SDDS is  short word of "stream data dynamic structure".
  
* DSL
  
SDDS is a DSL (domain-specific language) .
  
The schema of SDDS is human-readable  and machine and machine-readable. 
  
* Parse Engine
   
SDDS is a parse engine coded according the SDDS Specification. You can rapid complete the developing of the binary communication program.  
  
 
### Features
  
- Multi-protocol support
 
SDDS PHP could enabled the programs to support multiple communication protocols. Each protocol is a channel of the program.
 
- Data types
    
Support all data types required by the SDDS specification.

- Endianness support

You only need to configure Endianness(Big-Endian, Little-Endian) in the SDDS Schema. If there are individual endianness that are inconsistent, you can also configure them to the corresponding nodes.
    
- Charset support
   
By default, UTF-8 is used, and you can also specify different encodings.
    
- Event extension
    
All custom functions can be implemented with the corresponding EventHandler.
    
- Formula expression
 
 Formula expression is supported before encoding  or after decoding.

- Easy to debug

The default integration larapack/dd, you can use the browser to debug requests and output to the page via dd.  

### Why SDDS?
   
Usually we have to write different programs for different communication protocols. However, if you use SDDS, the programs you basically use are the same. And, SDDS has been implemented for you.
    
All you have to do is expand the data stream operations, data types, bit operations, etc. that are not implemented by SDDS. And, through the JSON definition of SDDS Schema. When protocol had been changed, you only need to modify the SDDS Schema without modifying your code.
    
SDDS can greatly speed up your development.
  
## Quick tutorial

## Install

Via Composer

``` bash
$ composer require byteferry/sdds_php:dev-master
```

## Usage

- Step 1: write SDDS Schema according to [SDDS Specification](https://github.com/byteferry/sdds/blob/master/README.md) .
  
- Step 2: Write the code to call SDDS PHP
  
1. implement the Channel class:
    
For example: the channel class is named DecoderChannel.
    
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
        //If there is no Event, then return directly
        return;
    }
}
```    
  
2. Call your DecodeChannel
 
```
    $channel = DecodeChannel::getInstance();
    $packet_decoder = $channel->getDecoder($your_channel_name,$your_schema_file);
    $data = $packet_decoder->decode($stream_data);
    
```  
    
3. Debug your SDDS Schema with DD using the http method through the browser. (To facilitate rapid development, we integrated Larapack/dd)
  
4. Integrate with your communication program.
  
### Advanced usage
     
Sometimes the existing code doesn't fully meet your needs. At this point, you need to extend the SDDS engine with EventHandler.
     
A typical EventHandler looks like this:
   
```
namespace Sdds\Examples\Decode;

use Sdds\Constants\ActionTypeContants;
use Sdds\Constants\EventTypeConstants;
use Sdds\Dispatcher\EventHandler;
use Sdds\Dispatcher\EventHandlerInterface;

/**
 * Class StreamHandler
 * @package Sdds\Examples\Decode
 * @desc Note: The class name is best to indicate which type of event you want to listen to.
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
        //Return the type of event you want to listen to
        return EventTypeConstants::EVENT_STREAM;
    }

    /**
     * @return mixed
     */
    public function actionType(){
        //Returns the operation type, which is decoding (INPUT)
        return ActionTypeContants::INPUT;
    }

    /**
     * Because, here is the decoding, that is, the InputStream is being listened to, so all the currently implemented functions must start with read.
     * However, it is not necessary if the implementation is before_action or after_action.
     * @param $stream
     * @param $length
     * @desc The first parameter is always the object you want to listen to, followed by the parameters that the function must have.
     */
    public function readSome($stream, $length){
        //TODO: implement your code here.
    }
     

}
``` 
   
There are 4 kind of EventHandler, See Sdds\Constants\EventTypeConstants for details.
    
We use them to meet different needs:
    
- DateNode: When we need to add a logical processing of a node.
  
- Packet: When we want to extend packet processing.
  
- Stream: When we want to add a custom type, or to add before_action or after_action
  
- Bitwise: When we want to add a bit operation to a node.

After we have these EventHandlers, we need to register these EventHandlers before using.
    
Then, the DecoderChannel looks like this:
    
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
        //Get event dispatcher
        $dispatcher = $this->getDispatcher();
        //Create and register an already implemented EventHandler
        $stream_handler = new $StreamHandler();
        $dispatcher->registerHandler($stream_handler)
    }
}
```       

Please see the source code in the examples directory for more details.

## Contributing
  
Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.
  
## Security
  
If you discover any security related issues, please create an issue in the issue tracker.
  
## Credits
   
- [ByteFerry][link-author]
- [All Contributors][link-contributors]
   
## License
    
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
      
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
