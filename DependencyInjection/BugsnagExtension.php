<?php
/*
 * This file is part of the Evolution7BugsnagBundle.
 *
 * (c) Evolution 7 <http://www.evolution7.com.au>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Evolution7\BugsnagBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BugsnagExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        //API Key is required
        if (!isset($config['api_key'])) {
            throw new \InvalidArgumentException('You need to provide an API key');
        }
        $container->setParameter('bugsnag.api_key', $config['api_key']);

        // Enabled stages, default is prod
        $container->setParameter('bugsnag.enabled_stages', $config['enabled_stages']);

        // Notify stages, default is staging and production
        $container->setParameter('bugsnag.notify_stages', $config['notify_stages']);

        if (!empty($config['user']) && is_string($config['user'])) {
            $container->setParameter('bugsnag.user', $config['user']);
        }

        // App Version
        if (isset($config['app_version'])) {
            $container->setParameter('bugsnag.app_version', $config['app_version']);
        }

        //Release stage class
        if (isset($config['release_stage']) && is_array($config['release_stage']) && isset($config['release_stage']['class'])) {
            $container->setParameter('bugsnag.release_stage.class', $config['release_stage']['class']);
        } else {
            $container->setParameter('bugsnag.release_stage.class', 'Evolution7\BugsnagBundle\ReleaseStage\ReleaseStage');
        }

        //Proxy information, don't set it if not present
        if (isset($config['proxy']) && is_array($config['proxy'])) {
            $container->setParameter('bugsnag.proxy', $config['proxy']);
        }
    }
}
