<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\TestModels;

/**
 * Observer that modifies the model
 */
class ModifyingObserver
{
    /**
     * @param   mixed  $model
     * @return  void
     */
    public function creating($model)
    {
        // Generate a slug from the title
        $title = $model->get('title');
        $slug = strtolower(str_replace(' ', '-', $title)) . '-modified';
        $model->set('slug', $slug);
    }
}
