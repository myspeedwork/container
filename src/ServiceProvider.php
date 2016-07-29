<?php

/**
 * This file is part of the Speedwork package.
 *
 * @link http://github.com/speedwork
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Speedwork\Container;

/**
 * @author sankar <sankar.suda@gmail>
 */
abstract class ServiceProvider
{
    /**
     * The application instance.
     */
    protected $app;

    /**
     * The Service Provider config.
     */
    protected $config;

    /**
     * Create a new service provider instance.
     *
     * @param \Speedwork\Container\Container $app
     */
    public function setContainer(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Set Config.
     *
     * @param array $config [description]
     */
    public function setConfig($config = [])
    {
        $this->config = $config;
    }

    protected function getSettings($name = null)
    {
        return $this->app[$name] ?: $this->app['config'][$name];
    }

    /**
     * Register a translation file namespace.
     *
     * @param string $path
     * @param string $namespace
     */
    protected function loadTranslationsFrom($path, $namespace)
    {
        $this->app['translator']->addNamespace($namespace, $path);
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container
     */
    abstract public function register(Container $app);
}
