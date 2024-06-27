<?php

/*
 * This file is part of the "AarhusKommuneBundle" for Kimai.
 * All rights reserved by ITK Development (https://github.com/itk-kimai).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\AarhusKommuneBundle;

use App\Plugin\PluginInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AarhusKommuneBundle extends Bundle implements PluginInterface
{
    public const PLUGIN_NAME = 'aarhus_kommune';

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new class() implements CompilerPassInterface {
            public function process(ContainerBuilder $container)
            {
                $twigFilesystemLoaderDefinition = $container->findDefinition('twig.loader.native_filesystem');

                // Prepend our custom templates in the `__main__` namespace.
                $path = __DIR__ . '/Resources/views/app';
                $twigFilesystemLoaderDefinition->addMethodCall('prependPath', [$path, '__main__']);

                // Add the original app templates in the `App` namespace (use `@App` in Twig templates).
                $path = \dirname(__DIR__, 3) . '/templates';
                $twigFilesystemLoaderDefinition->addMethodCall('prependPath', [$path, 'App']);
            }
        });
    }
}
