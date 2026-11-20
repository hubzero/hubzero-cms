<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

/**
 * Supports an HTML select list of image
 */
class Imagelist extends Filelist
{
    /**
     * The form field type.
     *
     * @var  string
     */
    public $type = 'Imagelist';

    /**
     * Method to get the list of images field options.
     * Use the filter attribute to specify allowable file extensions.
     *
     * @return  array<mixed>  The field option objects.
     */
    protected function getOptions()
    {
        $element = $this->fieldElement();

        // Define the image file type filter.
        $filter = '\.png$|\.gif$|\.jpg$|\.bmp$|\.ico$|\.jpeg$|\.psd$|\.eps$';

        // Set the form field element attribute for file type filter.
        $element->addAttribute('filter', $filter);

        // Get the field options.
        return parent::getOptions();
    }
}
