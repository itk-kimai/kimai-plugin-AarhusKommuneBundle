<?php

/*
 * This file is part of the "AarhusKommuneBundle" for Kimai.
 * All rights reserved by ITK Development (https://github.com/itk-kimai).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\AarhusKommuneBundle\Helper;

use App\Entity\Activity;
use App\Entity\Project;
use App\Entity\Timesheet;
use App\Entity\User;
use App\Repository\TimesheetRepository;
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
    public function ensureUserTimesheet(?User $user = null): ?Timesheet
    {
        $user ??= $this->getUser();
        if (null !== $user) {
            $timesheet = $this->timesheetRepository->findOneBy(['user' => $user]);

            if (null !== $timesheet) {
                return $timesheet;
            }

            $timesheet = $this->timesheetService->createNewTimesheet($user);
            $this->timesheetService->prepareNewTimesheet($timesheet);
            $this->setDefaultProject($timesheet);
            if (null !== $timesheet->getProject() && null !== $timesheet->getActivity()) {
                $this->timesheetRepository->save($timesheet);

                return $timesheet;
            }
        }

        return null;
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
