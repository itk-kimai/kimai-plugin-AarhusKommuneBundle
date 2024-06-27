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
use KimaiPlugin\AarhusKommuneBundle\Configuration\AarhusKommuneConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class MenuSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly AarhusKommuneConfiguration $configuration,
    )
    {
    }

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

        $configuration = $this->configuration->getMainMenu();
        if (isset($configuration['remove'])) {
            foreach ($configuration['remove'] as $spec) {
                if (isset($spec['route'])) {
                    $this->removeMenuItem($menu, route: $spec['route']);
                }
            }
        }
    }

    /**
     * Remove menu item by route name.
     */
    private function removeMenuItem(MenuItemModel $menu, string $route): void
    {
        foreach ($menu->getChildren() as $child) {
            if ($route === $child->getRoute()) {
                $menu->removeChild($child);
            } else {
                $this->removeMenuItem($child, $route);
            }
        }
    }
}
