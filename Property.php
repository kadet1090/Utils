<?php
/**
 * Copyright 2014 Kadet <kadet1090@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/legalcode CC BY-SA
 */
namespace Kadet\Utils;

trait Property {
    public function __get($name) {
        $reflection = new \ReflectionClass(get_called_class());
        try {
            $getter = '_get_'.$name;
            $method = $reflection->getMethod($getter);

            if($this->hasAccess($method, $this->getCaller()))
                return $this->$getter();
            else
                throw new \RuntimeException('Cannot access ' . ($method->isPrivate() ? 'private' : 'protected') . ' property ' . get_class($this) . '::$' . $name);
        } catch (\ReflectionException $exception) {
            if (method_exists($this, '_get'))
                return $this->_get($name);
            else
                throw new \RuntimeException('Trying to get non-existent property ' . get_class($this) . '::$' . $name);
        }
    }

    public function __set($name, $value) {
        $reflection = new \ReflectionClass(get_called_class());

        try {
            $setter = '_set_'.$name;
            $method = $reflection->getMethod($setter);

            if($this->hasAccess($method, $this->getCaller()))
                $this->$setter($value);
            else
                throw new \RuntimeException('Cannot access ' . ($method->isPrivate() ? 'private' : 'protected') . ' property ' . get_class($this) . '::$' . $name);
        } catch (\ReflectionException $exception) {
            if (method_exists($this, '_set'))
                $this->_set($name, $value);
            else
                $this->$name = $value;
        }
    }

    public function __unset($name) {
        $reflection = new \ReflectionClass(get_called_class());
        try {
            $unsetter = '_unset_'.$name;
            $method = $reflection->getMethod($unsetter);

            if($this->hasAccess($method, $this->getCaller()))
                return $this->$unsetter();
            else
                throw new \RuntimeException('Cannot unset ' . ($method->isPrivate() ? 'private' : 'protected') . ' property ' . get_class($this) . '::$' . $name);
        } catch (\ReflectionException $exception) {
            if (method_exists($this, '_unset'))
                return $this->_unset($name);
            else
                throw new \RuntimeException('Trying to unset non-existent property ' . get_class($this) . '::$' . $name);
        }
    }

    public function __isset($name) {
        $reflection = new \ReflectionClass(get_called_class());
        try {
            $issetter = '_isset_'.$name;
            $method = $reflection->getMethod($issetter);

            if($this->hasAccess($method, $this->getCaller()))
                return $this->$issetter();
            else
                return false;
        } catch (\ReflectionException $exception) {
            if (method_exists($this, '_isset'))
                return $this->_isset($name);
            else
                return false;
        }
    }

    private function hasAccess(\ReflectionMethod $method, $caller) {
        return $method->isPublic() ||
        ($method->isProtected() && $caller == get_called_class()) ||
        ($method->isPrivate() && $caller == $method->getDeclaringClass()->getName());
    }

    private function getCaller() {
        $backtrace = debug_backtrace();

        return isset($backtrace[2]['class']) ?
            $backtrace[2]['class'] :
            null;
    }
}