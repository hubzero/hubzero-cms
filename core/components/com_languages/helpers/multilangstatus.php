<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Languages\Helpers;

use Hubzero\Facades\App;

/**
 * Multilang status helper.
 */
abstract class Multilangstatus
{
    /**
     * Get homes
     *
     * @return  integer
     */
    public static function getHomes()
    {
        // Check for multiple Home pages
        $db = App::get('db');
        $query = $db->getQuery();
        $query->select('COUNT(*)');
        $query->from('#__menu');
        $query->where('home', '=', 1);
        $query->where('published', '=', 1);
        $query->where('client_id', '=', 0);
        $db->setQuery($query);
        return (int) $db->loadResult();
    }

    /**
     * Get lang switchers
     *
     * @return  integer
     */
    public static function getLangswitchers()
    {
        // Check if switcher is published
        $db = App::get('db');
        $query = $db->getQuery();
        $query->select('COUNT(*)');
        $query->from('#__modules');
        $query->whereEquals('module', 'mod_languages');
        $query->where('published', '=', 1);
        $query->where('client_id', '=', 0);
        $db->setQuery($query);
        return (int) $db->loadResult();
    }

    /**
     * Get content langs
     *
     * @return  array<int, \stdClass>
     */
    public static function getContentlangs()
    {
        // Check for published Content Languages
        $db = App::get('db');
        $query = $db->getQuery();
        $query->select('a.lang_code', 'lang_code');
        $query->select('a.published', 'published');
        $query->from('#__languages', 'a');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Get site langs
     *
     * @return  array<\stdClass>
     */
    public static function getSitelangs()
    {
        // check for published Site Languages
        $db = App::get('db');
        $query = $db->getQuery();
        $query->select('a.element', 'element');
        $query->from('#__extensions', 'a');
        // A bound value: quoting it as well made it 'language' with its quotes, which matched nothing
        $query->where('a.type', '=', 'language');
        $query->where('a.client_id', '=', 0);
        $db->setQuery($query);
        return $db->loadObjectList('element');
    }

    /**
     * Get home pages
     *
     * @return  array<\stdClass>
     */
    public static function getHomepages()
    {
        // Check for Home pages languages
        $db = App::get('db');
        $query = $db->getQuery();
        $query->select('language');
        $query->select('id');
        $query->from('#__menu');
        $query->where('home', '=', 1);
        $query->where('published', '=', 1);
        $query->where('client_id', '=', 0);
        $db->setQuery($query);
        return $db->loadObjectList('language');
    }

    /**
     * Get status
     *
     * @return  array<int, \stdClass>
     */
    public static function getStatus()
    {
        //check for combined status
        $db = App::get('db');
        $query = $db->getQuery();

        // Select all fields from the languages table.
        $query->select('a.*');
        $query->from('#__languages', 'a');

        // Select the language home pages (the joins were written for Joomla's builder,
        // whose join() took the type first and the whole ON clause after it)
        $query->select('l.home', 'home');
        $query->select('l.language', 'home_language');
        $joinCond = 'l.language = a.lang_code AND l.home=1 AND l.published=1 AND l.language <> \'*\'';
        $query->joinRaw('#__menu AS l', $joinCond, 'left');
        $query->select('e.enabled', 'enabled');
        $query->select('e.element', 'element');
        $query->joinRaw('#__extensions AS e', 'e.element = a.lang_code', 'left');
        $query->where('e.client_id', '=', 0);
        $query->where('e.enabled', '=', 1);
        $query->where('e.state', '=', 0);

        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
