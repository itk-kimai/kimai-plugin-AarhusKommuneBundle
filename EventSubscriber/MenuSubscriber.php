<?php

/*
 * This file is part of the "AarhusKommuneBundle" for Kimai.
 * All rights reserved by ITK Development (https://github.com/itk-kimai).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\AarhusKommuneBundle\EventSubscriber;

use App\Event\ConfigureMainMenuEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class MenuSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            // Set a very low priority in the hope that we are run last.
            ConfigureMainMenuEvent::class => ['onMenuConfigure', -9999],
        ];
    }

    public function onMenuConfigure(ConfigureMainMenuEvent $event): void
    {
        $menu = $event->getMenu();
        // Remove the 'dashboard' menu item.
        foreach ($menu->getChildren() as $child) {
            if ('dashboard' === $child->getRoute()) {
                $menu->removeChild($child);
                break;
            }
        }
    }
}
