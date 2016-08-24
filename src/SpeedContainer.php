<?php

/**
 * This file is part of the Speedwork package.
 *
 * @link http://github.com/speedwork
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */
namespace Speedwork\Container;

/**
 * @author sankar <sankar.suda@gmail>
 */
class SpeedContainer extends PimpleContainer
{
    /**
     * All of the registered service providers.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * The current globally available container (if any).
     *
     * @var static
     */
    protected static $instance;

    /**
     * Set the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * Set the shared instance of the container.
     *
     * @param \Speedwork\Container\Container $container
     */
    public static function setInstance(Container $container)
    {
        static::$instance = $container;
    }

    /**
     * Registers a service provider.
     *
     * @param ServiceProvider $provider A ServiceProvider instance
     * @param array           $values   An array of values that customizes the provider
     *
     * @return static
     */
    public function register($provider, array $values = [], $force = false)
    {
        $name = is_string($provider) ? $provider : get_class($provider);

        if ($registered = $this->providers[$name] && !$force) {
            return $registered;
        }

        // If the given "provider" is a string, we will resolve it, passing in the
        // application instance automatically for the developer. This is simply
        // a more convenient way of specifying your service provider classes.
        if (is_string($provider)) {
            $provider = $this->resolveProviderClass($provider);
        }

        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }

        $provider->setContainer($this);
        $provider->setConfig($values);
        $provider->register($this);

        $this->providers[$name] = $provider;

        return $this;
    }

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param string $provider
     *
     * @return \Speedwork\Container\ServiceProvider
     */
    public function resolveProviderClass($provider)
    {
        return new $provider($this);
    }
}
