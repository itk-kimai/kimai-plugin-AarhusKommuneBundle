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
use App\Timesheet\TimesheetService;
use KimaiPlugin\AarhusKommuneBundle\Helper\TimesheetHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class TimesheetSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TimesheetHelper $timesheetHelper,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TimesheetMetaDefinitionEvent::class => ['onTimesheetMetaDefinition'],
            KernelEvents::REQUEST => ['onKernelRequest'],
        ];
    }

    /**
     * Event handler for TimesheetService::prepareNewTimesheet.
     *
     * @see TimesheetService::prepareNewTimesheet().
     */
    public function onTimesheetMetaDefinition(TimesheetMetaDefinitionEvent $event): void {
        $timesheet = $event->getEntity();

        $this->timesheetHelper->setDefaultProject($timesheet);
    }

    /**
     * Make sure that user has at least one timesheet when using Quick entry.
     */
    public function onKernelRequest(RequestEvent $event): void {
        $request = $event->getRequest();
        if (Request::METHOD_GET === $request->getMethod()) {
            $attributes = $request->attributes;
            $route = $attributes->get('_route');
            if ('quick_entry' === $route) {
                // @see App\Controller\QuickEntryController::quickEntry
                $begin = $attributes->get('_route_params')['begin'] ?? null;
                try {
                    $this->timesheetHelper->ensureUserTimesheet(begin: $begin);
                } catch (\Exception) {
                    // Ignore all exceptions.
                }
            }
        }
    }
}
