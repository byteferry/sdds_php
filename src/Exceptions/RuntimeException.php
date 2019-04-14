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
 * Class RuntimeException
 * @package Sdds\Exceptions
 */
class RuntimeException extends RuntimeExceptionAbstract
{
    /**
     * @return RuntimeException
     */
    public static function BufferAllocationFailed()
    {
        return new self('Buffer allocation failed');
    }

    /**
     * @param $method_name
     * @return RuntimeException
     */
    public static function MethodNotExists($method_name)
    {
        return new self(sprintf('Method %s not exists.', $method_name));
    }

    /**
     * @return RuntimeException
     */
    public static function ObjectIsReadOnly()
    {
        return new self('Object is read only');
    }

    /**
     * @return RuntimeException
     */
    public static function StreamIsClosed()
    {
        return new self('Cannot operate on a closed stream');
    }

    /**
     * @param $name
     * @return RuntimeException
     */
    public static function NodeNotFound($name)
    {
        return new self(sprintf('The node %s not found.', $name));
    }

    /**
     * @return RuntimeException
     */
    public static function StreamIsEnd()
    {
        return new self('Reading is after the end of stream!');
    }

    /**
     * @param $reading_length
     * @param $need_length
     * @return RuntimeException
     */
    public static function StreamReadFail($reading_length, $need_length)
    {
        return new self(sprintf('Read the stream fail. The reading length is %s. The need length is %s.', $reading_length, $need_length));
    }

    /**
     * @param $id
     * @return RuntimeException
     */
    public static function nodeOfIdHasSet($id)
    {
        return new self(sprintf('The node selector of id %s has set.', $id));
    }

    /**
     * @param $id
     * @return RuntimeException
     */
    public static function nodeOfIndexNotFound($id)
    {
        return new self(sprintf('The node of item id %s not found.', $id));
    }

    /**
     * @return RuntimeException
     */
    public static function DataIsEndOfTheBits()
    {
        return new self('The Data Is End Of the Bits!');
    }

    /**
     * @param $name
     * @return RuntimeException
     */
    public static function optionItemUndefined($name)
    {
        return new self(sprintf('The option item  %s is undefined!.', $name));
    }

    /**
     * @param $name
     * @return RuntimeException
     */
    public static function sddsSchemaItemIsUndefined($name)
    {
        return new self(sprintf('The sdds schema item  %s is undefined!.', $name));
    }

    /**
     * @param $id
     * @return RuntimeException
     */
    public static function ValueIsRequired($id)
    {
        return new self(sprintf('The value of %s is required!.', $id));
    }

    /**
     * @param $id
     * @return RuntimeException
     */
    public static function InstanceOfIndexHasSet($id)
    {
        return new self(sprintf('The Instance of item id %s has set!.', $id));
    }

    /**
     * @param $id
     * @return RuntimeException
     */
    public static function ThereIsAlreadyAnEntryForId($id)
    {
        return new self(sprintf("There is already an entry for id %s ", $id));
    }

    /**
     * @param $id
     * @return RuntimeException
     */
    public static function ThereIsNoEntryForId($id)
    {
        return new self(sprintf("There is no entry for id %s ", $id));
    }

    /**
     * @param $name
     * @param $index
     * @return RuntimeException
     */
    public static function ThereIsAlreadyAnEntryForIndex($name, $index)
    {
        return new self(sprintf("There is already an entry for item %s index %s ", $name, $index));
    }

    /**
     * @param $name
     * @param $index
     * @return RuntimeException
     */
    public static function ThereIsNoEntryForIndex($name, $index)
    {
        return new self(sprintf("There no entry for item %s index %s ", $name, $index));
    }

    /**
     * @return RuntimeException
     */
    public static function missingRepeatCondition(){
        return new self("Missing repeat condition!");
    }

}