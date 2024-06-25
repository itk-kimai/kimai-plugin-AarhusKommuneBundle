<?php

/*
 * This file is part of the "AarhusKommuneBundle" for Kimai.
 * All rights reserved by ITK Development (https://github.com/itk-kimai).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\AarhusKommuneBundle\Helper;

use App\Controller\QuickEntryController;
use App\Entity\Activity;
use App\Entity\Project;
use App\Entity\Timesheet;
use App\Entity\User;
use App\Repository\Query\TimesheetQuery;
use App\Repository\TimesheetRepository;
use App\Timesheet\DateTimeFactory;
use App\Timesheet\TimesheetService;
use KimaiPlugin\AarhusKommuneBundle\Configuration\AarhusKommuneConfiguration;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TimesheetHelper
{
    public function __construct(
        private readonly AarhusKommuneConfiguration $configuration,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly TimesheetRepository $timesheetRepository,
        private readonly TimesheetService $timesheetService,
    ) {
    }

    /**
     * Set default project on timesheet.
     */
    public function setDefaultProject(Timesheet $timesheet): Timesheet
    {
        $user = $this->getUser();
        if (null !== $user) {
            $project = $this->getDefaultProject($user);
            if (null !== $project) {
                $timesheet->setProject($project);
                $activity = $this->getDefaultActivity($project, $user);
                $timesheet->setActivity($activity);
            }
        }

        return $timesheet;
    }

    /**
     * Ensure that at least one timesheet exists for user.
     */
    public function ensureUserTimesheet(?User $user = null, ?string $begin = null): ?Timesheet
    {
        $user ??= $this->getUser();
        if (null !== $user) {
            $timesheet = $this->getUserTimesheet($user, $begin);

            if (null === $timesheet) {
                $timesheet = $this->timesheetService->createNewTimesheet($user);
                $this->timesheetService->prepareNewTimesheet($timesheet);
                $this->setDefaultProject($timesheet);
                if (null !== $timesheet->getProject() && null !== $timesheet->getActivity()) {
                    $this->timesheetRepository->save($timesheet);

                    return $timesheet;
                }
            }
        }

        return null;
    }

    /**
     * Get user timesheet.
     *
     * This code is lifted from QuickEntryController::quickEntry which see.
     *
     * @see QuickEntryController::quickEntry()
     *
     * @param User $user
     * @param string|null $begin
     * @return Timesheet|null
     */
    private function getUserTimesheet(User $user, ?string $begin): ?Timesheet
    {
        $factory = DateTimeFactory::createByUser($user);

        if ($begin !== null) {
            try {
                $begin = $factory->createDateTime($begin);
            } catch (\Exception $ex) {
                $begin = null;
            }
        }

        if ($begin === null) {
            $begin = $factory->createDateTime();
        }

        $startWeek = $factory->getStartOfWeek($begin);
        $endWeek = $factory->getEndOfWeek($begin);

        $query = new TimesheetQuery();
        $query->setBegin($startWeek);
        $query->setEnd($endWeek);
        $query->setName('quickEntryForm');
        $query->setUser($user);

        $result = $this->timesheetRepository->getTimesheetResult($query);
        $timesheets = $result->getResults(true);

        return reset($timesheets) ?: null;
    }

    /**
     * Get current user if any,
     *
     * @return User|null
     */
    private function getUser(): ?User
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        return $user instanceof User ? $user : null;
    }

    /**
     * Get default project for a user.
     */
    private function getDefaultProject(User $user): ?Project
    {
        return $this->configuration->getPrimaryProject();
    }

    /**
     * Get default activity for a project for a user.
     */
    private function getDefaultActivity(Project $project, User $user): ?Activity
    {
        return $this->configuration->getPrimaryActivity($project);
    }
}
