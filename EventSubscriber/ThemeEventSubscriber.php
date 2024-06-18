<?php

/*
 * This file is part of the "AarhusKommuneBundle" for Kimai.
 * All rights reserved by ITK Development (https://github.com/itk-kimai).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\AarhusKommuneBundle\EventSubscriber;

use App\Event\ThemeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ThemeEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ThemeEvent::STYLESHEET => ['renderStylesheet', 100],
        ];
    }

    public function renderStylesheet(ThemeEvent $event): void
    {
        // Hide the project element.
        $css = <<<'CSS'
<style>
.timesheet_edit_form_row_project {
    display: none;
}
</style>
CSS;
        $event->addContent($css);
    }
}
