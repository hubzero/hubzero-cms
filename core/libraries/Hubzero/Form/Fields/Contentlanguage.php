<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Html\Builder\Contentlanguage as ContentLang;

/**
 * Provides a list of content languages
 */
class Contentlanguage extends Select
{
    /**
     * The form field type.
     *
     * @var  string
     */
    public $type = 'Contentlanguage';

    /**
     * Method to get the field options for content languages.
     *
     * @return  array<mixed>  The field option objects.
     */
    protected function getOptions()
    {
        // Merge any additional options in the XML definition.
        return array_merge(parent::getOptions(), ContentLang::existing());
    }
}
