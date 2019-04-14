<?php

/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\FormulaEngine\Exceptions;

/**
 * Class FormulaEngineException
 * @package Sdds\FormulaEngine\Exceptions
 */
class FormulaEngineException extends FormulaEngineExceptionAbstract
{
    /**
     * @return FormulaEngineException
     */
    public static function FormulaIsNotCorrect(){
        return new self('Formula is not correct!');
    }

    /**
     * @return FormulaEngineException
     */
    public static function CountOfVariablesIsZero(){
        return new self('Count of variables is zero!');
    }

    /**
     * @return FormulaEngineException
     */
    public static function DivisionByZero(){
        return new self('Division by zero!');
    }

    /**
     * @param $name
     * @return FormulaEngineException
     */
    public static function VariableNotFound($name){
        return new self(sprintf('Variables %s not found!',$name));
    }
}