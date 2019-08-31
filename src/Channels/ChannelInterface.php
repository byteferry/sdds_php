<?php

/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\Channels;

/**
 * Class ChannelAggregateInterface
 * @package Sdds\Channels
 */
interface ChannelInterface
{

    /**
     * if we have the class of handlers, we must register the handlers.
     */
    public function registerHandlers();
}