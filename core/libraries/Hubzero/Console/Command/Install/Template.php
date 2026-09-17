<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Install;

use Hubzero\Facades\App;
use Hubzero\Template\Style;

/**
 * Make the template the install was told to use the one the hub wears
 *
 * The site settings step writes `site_template` into app/config, but that is
 * only the fallback the template loader reaches for when no style is marked
 * home. The style is what a hub actually wears, and it comes from the base
 * data - so an install told to use one template would come up wearing
 * whichever the base data made default, with nothing to say why.
 *
 * The site template only. `administrator_template` has never been acted on,
 * and what is written there in the field is not always an administrator
 * template - so reading it now would put a site template on the admin and
 * break the one screen somebody would use to put it back.
 **/
class Template
{
    /**
     * Make the named template the hub's site template
     *
     * @param   bool    $ansi      Whether to use ANSI color output
     * @param   string  $template  The site template, or null to leave it alone
     * @return  bool
     */
    public static function configure($ansi = true, $template = null)
    {
        if (!$template) {
            return true;
        }

        self::output("\n", $ansi);
        self::output("\e[33mSite Template\e[39m\n", $ansi);
        self::output("-------------\n", $ansi);
        self::output("\n", $ansi);

        $say = function ($message, $type = 'info') use ($ansi) {
            $colour = ($type == 'warning') ? "\e[33m" : '';
            self::output('  ' . $colour . $message . ($colour ? "\e[39m" : '') . "\n", $ansi);
        };

        return Style::makeDefault(App::get('db'), $template, 0, $say);
    }

    /**
     * The site template the answers ask for
     *
     * @param   array  $answers  The site section of the answer file
     * @return  string|null
     */
    public static function asked(array $answers)
    {
        $site = isset($answers['site_template']) ? trim((string) $answers['site_template']) : '';

        return $site ?: null;
    }

    /**
     * Write to stdout, stripping the colour when it is not wanted
     *
     * @param   string  $message
     * @param   bool    $ansi
     * @return  void
     */
    private static function output($message, $ansi = true)
    {
        echo $ansi ? $message : preg_replace('/\e\[[0-9;]*m/', '', $message);
    }
}
