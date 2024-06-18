<?php

/*
 * This file is part of the "AarhusKommuneBundle" for Kimai.
 * All rights reserved by ITK Development (https://github.com/itk-kimai).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\AarhusKommuneBundle\Controller;

use App\Configuration\SystemConfiguration;
use App\Controller\TimesheetAbstractController;
use App\Entity\Timesheet;
use App\Form\TimesheetEditForm;
use App\Repository\TagRepository;
use App\Repository\TimesheetRepository;
use App\Timesheet\TimesheetService;
use KimaiPlugin\AarhusKommuneBundle\Configuration\AarhusKommuneConfiguration;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/timesheet', name: 'aarhus_kommune_')]
final class TimesheetController extends TimesheetAbstractController
{
    public function __construct(
        TimesheetRepository $repository,
        EventDispatcherInterface $dispatcher,
        TimesheetService $service,
        SystemConfiguration $configuration,
        TagRepository $tagRepository,
        private readonly AarhusKommuneConfiguration $pluginConfiguration,
    )
    {
        parent::__construct($repository, $dispatcher, $service, $configuration, $tagRepository);
    }

    #[Route(path: '/create', name: 'timesheet_create', methods: ['GET', 'POST'])]
    #[IsGranted('create_own_timesheet')]
    public function createAction(Request $request): Response
    {
        return $this->create($request);
    }

    // Lifted in bits and pieces from AbstractController::create().
    protected function create(Request $request): Response
    {
        $entry = $this->service->createNewTimesheet($this->getUser(), $request);

        // Set our project and activity
        $project = $this->pluginConfiguration->getPrimaryProject();
        $activity = $this->pluginConfiguration->getPrimaryActivity($project);
        $entry
            ->setProject($project)
            ->setActivity($activity);

        // We don't want to prepopulate the form from the request.

        $createForm = $this->getCreateForm($entry);
        $createForm->handleRequest($request);

        if ($createForm->isSubmitted() && $createForm->isValid()) {
            try {
                $this->service->saveNewTimesheet($entry);
                $this->flashSuccess('action.update.success');

                // @todo where do we go from here?
                return $this->redirectToRoute($this->getTimesheetRoute());
            } catch (\Exception $ex) {
                $this->handleFormUpdateException($ex, $createForm);
            }
        }

        return $this->render('@AarhusKommune/timesheet/create.html.twig', [
            'page_setup' => $this->createPageSetup(),
            'route_back' => $this->getTimesheetRoute(),
            'timesheet' => $entry,
            'form' => $createForm->createView(),
            'template' => $this->getTrackingMode()->getEditTemplate(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getCreateForm(Timesheet $entry): FormInterface
    {
        $form = $this->generateCreateForm($entry, TimesheetEditForm::class, $this->generateUrl('aarhus_kommune_timesheet_create'));

        $elementsToKeep = [
            'begin_date',
            'begin_time',
            'end_time',
            'duration',
            'project',
            'activity',
            'description',
        ];
        foreach ($form->all() as $name => $element) {
            if (!\in_array($name, $elementsToKeep, true)) {
                $form->remove($name);
            }
        }

        return $form;
    }

    protected function getDuplicateForm(Timesheet $entry, Timesheet $original): FormInterface
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }
}
