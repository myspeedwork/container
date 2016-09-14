<?php

/*
 * This file is part of the Speedwork package.
 *
 * (c) Sankar <sankar.suda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Speedwork\Container;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Interface for event listener providers.
 *
 * @author Sankar <sankar.suda@gmail.com>
 */
interface EventListenerInterface
{
    public function subscribe(Container $app, EventDispatcherInterface $dispatcher);
}
