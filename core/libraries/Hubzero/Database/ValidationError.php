<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

/**
 * Validation error with rich metadata
 *
 * This class provides structured validation error information while
 * maintaining backward compatibility by implementing Stringable.
 *
 * ## Usage
 *
 * ```php
 * // BC: Works as string
 * echo $error;  // "title cannot be empty"
 *
 * // New: Access structured data
 * $error->getField();    // "title"
 * $error->getRule();     // "notempty"
 * $error->getMessage();  // "title cannot be empty"
 * $error->getParams();   // []
 *
 * // For JSON APIs
 * json_encode($error);   // {"field":"title","rule":"notempty",...}
 * ```
 */
class ValidationError implements \Stringable, \JsonSerializable
{
    /**
     * The field name that failed validation
     *
     * @var string
     */
    protected string $field;

    /**
     * The rule name that failed
     *
     * @var string
     */
    protected string $rule;

    /**
     * The error message
     *
     * @var string
     */
    protected string $message;

    /**
     * Parameters passed to the rule
     *
     * @var array
     */
    protected array $params;

    /**
     * The value that failed validation
     *
     * @var mixed
     */
    protected $value;

    /**
     * Create a new validation error
     *
     * @param  string  $field    The field name
     * @param  string  $rule     The rule name
     * @param  string  $message  The error message
     * @param  array   $params   Rule parameters
     * @param  mixed   $value    The value that failed
     */
    public function __construct(
        string $field,
        string $rule,
        string $message,
        array $params = [],
        $value = null
    ) {
        $this->field = $field;
        $this->rule = $rule;
        $this->message = $message;
        $this->params = $params;
        $this->value = $value;
    }

    /**
     * Get the field name
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Get the rule name
     *
     * @return string
     */
    public function getRule(): string
    {
        return $this->rule;
    }

    /**
     * Get the error message
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Get the rule parameters
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Get a specific parameter
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function getParam(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    /**
     * Get the value that failed validation
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Convert to string for BC compatibility
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->message;
    }

    /**
     * Specify data for JSON serialization
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'field' => $this->field,
            'rule' => $this->rule,
            'message' => $this->message,
            'params' => $this->params,
        ];
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->jsonSerialize();
    }
}
