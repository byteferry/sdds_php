<?php
/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\Examples\Encode;

use Sdds\Channels\OutputChannel;
use Sdds\Channels\ChannelInterface;

class EncoderChannel  extends OutputChannel implements ChannelInterface
{
    /**
     * @return bool
     */
    public function registerHandlers(){
        return true;
    }
}