<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

/**
 * Database validation class
 *
 * Provides validation rules for model data. Supports both simple string rules
 * and parameterized rules.
 *
 * ## Basic Rules
 *
 * ```php
 * $rules = [
 *     'title' => 'notempty',
 *     'count' => 'positive|nonzero',
 *     'email' => 'email',
 * ];
 * ```
 *
 * ## Parameterized Rules
 *
 * ```php
 * $rules = [
 *     'name'   => 'notempty|length:3,255',
 *     'age'    => 'between:18,120',
 *     'status' => 'in:draft,published,archived',
 *     'code'   => 'regex:/^[A-Z]{3}$/',
 *     'score'  => 'min:0|max:100',
 * ];
 * ```
 *
 * ## Custom Callables
 *
 * ```php
 * $rules = [
 *     'field' => function($data) {
 *         return $data['field'] === 'valid' ? false : 'Invalid value';
 *     },
 * ];
 * ```
 *
 * ## ValidationError Objects
 *
 * Errors returned are ValidationError objects that stringify for BC:
 *
 * ```php
 * $result = Rules::validate($data, $rules);
 * if ($result !== true) {
 *     echo $result[0];            // BC: "title cannot be empty"
 *     echo $result[0]->getField(); // New: "title"
 *     echo $result[0]->getRule();  // New: "notempty"
 * }
 * ```
 */
class Rules
{
    /**
     * Do validation checks on provided data
     *
     * @param   array       $data   The fields to validate
     * @param   array       $rules  The rules upon which to validate
     * @return  array|bool  Array of ValidationError objects on failure, true on success
     */
    public static function validate($data, $rules)
    {
        $errors = [];

        foreach ($data as $k => $v) {
            if (array_key_exists($k, $rules)) {
                $fieldErrors = self::validateField($k, $v, $rules[$k], $data);
                $errors = array_merge($errors, $fieldErrors);
            }
        }

        return (count($errors) > 0) ? $errors : true;
    }

    /**
     * Validate a single field against its rules
     *
     * @param   string        $key    The field name
     * @param   mixed         $value  The field value
     * @param   string|callable|array  $rules  The rules to apply
     * @param   array         $data   The full data array
     * @return  array  Array of ValidationError objects
     */
    protected static function validateField($key, $value, $rules, array $data): array
    {
        $errors = [];

        // Handle callable rules (but not string function names, which could conflict with rule names)
        if (!is_string($rules) && is_callable($rules)) {
            $error = call_user_func_array($rules, [$data]);
            if ($error) {
                // Wrap string errors from callbacks in ValidationError
                if ($error instanceof ValidationError) {
                    $errors[] = $error;
                } elseif (is_array($error) && isset($error['message'])) {
                    $errors[] = new ValidationError(
                        $error['field'] ?? $key,
                        $error['rule'] ?? 'callback',
                        $error['message'],
                        $error['params'] ?? []
                    );
                } else {
                    $errors[] = new ValidationError($key, 'callback', (string) $error);
                }
            }
            return $errors;
        }

        // Handle array format rules (for scenarios, etc.)
        if (is_array($rules)) {
            if (isset($rules['rule'])) {
                // Single rule in array format
                $ruleList = [$rules];
            } else {
                // Multiple rules
                $ruleList = $rules;
            }

            foreach ($ruleList as $ruleConfig) {
                if (is_array($ruleConfig) && isset($ruleConfig['rule'])) {
                    $ruleName = $ruleConfig['rule'];
                    $params = $ruleConfig;
                    unset($params['rule'], $params['on'], $params['message']);

                    $error = self::applyRule($key, $value, $ruleName, $params, $data);
                    if ($error) {
                        // Override message if provided
                        if (isset($ruleConfig['message'])) {
                            $error = new ValidationError(
                                $error->getField(),
                                $error->getRule(),
                                self::interpolateMessage($ruleConfig['message'], $error->getParams()),
                                $error->getParams(),
                                $error->getValue()
                            );
                        }
                        $errors[] = $error;
                    }
                }
            }
            return $errors;
        }

        // Handle pipe-separated string rules
        $ruleList = strpos($rules, '|') !== false
            ? explode('|', $rules)
            : [$rules];

        foreach ($ruleList as $ruleString) {
            $error = self::parseAndApplyRule($key, $value, $ruleString, $data);
            if ($error) {
                $errors[] = $error;
            }
        }

        return $errors;
    }

