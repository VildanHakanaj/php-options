<?php

declare(strict_types=1);

namespace VildanHakanaj;

class Options extends Collection
{

    /**
     * Static constructor with array
     * @param array $options
     * @return Options
     */
    public static function fromArray(array $options): Options
    {
        return new self($options);
    }

    /**
     * Get all the options as an array
     * @return array
     */
    public function all(): array
    {
        return $this->options;
    }

    /**
     * Returns all the keys of the options array
     * @return int[]|string[]
     */
    public function keys(): array
    {
        return array_keys($this->options);
    }

    /**
     * Return all the values in the options array.
     * @return array
     */
    public function values(): array
    {
        return array_values($this->options);
    }

    /**
     * @param callable|null $func
     * @return array
     */
    public function filter(callable $func = null): array
    {
        if (is_null($func)) {
            return array_filter($this->options);
        }

        return array_filter($this->options, $func);
    }

    /**
     * Filters the options by key
     * @param callable $func
     * @return array
     */
    public function filterByKey(callable $func): array
    {
        return array_filter($this->options, $func, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Will add the key value only if the key is not already in the options array.
     * @param string $key
     * @param $value
     * @return $this
     */
    public function addIfUnique(string $key, $value): Options
    {
        if ($this->has($key)) {
            return $this;
        }

        $this->options[$key] = $value;
        return $this;
    }

    /**
     * Merges the given array into the options array.
     * If the given array has existing keys in the
     * options array it will override them.
     * @param array $options
     * @return $this
     */
    public function merge(array $options): Options
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * Merges the given key and value to the existing options array.
     * If the key already exist in the array it will override it the existing one.
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function mergeKey(string $key, $value): Options
    {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * Overrides the options array to the new provided options
     * @param array $options
     * @return $this
     */
    public function override(array $options): Options
    {
        $this->options = $options;
        return $this;
    }

    public function isEnabled(string $key, bool $default = false): bool
    {
        return $this->boolean($key, $default);

    }

    public function isDisabled(string $key, bool $default = true): bool
    {
        return $this->boolean($key, $default);
    }

    public function boolean(string $key, bool $default = true): bool
    {
        if (!$this->has($key)) {
            return $default;
        }

        return filter_var($this->options[$key], FILTER_VALIDATE_BOOL);
    }


    /**
     * Convert the options into json
     * @return false|string
     */
    public function toJson($flags = 0): mixed
    {
        return json_encode($this->options, $flags);
    }
}