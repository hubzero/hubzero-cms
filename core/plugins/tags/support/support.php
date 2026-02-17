<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Tags plugin class for support tickets
 */
namespace Plugins\Tags\Support;

use Hubzero\Plugin\Plugin;

class Support extends Plugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var    boolean
     */
// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_autoloadLanguage = true;
}
