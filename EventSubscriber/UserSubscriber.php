<?php

/*
 * This file is part of the "AarhusKommuneBundle" for Kimai.
 * All rights reserved by ITK Development (https://github.com/itk-kimai).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\AarhusKommuneBundle\EventSubscriber;

use App\Entity\User;
use App\Entity\UserPreference;
use App\Event\UserCreatePreEvent;
use KimaiPlugin\AarhusKommuneBundle\Configuration\AarhusKommuneConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly AarhusKommuneConfiguration $configuration
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserCreatePreEvent::class => ['onUserPreCreate'],
        ];
    }

    public function onUserPreCreate(UserCreatePreEvent $event): void {
        $user = $event->getUser();
        // Set all user wizards as seen.
        foreach (User::WIZARDS as $wizard) {
            $user->setWizardAsSeen($wizard);
        }

        $defaults = $this->configuration->getUserDefaults();

        $user->setLanguage($defaults[UserPreference::LANGUAGE] ?? 'da');
        $user->setLocale($defaults[UserPreference::LOCALE] ?? 'da');
        $user->setTimezone($defaults[UserPreference::TIMEZONE] ?? 'Europe/Copenhagen');
        $user->setPreferenceValue(UserPreference::SKIN, $defaults[UserPreference::SKIN] ?? 'default');
        $user->setPreferenceValue('login_initial_view', $defaults['login_initial_view'] ?? 'quick_entry');
    }
}
