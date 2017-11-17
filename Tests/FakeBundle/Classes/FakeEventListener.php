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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FakeListener.
 */
class FakeEventListener implements EventSubscriberInterface
{
    const MODE = 'defaultMode';
    const TITLE = 'defaultTitle';

    public function __construct()
    {
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            'fake_event.fake_method' => array('fakeMethod'),
            'fake_event.fake_method_exception' => array('fakeMethodException'),
        );
    }

    public function fakeMethod(): void
    {
    }

    /**
     * @param FakeEvent $event
     *
     * @throws FakeException
     */
    public function fakeMethodException(FakeEvent $event): void
    {
        if (FakeEvent::ARG_A == $event->argA && FakeEvent::ARG_B == $event->argB) {
            throw new FakeException(FakeException::FAKE_MESSAGE);
        }
    }
}
