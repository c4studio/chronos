<?php

namespace Chronos\Scaffolding\Services;

class RouteMapService
{
    protected static $modelMap = [];
    protected static $typeMap = [];

    /**
     * Register new mapping
     *
     * @param $action
     * @param $type
     * @param null $id
     */
    public static function add($action, $type, $id = null)
    {
        if (!is_null($id))
            self::$modelMap[$type][$id] = $action;
        else
            self::$typeMap[$type] = $action;
    }

    /**
     * Return mapping
     *
     * @param $type
     * @param null $id
     * @return mixed|null
     */
    public static function get($type, $id = null)
    {
        if (!is_null($id) && isset(self::$modelMap[$type][$id]))
            return action(self::$modelMap[$type][$id]);
        elseif (isset(self::$typeMap[$type]))
            return action(self::$typeMap[$type]);

        return null;
    }
}