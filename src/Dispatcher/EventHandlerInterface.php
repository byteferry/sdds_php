<?php
/*
 * This file is part of the byteferry/sdds package.
 *
 * (c) ByteFerry <byteferry@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sdds\Dispatcher;


interface EventHandlerInterface
{

    /**
     * @return mixed
     */
    public function eventType();

    /**
     * @return mixed
     */
    public function actionType();


}