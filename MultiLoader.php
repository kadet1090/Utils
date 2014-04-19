<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kacper
 * Date: 08.07.13
 * Time: 12:39
 * To change this template use File | Settings | File Templates.
 */

namespace Kadet\Utils;


class MultiLoader extends AutoLoader {
    private $mappings = [];

    /**
     * @param array $mappings Namespace => Directory mappings.
     */
    public function __construct($mappings) {
        foreach($mappings as $namespace => $directory)
            $this->mappings[$namespace.(substr($namespace, -1) == '\\' ? '' : '\\')] =
                $directory.(substr($directory, -1) == '/' ? '' : '/');
    }

    /**
     * Autoload function.
     *
     * @param string $class Class to be loaded.
     */
    public function load($class) {
        foreach($this->mappings as $namespace => $directory) {
            if(preg_match('#^'.str_replace('\\', '\\\\', $namespace).'#si', $class)) {
                $class = preg_replace('#^'.str_replace('\\', '\\\\', $namespace).'#si', '', $class);
                include_once $directory.str_replace('\\', '/', $class).'.php';
            }
        }
    }
}