<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Facades\Html;

/**
 * Renders a list of resource types
 */
class Resourcetype extends Select
{
    /**
     * The form field type.
     *
     * @var  string
     */
    protected $type = 'resourcetype';

    /**
     * Method to get the field options.
     *
     * @return  array<mixed>  The field option objects.
     */
    protected function getOptions()
    {

        $types = \Components\Resources\Models\Type::getMajorTypes();

        $options = [];

        foreach ($types as $type) {
            $options[] = Html::select('option', $type->id, stripslashes($type->type), 'value', 'text');
        }

        return array_merge(parent::getOptions(), $options);
    }
}
