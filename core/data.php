<?php

namespace Core;

abstract class Data {
    protected static array $data = [
        "queries" => [],
        "urls" => [],
        "headers" => [],
        "keys" => [],
        "data" => [],
        "records" => [],
    ];
    protected static array $lockedKeys = [];

    final protected static function setData(string $key, $value): void {
        if (self::isLocked($key) && !self::hasData($key)) {
            return;
        }

        $segments = explode(".", $key);
        $ref = &self::$data;

        foreach ($segments as $segment) {
            if (!isset($ref[$segment]) || !is_array($ref[$segment])) {
                $ref[$segment] = [];
            }
            $ref = &$ref[$segment];
        }

        $ref = $value;
    }

    final protected static function getData(string $key, $default = null): mixed {        
        $segments = explode(".", $key);
        $ref = self::$data;

        foreach ($segments as $segment) {
            if (!isset($ref[$segment])) {
                return $default;
            }
            $ref = $ref[$segment];
        }
        return $ref;
    }

    final protected static function hasData(string $key): bool {
        $segments = explode(".", $key);
        $ref = self::$data;

        foreach ($segments as $segment) {
            if (!array_key_exists($segment, $ref)) {
                return false;
            }
            $ref = $ref[$segment];
        }

        return true;
    }

    final protected static function allData(): array {        
        return self::$data;
    }

    final protected static function clearData(): void {        
        self::$data = [];
        self::$lockedKeys = [];
    }

    final protected static function lockData(string $key): void {        
        if (!in_array($key, self::$lockedKeys)) {
            self::$lockedKeys[] = $key;
        }
    }

    final protected static function isLocked(string $key): bool {
        return in_array($key, self::$lockedKeys);
    }

    final protected static function forgetData(string $key): void {
        $segments = explode(".", $key);
        $ref = &self::$data;

        foreach ($segments as $i => $segment) {
            if (!isset($ref[$segment])) {
                return;
            }

            if ($i === count($segments) - 1) {
                unset($ref[$segment]);
                return;
            }

            $ref = &$ref[$segment];
        }
    }
}


?>