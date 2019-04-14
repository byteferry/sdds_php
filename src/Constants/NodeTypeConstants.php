<?php

/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\Constants;

/**
 * Class NodeTypeConstants
 * @package Sdds\Constants
 */
class NodeTypeConstants
{
    /**
     * Normal base type node
     */
    const OF_NORMAL         = 0;
    /**
     * Normal custom type node
     */
    const OF_CUSTOM         = 1;
    /**
     * Node of a byte fields
     */
    const OF_BYTE_FIELDS    = 2;
    /**
     * Node of a bit fields
     */
    const OF_BIT_FIELDS     = 3;
    /**
     * Node of the selector "One_of"
     */
    const OF_SELECTOR       = 4;
    /**
     * Node of the repeat
     */
    const OF_REPEAT         = 5;
}