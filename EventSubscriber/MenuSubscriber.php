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
use App\Utils\MenuItemModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class MenuSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            // Set a very high priority in the hope that we are run first.
            ConfigureMainMenuEvent::class => ['onMenuConfigure', 10_000],
        ];
    }

    public function onMenuConfigure(ConfigureMainMenuEvent $event): void
    {
        $event->getMenu()->addChild(
            new MenuItemModel('aarhus_kommune_timesheet_create', 'Aarhus kommune', 'aarhus_kommune_timesheet_create', [], 'fas fa-snowman')
        );
    }
}
