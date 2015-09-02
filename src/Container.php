<?php

/**
 * This file is part of the Speedwork package.
 *
 * (c) 2s Technologies <info@2stechno.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Speedwork\Container;

/**
 * @author sankar <sankar.suda@gmail>
 */
class Container extends PimpleContainer
{
    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id)
    {
        return isset($this[$id]);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $this[$key];
        }

        return Registry::get($key);
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function set($id, $value = null)
    {
        return $this[$id] = $value;
    }

    /**
     * Magic method to get property.
     *
     * @param string $key value to get
     *
     * @return bool
     */
    public function __set($id, $value = null)
    {
        return $this->set($id, $value);
    }

    /**
     * Magic method to get property.
     *
     * @param string $key value to get
     *
     * @return bool
     */
    public function __get($key)
    {
        return $this->get($key);
    }
}
