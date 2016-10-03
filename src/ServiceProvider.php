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

    /**
     * Register the routes.
     *
     * @param array $routes
     */
    protected function setRoutes($routes = [])
    {
        return $this->registerConfig('router.router.routes', $routes);
    }

    /**
     * Register the service provider configuration.
     *
     * @param array|string $key
     * @param mixed        $value
     *
     * @return [type] [description]
     */
    protected function registerConfig($key, $value = null)
    {
        if ($this->app['files']->isFile($key)) {
            $this->app['config.loader']->load($key, $value);
        } elseif (is_array($key)) {
            foreach ($key as $innerKey => $innerValue) {
                $config = $this->app['config']->get($innerKey);
                if (is_array($config)) {
                    $innerValue = array_replace_recursive($innerValue, $config);
                }

                $this->app['config']->set($innerKey, $innerValue);
            }
        } else {
            $config = $this->app['config']->get($key);
            if (is_array($config)) {
                $value = array_replace_recursive($value, $config);
            }

            $this->app['config']->set($key, $value);
        }

        return $this;
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

        if ($group) {
            if (!array_key_exists($group, static::$publishGroups)) {
                static::$publishGroups[$group] = [];
            }

            static::$publishGroups[$group] = array_merge(static::$publishGroups[$group], $paths);

            if (!array_key_exists($group, static::$publishes[$class])) {
                static::$publishes[$class][$group] = [];
            }

            static::$publishes[$class][$group] = array_merge(static::$publishes[$class][$group], $paths);
        } else {
            static::$publishes[$class] = array_merge(static::$publishes[$class], $paths);
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

            return array_intersect_key(static::$publishes[$provider], [$group => static::$publishGroups[$group]]);
        }

        if ($group && array_key_exists($group, static::$publishGroups)) {
            return [$group => static::$publishGroups[$group]];
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
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container
     */
    abstract public function register(Container $app);
}
