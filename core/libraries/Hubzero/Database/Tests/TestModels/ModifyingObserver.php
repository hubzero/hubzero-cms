<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

/**
 * Observer that modifies the model
 */
class ModifyingObserver
{
    public function creating($model)
    {
        // Generate a slug from the title
        $title = $model->get('title');
        $slug = strtolower(str_replace(' ', '-', $title)) . '-modified';
        $model->set('slug', $slug);
    }
}
