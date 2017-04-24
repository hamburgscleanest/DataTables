<?php

namespace hamburgscleanest\DataTables\Helpers;

/**
 * Class SessionHelper
 * @package hamburgscleanest\DataTables\Helpers
 */
class SessionHelper {

    const SESSION_STORAGE = 'data-tables.';

    /**
     * Save the state for the given key.
     *
     * @param string $key
     * @param mixed $sessionValue
     */
    public function saveState(string $key, $sessionValue) : void
    {
        \request()->session()->put($this->_getFormattedKey($key), $sessionValue);
    }

    /**
     * @param string $key
     * @return string
     */
    private function _getFormattedKey(string $key) : string
    {
        return
            self::SESSION_STORAGE .
            \preg_replace(
                '/\.|\//',
                '_',
                \preg_replace('/(http|https):\/\//', '', \request()->url())
            ) .
            '.' . $key;
    }

    /**
     * Get the state for the given key.
     *
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function getState(string $key, $default = null)
    {
        return \request()->session()->get($this->_getFormattedKey($key)) ?? $default;
    }

    /**
     * Remove the state for the given key.
     *
     * @param string $key
     */
    public function removeState(string $key) : void
    {
        \request()->session()->remove($this->_getFormattedKey($key));
    }
}