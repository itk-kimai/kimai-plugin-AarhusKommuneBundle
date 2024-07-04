<?php

/*
 * This file is part of the "AarhusKommuneBundle" for Kimai.
 * All rights reserved by ITK Development (https://github.com/itk-kimai).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\AarhusKommuneBundle\EventSubscriber;

use App\Event\SystemConfigurationEvent;
use App\Form\Model\Configuration;
use App\Form\Model\SystemConfiguration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class SystemConfigurationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemConfigurationEvent::class => ['onSystemConfiguration', 100],
        ];
    }

    public function onSystemConfiguration(SystemConfigurationEvent $event): void
    {
        $event->addConfiguration(
            (new SystemConfiguration('aarhuskommune_config'))
                ->setConfiguration([
                    (new Configuration('aarhuskommune.meta-title'))
                        ->setTranslationDomain('system-configuration')
                        ->setType(TextType::class)
                        ->setOptions([
                            'required' => false,
                        ]),
                    (new Configuration('aarhuskommune.login_message'))
                        ->setTranslationDomain('system-configuration')
                        ->setType(TextareaType::class),
                    (new Configuration('aarhuskommune.social_login_title'))
                        ->setTranslationDomain('system-configuration')
                        ->setType(TextType::class)
                        ->setOptions([
                            'required' => false,
                        ]),
                    (new Configuration('aarhuskommune.help_url'))
                        ->setTranslationDomain('system-configuration')
                        ->setType(UrlType::class)
                        ->setOptions([
                            'required' => false,
                        ]),
                ])
        );
    }
}
