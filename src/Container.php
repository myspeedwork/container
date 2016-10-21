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

/**
 * @author Sankar <sankar.suda@gmail.com>
 */
class Container extends BuildContainer
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