    /**
     * Parse a rule string and apply it
     *
     * Supports formats:
     * - "notempty"
     * - "length:3,255"
     * - "regex:/^[A-Z]+$/"
     *
     * @param   string  $key        The field name
     * @param   mixed   $value      The field value
     * @param   string  $ruleString The rule string
     * @param   array   $data       The full data array
     * @return  ValidationError|null
     */
    protected static function parseAndApplyRule($key, $value, $ruleString, array $data): ?ValidationError
    {
        $ruleString = trim($ruleString);

        if (empty($ruleString)) {
            return null;
        }

        // Check for parameters (rule:param1,param2)
        // Special handling for regex which contains colons
        if (strpos($ruleString, 'regex:') === 0) {
            $ruleName = 'regex';
            $params = ['pattern' => substr($ruleString, 6)];
        } elseif (strpos($ruleString, ':') !== false) {
            list($ruleName, $paramString) = explode(':', $ruleString, 2);
            $params = self::parseParams($paramString);
        } else {
            $ruleName = $ruleString;
            $params = [];
        }

        return self::applyRule($key, $value, $ruleName, $params, $data);
    }

    /**
     * Apply a validation rule
     *
     * @param   string  $key      The field name
     * @param   mixed   $value    The field value
     * @param   string  $ruleName The rule name
     * @param   array   $params   Rule parameters
     * @param   array   $data     The full data array
     * @return  ValidationError|null
     */
    protected static function applyRule($key, $value, $ruleName, array $params, array $data): ?ValidationError
    {
        $ruleName = strtolower(trim($ruleName));

        // Map of rule names to methods
        $ruleMethod = 'rule' . ucfirst($ruleName);

        if (method_exists(__CLASS__, $ruleMethod)) {
            return self::$ruleMethod($key, $value, $params, $data);
        }

        // BC: Check for old-style private methods (notempty, positive, etc.)
        if (method_exists(__CLASS__, $ruleName)) {
            $result = self::$ruleName($key, $value);
            if ($result) {
                // Convert old string error to ValidationError
                if ($result instanceof ValidationError) {
                    return $result;
                }
                return new ValidationError($key, $ruleName, (string) $result, $params, $value);
            }
            return null;
        }

        // Unknown rule - skip silently for BC
        return null;
    }

    /**
     * Parse parameter string into array
     *
     * @param   string  $paramString  Comma-separated parameters
     * @return  array
     */
    protected static function parseParams(string $paramString): array
    {
        if (empty($paramString)) {
            return [];
        }

        $parts = explode(',', $paramString);
        $params = [];

        foreach ($parts as $i => $part) {
            $params[$i] = trim($part);
        }

        return $params;
    }

    /**
     * Interpolate message placeholders
     *
     * @param   string  $message  Message with {key} placeholders
     * @param   array   $params   Parameters to substitute
     * @return  string
     */
    protected static function interpolateMessage(string $message, array $params): string
    {
        foreach ($params as $key => $value) {
            $message = str_replace('{' . $key . '}', (string) $value, $message);
        }
        return $message;
    }

    // =========================================================================
    // Built-in Rules (BC - old style)
    // =========================================================================

    /**
     * Checks that var isn't empty
     *
     * @param   string  $key  The field name
     * @param   mixed   $var  The field content
     * @return  ValidationError|false
     */
    private static function notempty($key, $var)
    {
        if (!empty($var)) {
            return false;
        }
        return new ValidationError($key, 'notempty', "{$key} cannot be empty");
    }

    /**
     * Checks that var is positive
     *
     * @param   string  $key  The field name
     * @param   mixed   $var  The field content
     * @return  ValidationError|false
     */
    private static function positive($key, $var)
    {
        if ($var >= 0) {
            return false;
        }
        return new ValidationError($key, 'positive', "{$key} must be a positive integer");
    }

    /**
     * Checks that var is non-zero
     *
     * @param   string  $key  The field name
     * @param   mixed   $var  The field content
     * @return  ValidationError|false
     */
    private static function nonzero($key, $var)
    {
        if ($var > 0 || $var < 0) {
            return false;
        }
        return new ValidationError($key, 'nonzero', "{$key} cannot be zero");
    }

    /**
     * Checks that var is alphabetical
     *
     * @param   string  $key  The field name
     * @param   mixed   $var  The field content
     * @return  ValidationError|false
     */
    private static function alpha($key, $var)
    {
        if (preg_match('/^[[:alpha:] ]+$/', $var)) {
            return false;
        }
        return new ValidationError($key, 'alpha', "{$key} can only contain alphabetical characters");
    }

    /**
     * Checks that var is phone
     *
     * @param   string  $key  The field name
     * @param   mixed   $var  The field content
     * @return  ValidationError|false
     */
    private static function phone($key, $var)
    {
        // Matches NANPA phone numbers (US/CA/territories) with optional country code,
        // area code, separators, and extensions
        $areaCode = '(?![2-9]11)(?!555)([2-9][0-8][0-9])';
        $regex = '/^(?:(?:\+?1\s*(?:[.-]\s*)?)?'
            . '(?:\(\s*' . $areaCode . '\s*\)|' . $areaCode . ')'
            . '\s*(?:[.-]\s*)?)'
            . '([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?'
            . '([0-9]{4})'
            . '(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$/';

        if (preg_match($regex, $var)) {
            return false;
        }
        return new ValidationError($key, 'phone', "{$key} does not appear to be a valid phone number");
    }

