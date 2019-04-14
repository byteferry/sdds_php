<?php

/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\FormulaEngine;

use Sdds\FormulaEngine\Exceptions\FormulaEngineException;

/**
 * Class FormulaEngine
 * @package Sdds\FormulaEngine
 */
class FormulaEngine
{

    /**
     * FormulaEngine::calculate($formula, $variables)
     * calculate the formula
     *
     * @param string $formula
     * @param array $variables
     * @return mixed
     * @throws \Exception
     */
    public static function calculate($formula, $variables = array()){

        if ( false === preg_match_all( '/(\w+)/', $formula, $result, PREG_PATTERN_ORDER ) ){
            throw FormulaEngineException::FormulaIsNotCorrect();
        }
        if(empty($variables)){
            throw FormulaEngineException::CountOfVariablesIsZero();
        }
        $keys = $result[0];

        if (!isset($variables['true'])){
            $variables['true'] = 1;
        }
        if (!isset( $variables['false'])){
            $variables['false'] = 0;
        }
        $pos = 0;
        foreach ($keys as $value){
            if ((is_numeric( $value )) || (is_callable($value))){
                continue;
            }
            if (isset($variables[$value])){
                $pos = strpos($formula, $value, $pos);
                $formula = substr_replace($formula, '$', $pos, 0);
                $pos += strlen($value) + 1;
                $var_array[$value] = $variables[$value];
            }else{
                throw FormulaEngineException::VariableNotFound($value);
            }
        }

        $formula_exp = '$return=' . $formula . ";";
        $return = 0;

        call_user_func(function()use($variables,$formula_exp,&$return){
            extract($variables,EXTR_OVERWRITE);
            @eval($formula_exp);
        });

        if ((false===$return)||(INF == $return)){
            throw FormulaEngineException::DivisionByZero();
        }
        return 0 + $return;
    }

}