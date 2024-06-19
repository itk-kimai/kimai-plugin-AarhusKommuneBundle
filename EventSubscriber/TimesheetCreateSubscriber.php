<?php

/*
 * This file is part of the "AarhusKommuneBundle" for Kimai.
 * All rights reserved by ITK Development (https://github.com/itk-kimai).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\AarhusKommuneBundle\EventSubscriber;

use KimaiPlugin\AarhusKommuneBundle\Configuration\AarhusKommuneConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

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
            KernelEvents::REQUEST => ['onKernelRequest'],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // ignore sub-requests
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        if ('timesheet_create' === $route) {
            try {
                if (!$request->query->has('project')) {
                    $project = $this->configuration->getPrimaryProject();
                    $request->query->set('project', $project->getId());

                    if (!$request->query->has('activity')) {
                        $activity = $this->configuration->getPrimaryActivity($project);
                        $request->query->set('activity', $activity->getId());
                    }
                }
            } catch (\Exception $e) {
                // Ignore all exceptions.
            }
        }
    }
}
