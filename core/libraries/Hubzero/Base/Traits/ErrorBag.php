<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Traits;

use Exception;

/**
 * Error Bag trait.
 * Adds basic methods for setting and getting error messages.
 *
 * Methods based on those found in Joomla's JObject class.
 */
trait ErrorBag
{
    /**
     * An array of error messages or Exception objects.
     *
     * @var  array<mixed>
     */
    protected $errors = [];

    /**
     * Get the most recent error message.
     *
     * @param   integer|null  $i         Option error index.
     * @param   boolean  $toString  Indicates if error objects should return their error message.
     * @return  ($toString is true ? string : string|\Exception)  The error, or an empty string when there is none
     * @phpstan-impure
     */
    public function getError($i = null, $toString = true)
    {
        // Find the error
        if ($i === null) {
            // Default, return the last message
            $error = end($this->errors);
            if ($error === false) {
                // No errors: an empty string, as Database\Traits\ErrorBag gives
                return '';
            }
        } elseif (!array_key_exists($i, $this->errors)) {
            // If $i has been specified but does not exist, there is none
            return '';
        } else {
            $error = $this->errors[$i];
        }

        // Only the message: an Exception's own string adds its class, file and stack trace
        if ($error instanceof Exception && $toString) {
            return $error->getMessage();
        }

        return $error;
    }

    /**
     * Return all errors, if any.
     *
     * @return  array<mixed>  Array of error messages
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Add an error message.
     *
     * @param   string|\Exception|false|null  $error  Error message. False, null or empty (what getError() gives
     *                                                when there is none, often passed on) is not recorded.
     * @param   int|string|null  $key    Specific key to set the value to
     * @return  $this
     */
    public function setError($error, $key = null)
    {
        // Passing on another object's getError() when it had none gave false,
        // which used to be recorded as an error
        if ($error === false || $error === null || $error === '') {
            return $this;
        }

        if ($key !== null) {
            $this->errors[$key] = $error;
        } else {
            array_push($this->errors, $error);
        }
        return $this;
    }

    /**
     * Set the list of errors
     *
     * @param   array<mixed>   $errors  List of Error message.
     * @return  $this
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
        return $this;
    }
}
