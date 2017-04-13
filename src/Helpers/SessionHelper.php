<?php

namespace hamburgscleanest\DataTables\Helpers;

use Illuminate\Http\Request;

/**
 * Class SessionHelper
 * @package hamburgscleanest\DataTables\Helpers
 */
class SessionHelper {

    const SESSION_STORAGE = 'data-tables.';

    /** @var Request */
    private $_request;

    /**
     * SessionHelper constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->_request = $request;
    }

    /**
     * Save the state for the given key.
     *
     * @param string $key
     * @param mixed $sessionValue
     */
    public function saveState(string $key, $sessionValue)
    {
        $this->_request->session()->put($this->_getFormattedKey($key), $sessionValue);
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
                \preg_replace('/(http|https):\/\//', '', $this->_request->url())
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
        return $this->_request->session()->get($this->_getFormattedKey($key)) ?? $default;
    }

    /**
     * Remove the state for the given key.
     *
     * @param string $key
     */
    public function removeState(string $key)
    {
        $this->_request->session()->remove($this->_getFormattedKey($key));
    }
}