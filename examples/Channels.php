<?php
/**
 * Created by PhpStorm.
 * User: Bardo
 * Date: 2019-08-26
 * Time: 15:49
 */

namespace Sdds\Examples;


use Sdds\Channels\ChannelAggregate;
use Sdds\Channels\ChannelAggregateInterface;

class Channels extends ChannelAggregate implements ChannelAggregateInterface
{
    public function registerHandler()
    {
        // TODO: Implement registerHandler() method.
    }
}