<?php

/*
 * This file is part of the "AarhusKommuneBundle" for Kimai.
 * All rights reserved by ITK Development (https://github.com/itk-kimai).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\AarhusKommuneBundle\Configuration;

use App\Configuration\SystemConfiguration;
use App\Entity\Activity;
use App\Entity\Project;
use App\Repository\ActivityRepository;
use App\Repository\ProjectRepository;
use KimaiPlugin\AarhusKommuneBundle\AarhusKommuneBundle;
use KimaiPlugin\AarhusKommuneBundle\Exception\RuntimeException;

final class AarhusKommuneConfiguration
{
    public const CONFIGURATION_NAME = AarhusKommuneBundle::PLUGIN_NAME;

    public function __construct(
        private readonly SystemConfiguration $configuration,
        private readonly ProjectRepository $projectRepository,
        private readonly ActivityRepository $activityRepository,
    )
    {
    }

    public function getPrimaryProject(): Project
    {
        $id = $this->findConfiguration('primary_project');

        $project = $this->projectRepository->find($id);
        if (null === $project) {
            throw new RuntimeException(sprintf('Invalid primary project id: %s', $id));
        }

        return $project;
    }

    public function getPrimaryActivity(Project $project): Activity
    {
        $id = $this->findConfiguration('primary_activity');

        $activity = $this->activityRepository->find($id);
        if (null === $activity) {
            throw new RuntimeException(sprintf('Invalid primary activity id: %s', $id));
        }

        if ($activity->getProject() !== $project) {
            throw new RuntimeException(sprintf('Activity %s (%s) does not belong to project %s (%s)', $activity->getName(), $activity->getId(), $project->getName(), $project->getId()));
        }

        return $activity;
    }

    public function getMainMenu(): array
    {
        return $this->findConfiguration('main_menu', allowEmpty: true, array: true) ?? [];
    }

    private function findConfiguration(string $name, bool $allowEmpty = false, bool $array = false): mixed
    {
        $key = self::CONFIGURATION_NAME . '.' . $name;
        $value = $array ? $this->configuration->findArray($key) : $this->configuration->find($key);

        if (!$allowEmpty && empty($value)) {
            throw new RuntimeException(sprintf('Configuration %s is not set', $key));
        }

        return $value;
    }
}