    /**
     * Checks that var is email
     *
     * @param   string  $key  The field name
     * @param   mixed   $var  The field content
     * @return  ValidationError|false
     */
    private static function email($key, $var)
    {
        if (filter_var($var, FILTER_VALIDATE_EMAIL) !== false) {
            return false;
        }
        return new ValidationError($key, 'email', "{$key} does not appear to be a valid email address");
    }

    // =========================================================================
    // New Parameterized Rules
    // =========================================================================

    /**
     * Validate string length
     *
     * Usage: length:min,max OR length:min OR length:,max
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  [0 => min, 1 => max] or ['min' => x, 'max' => y]
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleLength($key, $value, array $params, array $data): ?ValidationError
    {
        $len = strlen((string) $value);

        $min = $params['min'] ?? $params[0] ?? null;
        $max = $params['max'] ?? $params[1] ?? null;

        if ($min !== null && $min !== '' && $len < (int) $min) {
            return new ValidationError(
                $key,
                'length',
                "{$key} must be at least {$min} characters",
                ['min' => $min, 'max' => $max, 'actual' => $len],
                $value
            );
        }

        if ($max !== null && $max !== '' && $len > (int) $max) {
            return new ValidationError(
                $key,
                'length',
                "{$key} cannot exceed {$max} characters",
                ['min' => $min, 'max' => $max, 'actual' => $len],
                $value
            );
        }

        return null;
    }

    /**
     * Validate minimum value
     *
     * Usage: min:value
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  [0 => min] or ['min' => x]
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleMin($key, $value, array $params, array $data): ?ValidationError
    {
        $min = $params['min'] ?? $params[0] ?? 0;

        if (is_numeric($value) && $value < $min) {
            return new ValidationError(
                $key,
                'min',
                "{$key} must be at least {$min}",
                ['min' => $min, 'actual' => $value],
                $value
            );
        }

        return null;
    }

    /**
     * Validate maximum value
     *
     * Usage: max:value
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  [0 => max] or ['max' => x]
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleMax($key, $value, array $params, array $data): ?ValidationError
    {
        $max = $params['max'] ?? $params[0] ?? PHP_INT_MAX;

        if (is_numeric($value) && $value > $max) {
            return new ValidationError(
                $key,
                'max',
                "{$key} cannot exceed {$max}",
                ['max' => $max, 'actual' => $value],
                $value
            );
        }

        return null;
    }

    /**
     * Validate value is between min and max
     *
     * Usage: between:min,max
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  [0 => min, 1 => max]
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleBetween($key, $value, array $params, array $data): ?ValidationError
    {
        $min = $params['min'] ?? $params[0] ?? 0;
        $max = $params['max'] ?? $params[1] ?? PHP_INT_MAX;

        if (is_numeric($value) && ($value < $min || $value > $max)) {
            return new ValidationError(
                $key,
                'between',
                "{$key} must be between {$min} and {$max}",
                ['min' => $min, 'max' => $max, 'actual' => $value],
                $value
            );
        }

        return null;
    }

    /**
     * Validate value is in a list
     *
     * Usage: in:value1,value2,value3
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  The allowed values
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleIn($key, $value, array $params, array $data): ?ValidationError
    {
        $allowed = $params['values'] ?? $params;

        if (!in_array($value, $allowed, false)) {
            $list = implode(', ', $allowed);
            return new ValidationError(
                $key,
                'in',
                "{$key} must be one of: {$list}",
                ['allowed' => $allowed, 'actual' => $value],
                $value
            );
        }

        return null;
    }

    /**
     * Validate value is NOT in a list
     *
     * Usage: notin:value1,value2,value3
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  The disallowed values
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleNotin($key, $value, array $params, array $data): ?ValidationError
    {
        $disallowed = $params['values'] ?? $params;

        if (in_array($value, $disallowed, false)) {
            $list = implode(', ', $disallowed);
            return new ValidationError(
                $key,
                'notin',
                "{$key} cannot be one of: {$list}",
                ['disallowed' => $disallowed, 'actual' => $value],
                $value
            );
        }

        return null;
    }

    /**
     * Validate value matches regex pattern
     *
     * Usage: regex:/pattern/
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  ['pattern' => '/regex/']
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleRegex($key, $value, array $params, array $data): ?ValidationError
    {
        $pattern = $params['pattern'] ?? $params[0] ?? '';

        if (empty($pattern)) {
            return null;
        }

        if (!preg_match($pattern, (string) $value)) {
            return new ValidationError(
                $key,
                'regex',
                "{$key} format is invalid",
                ['pattern' => $pattern],
                $value
            );
        }

        return null;
    }

    /**
     * Validate value is numeric
     *
     * Usage: numeric
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  Unused
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleNumeric($key, $value, array $params, array $data): ?ValidationError
    {
        if (!is_numeric($value)) {
            return new ValidationError(
                $key,
                'numeric',
                "{$key} must be a number",
                [],
                $value
            );
        }

        return null;
    }

    /**
     * Validate value is an integer
     *
     * Usage: integer
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  Unused
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleInteger($key, $value, array $params, array $data): ?ValidationError
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            return new ValidationError(
                $key,
                'integer',
                "{$key} must be an integer",
                [],
                $value
            );
        }

        return null;
    }

    /**
     * Validate value is a boolean
     *
     * Usage: boolean
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  Unused
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleBoolean($key, $value, array $params, array $data): ?ValidationError
    {
        $acceptable = [true, false, 0, 1, '0', '1'];

        if (!in_array($value, $acceptable, true)) {
            return new ValidationError(
                $key,
                'boolean',
                "{$key} must be a boolean",
                [],
                $value
            );
        }

        return null;
    }

    /**
     * Validate value is alphanumeric
     *
     * Usage: alphanumeric
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  Unused
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleAlphanumeric($key, $value, array $params, array $data): ?ValidationError
    {
        if (!ctype_alnum((string) $value)) {
            return new ValidationError(
                $key,
                'alphanumeric',
                "{$key} can only contain letters and numbers",
                [],
                $value
            );
        }

        return null;
    }

    /**
     * Validate value is a URL
     *
     * Usage: url
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  Unused
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleUrl($key, $value, array $params, array $data): ?ValidationError
    {
        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            return new ValidationError(
                $key,
                'url',
                "{$key} must be a valid URL",
                [],
                $value
            );
        }

        return null;
    }

    /**
     * Validate value matches another field
     *
     * Usage: same:other_field
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  [0 => other field name]
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleSame($key, $value, array $params, array $data): ?ValidationError
    {
        $otherField = $params['field'] ?? $params[0] ?? null;

        if ($otherField === null) {
            return null;
        }

        $otherValue = $data[$otherField] ?? null;

        if ($value !== $otherValue) {
            return new ValidationError(
                $key,
                'same',
                "{$key} must match {$otherField}",
                ['field' => $otherField],
                $value
            );
        }

        return null;
    }

    /**
     * Validate value differs from another field
     *
     * Usage: different:other_field
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  [0 => other field name]
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleDifferent($key, $value, array $params, array $data): ?ValidationError
    {
        $otherField = $params['field'] ?? $params[0] ?? null;

        if ($otherField === null) {
            return null;
        }

        $otherValue = $data[$otherField] ?? null;

        if ($value === $otherValue) {
            return new ValidationError(
                $key,
                'different',
                "{$key} must be different from {$otherField}",
                ['field' => $otherField],
                $value
            );
        }

        return null;
    }

    /**
     * Validate field is required (alias for notempty)
     *
     * Usage: required
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  Unused
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleRequired($key, $value, array $params, array $data): ?ValidationError
    {
        if (empty($value) && $value !== '0' && $value !== 0) {
            return new ValidationError(
                $key,
                'required',
                "{$key} is required",
                [],
                $value
            );
        }

        return null;
    }

    /**
     * Validate value is a valid date
     *
     * Usage: date OR date:Y-m-d
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  [0 => format] optional
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleDate($key, $value, array $params, array $data): ?ValidationError
    {
        $format = $params['format'] ?? $params[0] ?? null;

        if ($format !== null) {
            $d = \DateTime::createFromFormat($format, $value);
            if (!$d || $d->format($format) !== $value) {
                return new ValidationError(
                    $key,
                    'date',
                    "{$key} must be a valid date in format {$format}",
                    ['format' => $format],
                    $value
                );
            }
        } else {
            if (strtotime($value) === false) {
                return new ValidationError(
                    $key,
                    'date',
                    "{$key} must be a valid date",
                    [],
                    $value
                );
            }
        }

        return null;
    }

    /**
     * Validate value is a valid UUID v4
     *
     * Usage: uuid
     *
     * @param   string  $key     The field name
     * @param   mixed   $value   The field value
     * @param   array   $params  Unused
     * @param   array   $data    Full data array
     * @return  ValidationError|null
     */
    protected static function ruleUuid($key, $value, array $params, array $data): ?ValidationError
    {
        // Empty values are allowed (use required|uuid if needed)
        if ($value === null || $value === '') {
            return null;
        }

        // UUID v4 format: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
        // where y is one of 8, 9, a, or b
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';

        if (!preg_match($pattern, $value)) {
            return new ValidationError(
                $key,
                'uuid',
                "{$key} must be a valid UUID",
                [],
                $value
            );
        }

        return null;
    }
}
