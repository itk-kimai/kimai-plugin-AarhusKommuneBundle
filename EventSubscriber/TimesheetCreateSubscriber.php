<?php

/*
 * This file is part of the "AarhusKommuneBundle" for Kimai.
 * All rights reserved by ITK Development (https://github.com/itk-kimai).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\AarhusKommuneBundle\EventSubscriber;

use App\Event\TimesheetMetaDefinitionEvent;
use KimaiPlugin\AarhusKommuneBundle\Configuration\AarhusKommuneConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class TimesheetCreateSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly AarhusKommuneConfiguration $configuration,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TimesheetMetaDefinitionEvent::class => ['onTimesheetMetaDefinition'],
        ];
    }

    public function onTimesheetMetaDefinition(TimesheetMetaDefinitionEvent $event): void {
        $timesheet = $event->getEntity();

        try {
            $project = $this->configuration->getPrimaryProject();
            $timesheet->setProject($project);
            $activity = $this->configuration->getPrimaryActivity($project);
            $timesheet->setActivity($activity);
        } catch (\Exception $e) {
            // Ignore all exceptions.
        }
    }
}
