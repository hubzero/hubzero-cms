<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Facades\App;

/**
 * Renders a select list of Asset Groups
 */
class ContentLanguages extends Select
{
    /**
     * Element name
     *
     * @var  string
     */
    protected $name = 'ContentLanguages';

    /**
     * Get the options for the element
     *
     * @param   \SimpleXMLElement  &$node  XMLElement node object containing the settings for the element
     * @return  array<mixed>
     */
    protected function getOptions(&$node)
    {
        $db = App::get('db');

        $query = $db
            ->getQuery()
            ->select('a.lang_code', 'value')
            ->select('a.title', 'title')
            ->select('a.title_native')
            ->from('#__languages', 'a')
            ->where('a.published', '>=', '0')
            ->order('a.title', 'asc');

        // Get the options.
        $db->setQuery($query->toString());
        $options = $db->loadObjectList();

        // Check for a database error.

        // Merge any additional options in the XML definition.
        return array_merge(parent::getOptions($node), $options);
    }
}
