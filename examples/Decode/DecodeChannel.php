<?php
/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\Examples\Decode;

use Sdds\Channels\InputChannel;
use Sdds\Channels\ChannelInterface;

class DecodeChannel extends InputChannel implements ChannelInterface
{
    /**
     * @return bool
     */
    public function registerHandlers(){
        return true;
    }
}