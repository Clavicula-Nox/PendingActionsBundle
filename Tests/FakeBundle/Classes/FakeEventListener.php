<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FakeListener
 * @package ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes
 */
class FakeEventListener implements EventSubscriberInterface
{
    const MODE = "defaultMode";
    const TITLE = "defaultTitle";

    public function __construct()
    {

    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            'fake_event.fake_method' => array('fakeMethod'),
            'fake_event.fake_method_exception' => array('fakeMethodExcpetion'),
        );
    }

    public function fakeMethod()
    {

    }

    /**
     * @param Event $event
     * @throws FakeException
     */
    public function fakeMethodExcpetion(Event $event)
    {
        if ($event->argA == FakeEvent::ARG_A && $event->argB == FakeEvent::ARG_B) {
            throw new FakeException(FakeException::FAKE_MESSAGE);
        }
    }
}
