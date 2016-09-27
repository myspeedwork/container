<?php

/*
 * This file is part of the Speedwork package.
 *
 * (c) Sankar <sankar.suda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Speedwork\Container;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author sankar <sankar.suda@gmail.com>
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
     * The paths that should be published.
     *
     * @var array
     */
    protected static $publishes = [];

    /**
     * The paths that should be published by group.
     *
     * @var array
     */
    protected static $publishGroups = [];

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

    /**
     * Get the service provider settings.
     *
     * @param string $name   Name of the config
     * @param string $option Name in app
     *
     * @return mixed Configuration
     */
    protected function getSettings($name = null, $option = null)
    {
        $key = $option ?: $name;
        if ($this->app[$key]) {
            return $this->app[$key];
        }

        return $this->app['config'][$name];
    }

    protected function setRoutes($routes = [])
    {
        return $this->config('router.router.routes', $routes);
    }

    protected function config($key, $values = [])
    {
        $config = $this->app['config']->get($key);
        if (is_array($config)) {
            $values = array_replace_recursive($config, $values);
        }

        return $this->app['config']->set($key, $values);
    }

    /**
     * Register paths to be published by the publish command.
     *
     * @param array  $paths
     * @param string $group
     */
    protected function publishes(array $paths, $group = null)
    {
        $class = static::class;

        if (!array_key_exists($class, static::$publishes)) {
            static::$publishes[$class] = [];
        }

        static::$publishes[$class] = array_merge(static::$publishes[$class], $paths);

        if ($group) {
            if (!array_key_exists($group, static::$publishGroups)) {
                static::$publishGroups[$group] = [];
            }

            static::$publishGroups[$group] = array_merge(static::$publishGroups[$group], $paths);
        }
    }

    /**
     * Get the paths to publish.
     *
     * @param string $provider
     * @param string $group
     *
     * @return array
     */
    public static function pathsToPublish($provider = null, $group = null)
    {
        if ($provider && $group) {
            if (empty(static::$publishes[$provider]) || empty(static::$publishGroups[$group])) {
                return [];
            }

            return array_intersect_key(static::$publishes[$provider], static::$publishGroups[$group]);
        }

        if ($group && array_key_exists($group, static::$publishGroups)) {
            return static::$publishGroups[$group];
        }

        if ($provider && array_key_exists($provider, static::$publishes)) {
            return static::$publishes[$provider];
        }

        if ($group || $provider) {
            return [];
        }

        $paths = [];

        foreach (static::$publishes as $publish) {
            $paths = array_merge($paths, $publish);
        }

        return $paths;
    }

    /**
     * Register the package's custom Speedwork commands.
     *
     * @param array|mixed $commands
     */
    protected function commands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();

        // To register the commands with Speedwork, we will grab each of the arguments
        // passed into the method and listen for Speedwork "init" event which will
        // give us the Speedwork console instance which we will give commands to.
        $this->app['events']->addListener('console.init.event', function (Event $event) use ($commands) {
            $event->getConsole()->resolveCommands($commands);
        });
    }

    /**
     * Register a database migration path.
     *
     * @param array|string $paths
     */
    protected function loadMigrationsFrom($paths)
    {
        $this->app['migrator']->path($paths);
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
