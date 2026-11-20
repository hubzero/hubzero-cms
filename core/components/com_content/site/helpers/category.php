<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Content\Site\Helpers;

/**
 * Content Component Category Tree
 */
class Category extends \Components\Categories\Helpers\Categories
{
    /**
     * Class constructor
     *
     * @param   array<mixed>  $options  Array of options
     * @return  void
     */
    public function __construct($options = [])
    {
        $options['table'] = '#__content';
        $options['extension'] = 'com_content';

        parent::__construct($options);
    }
}
