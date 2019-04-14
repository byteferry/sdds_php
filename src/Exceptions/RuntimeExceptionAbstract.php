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

use RuntimeException;
use Throwable;

/**
 * Class RuntimeExceptionAbstract
 * @package Sdds\Exceptions
 */
abstract class RuntimeExceptionAbstract extends RuntimeException implements Throwable
{

}