<?php
/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdds\Examples;

use Sdds\Constants\ActionTypeContants;
use Sdds\Constants\EventTypeConstants;
use Sdds\Dispatcher\EventHandler;
use Sdds\Dispatcher\EventHandlerInterface;

class PacketHandler extends EventHandler implements EventHandlerInterface
{
    /**
     * Listener constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function eventType(){
        return EventTypeConstants::EVENT_PACKET;
    }

    /**
     * @return mixed
     */
    public function actionType(){
        return ActionTypeContants::OUTPUT;
    }
}