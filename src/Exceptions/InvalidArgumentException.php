<?php

/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\Exceptions;

/**
 * Class InvalidArgumentException
 * @package Sdds\Exceptions
 */
class InvalidArgumentException extends InvalidArgumentExceptionAbstract
{

    /**
     * @return InvalidArgumentException
     */
    public static function StreamMustBeResource(){
        return new self('Stream must be a resource');
    }

    /**
     * @param null $type
     * @return InvalidArgumentException
     */
    public static function InvalidResourceType($type=null){
        if (null==$type){
            return new self('Invalid resource type');
        }
        return new self(sprintf('Invalid resource type: %s', $type));
    }

    /**
     * @param $name
     * @return InvalidArgumentException
     */
    public static function PropertyNotFound($name){
        return new self(sprintf('Property %s not found.', $name));
    }

    /**
     * @return InvalidArgumentException
     */
    public static function LengthCanNotBeZero(){
        return new self('The argument length can not be zero.');
    }

}