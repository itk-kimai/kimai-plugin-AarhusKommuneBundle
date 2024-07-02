<?php

/*
 * This file is part of the "AarhusKommuneBundle" for Kimai.
 * All rights reserved by ITK Development (https://github.com/itk-kimai).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\AarhusKommuneBundle\TrackingMode;

use App\Entity\Timesheet;
use App\Timesheet\RoundingService;
use App\Timesheet\TrackingMode\AbstractTrackingMode;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

final class NoTrackingMode extends AbstractTrackingMode
{
    public function __construct(
        private readonly RoundingService $rounding
    )
    {
    }

    public function canEditBegin(): bool
    {
        return true;
    }

    public function canEditEnd(): bool
    {
        return true;
    }

    public function canEditDuration(): bool
    {
        return true;
    }

    public function canUpdateTimesWithAPI(): bool
    {
        return true;
    }

    public function getId(): string
    {
        return 'no_tracking';
    }

    public function canSeeBeginAndEndTimes(): bool
    {
        return true;
    }

    public function getEditTemplate(): string
    {
        return 'timesheet/edit-default.html.twig';
    }

    public function create(Timesheet $timesheet, ?Request $request = null): void
    {
        parent::create($timesheet, $request);

        if (null === $timesheet->getBegin()) {
            $timesheet->setBegin(new DateTime('now', $this->getTimezone($timesheet)));
        }
        $this->rounding->roundBegin($timesheet);

        // Set timesheet end to prevent having a running tracker.
        $begin = $timesheet->getBegin();
        if (null !== $begin) {
            $timesheet->setEnd(clone $begin);
        }
    }
}
