<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Rules;

use Hubzero\Form\Rule;

/**
 * Form Rule for boolean values.
 */
class Boolean extends Rule
{
    /**
     * The regular expression to use in testing a form field value.
     *
     * @var  string
     */
    protected $regex = '^(?:[01]|true|false)$';

    /**
     * The regular expression modifiers to use when testing a form field value.
     *
     * @var  string
     */
    protected $modifiers = 'i';
}
