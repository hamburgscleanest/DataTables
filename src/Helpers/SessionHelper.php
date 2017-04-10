<?php

namespace hamburgscleanest\DataTables\Helpers;

use Illuminate\Http\Request;

/**
 * Class SessionHelper
 * @package hamburgscleanest\DataTables\Helpers
 */
class SessionHelper {

    const SESSION_STORAGE = 'data-tables.';

    /**
     * @param Request $request
     * @param string $key
     * @return string
     */
    private function getFormattedKey(Request $request, string $key)
    {
        return
            self::SESSION_STORAGE .
            \preg_replace(
                '/\.|\//',
                '_',
                \preg_replace('/(http|https):\/\//', '', $request->url())
            ) .
            '.' . $key;
    }

    /**
     * @param Request $request
     * @param string $key
     * @param mixed $sessionValue
     */
    public function saveState(Request $request, string $key, $sessionValue)
    {
        $request->session()->put(self::getFormattedKey($request, $key), $sessionValue);
    }

    /**
     * @param Request $request
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function getState(Request $request, string $key, $default = null)
    {
        return $request->session()->get(self::getFormattedKey($request, $key)) ?? $default;
    }

    /**
     * @param Request $request
     * @param string $key
     */
    public function removeState(Request $request, string $key)
    {
        $request->session()->remove(self::getFormattedKey($request, $key));
    }
}