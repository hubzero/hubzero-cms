<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Facades\App;

/**
 * Form Field class for listing plugins
 */
class Plugins extends Select
{
    /**
     * The field type.
     *
     * @var  string
     */
    protected $type = 'Plugins';

    /**
     * Method to get a list of options for a list input.
     *
     * @return  array<mixed>|string  An array of Html options.
     */
    protected function getOptions()
    {
        $element = $this->fieldElement();

        // Initialise variables
        $folder = $element['folder'];

        if (!empty($folder)) {
            // Get list of plugins
            $db = App::get('db');
            $query = $db
                ->getQuery()
                ->select('element', 'value')
                ->select('name', 'text')
                ->from('#__extensions')
                ->whereEquals('folder', $folder)
                ->whereEquals('enabled', '1')
                ->order('ordering', 'asc')
                ->order('name', 'asc');
            $db->setQuery($query->toString());

            $options = $db->loadObjectList();

            $lang = App::get('language');
            foreach ($options as $i => $item) {
                $extension = 'plg_' . $folder . '_' . $item->value;
                $lang->load($extension
                    . '.sys', PATH_APP
                    . '/plugins/'
                    . $folder
                    . '/'
                    . $item->value, null, false, true)
                ||  $lang->load($extension
                . '.sys', PATH_CORE
                . '/plugins/'
                . $folder
                . '/'
                . $item->value, null, false, true);

                $options[$i]->text = $lang->txt($item->text);
            }
        } else {
            App::abort(500, App::get('language')->txt('JFRAMEWORK_FORM_FIELDS_PLUGINS_ERROR_FOLDER_EMPTY'));
        }

        // Merge any additional options in the XML definition.
        return array_merge(parent::getOptions(), $options);
    }
}
