<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Template;

/**
 * The one place that says which template a client wears
 *
 * A hub's template is a row: the style in #__template_styles whose `home` is
 * 1 for that client. The `site_template` in app/config is only the fallback
 * the loader reaches for when there is no such row, so writing the config and
 * stopping there leaves a hub running whatever the base data made default -
 * which is how an install could be told to use one template and come up
 * wearing another.
 *
 * Both the installer and a flavor's `template` lever come through here, so
 * there is one answer to "make this the default" rather than one per caller.
 **/
class Style
{
    /**
     * Make a template's style the default for a client
     *
     * A template with no style of its own gets one, so that naming a template
     * that was installed without a style still works. Nothing is deleted: the
     * styles that were there stay, and only which of them is home changes.
     *
     * @param   object    $db        The database
     * @param   string    $template  The template element, e.g. meridian
     * @param   integer   $client    0 for the site, 1 for the administrator
     * @param   callable  $log       Optional; takes a message and a type
     * @return  boolean   Whether a style is now the default
     */
    public static function makeDefault($db, $template, $client = 0, $log = null)
    {
        $say = function ($message, $type = 'info') use ($log) {
            if (is_callable($log)) {
                call_user_func($log, $message, $type);
            }
        };

        if (!$template || !$db->tableExists('#__template_styles')) {
            return false;
        }

        $db->setQuery(
            "SELECT `id` FROM `#__template_styles` WHERE `client_id` = " . (int) $client
            . " AND `template` = " . $db->quote($template) . " ORDER BY `home` DESC, `id` ASC"
        );
        $id = (int) $db->loadResult();

        if (!$id) {
            // A template can be on disk, and installed, without a style of its
            // own - the styles are data and the template is files. Give it one
            // rather than refusing to use it.
            if (!self::exists($template)) {
                $say("No template named '" . $template . "' - leaving the default as it is", 'warning');

                return false;
            }

            $db->setQuery(
                "INSERT INTO `#__template_styles` (`template`, `client_id`, `home`, `title`, `params`)"
                . " VALUES (" . $db->quote($template) . ", " . (int) $client . ", 0, "
                . $db->quote($template) . ", '{}')"
            );
            $db->query();

            $id = (int) $db->insertid();

            $say("Added a style for '" . $template . "', which had none");
        }

        $db->setQuery(
            "UPDATE `#__template_styles` SET `home` = 0 WHERE `client_id` = " . (int) $client
        );
        $db->query();

        $db->setQuery("UPDATE `#__template_styles` SET `home` = 1 WHERE `id` = " . $id);
        $db->query();

        $say('Making ' . $template . ' the default ' . ($client ? 'administrator' : 'site') . ' template');

        return true;
    }

    /**
     * The template a client wears, or an empty string
     *
     * @param   object   $db      The database
     * @param   integer  $client  0 for the site, 1 for the administrator
     * @return  string
     */
    public static function current($db, $client = 0)
    {
        if (!$db->tableExists('#__template_styles')) {
            return '';
        }

        $db->setQuery(
            "SELECT `template` FROM `#__template_styles`"
            . " WHERE `client_id` = " . (int) $client . " AND `home` = 1"
        );

        return (string) $db->loadResult();
    }

    /**
     * Is the template on disk
     *
     * Site and administrator templates share one directory; which client a
     * template is for is a property of its row, not of where it sits. A hub's
     * own templates in the app directory count as much as the shipped ones.
     *
     * @param   string  $template
     * @return  boolean
     */
    public static function exists($template)
    {
        // A template is named, not addressed. Without this, '../components'
        // is a directory that exists and would be accepted as a template.
        if (!preg_match('/^[A-Za-z0-9_-]+$/', (string) $template)) {
            return false;
        }

        foreach (array('PATH_APP', 'PATH_CORE') as $constant) {
            if (!defined($constant)) {
                continue;
            }

            if (is_dir(constant($constant) . '/templates/' . $template)) {
                return true;
            }
        }

        return false;
    }
}
