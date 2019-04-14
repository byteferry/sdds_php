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
 * Class SchemaException
 * @package Sdds\Exceptions
 */
class SchemaException extends SchemaExceptionAbstract
{
    /**
     * @param string $msg
     * @return SchemaException
     */
    public static function schemaFileReadFail($msg=''){
        return new self('Schema File Read Fail!  '.$msg);
    }

    /**
     * @param $type
     * @return SchemaException
     */
    public static function typeNotFound($type){
        return new self(sprintf('The type %s not found in schema!',$type));
    }

    /**
     * @return SchemaException
     */
    public static function schemaIsNotValid(){
        return new self('json_decode fail because schema is not valid!');
    }

}