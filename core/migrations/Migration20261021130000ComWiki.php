<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for bringing seeded wiki help pages up to date
 *
 * The default pages are only copied into a wiki when it is created, so
 * fixes to them never reach existing wikis. This applies the latest
 * fixes to the current version of each page that still has the old
 * markup: the generated macro list, multi-line math examples, the
 * processors table header, and the accessible search form.
 **/
class Migration20261021130000ComWiki extends Base
{
    /**
     * The old search form on MainPage
     *
     * @var  string
     */
    private $oldForm = "\t\t\t\t\t<form action=\"/search\" method=\"get\" class=\"search\">\n\t\t\t\t\t\t<fieldset>\n\t\t\t\t\t\t\t<p>\n\t\t\t\t\t\t\t\t<input type=\"text\" name=\"terms\" value=\"\" />\n";

    /**
     * The accessible search form on MainPage
     *
     * @var  string
     */
    private $newForm = "\t\t\t\t\t<form action=\"/search\" method=\"get\" class=\"search\" role=\"search\" aria-label=\"Search wiki articles\">\n\t\t\t\t\t\t<fieldset>\n\t\t\t\t\t\t\t<legend class=\"sr-only\">Search wiki articles</legend>\n\t\t\t\t\t\t\t<p>\n\t\t\t\t\t\t\t\t<label for=\"wiki-search-terms\" class=\"sr-only\">Search terms</label>\n\t\t\t\t\t\t\t\t<input type=\"text\" name=\"terms\" id=\"wiki-search-terms\" value=\"\" />\n";

    /**
     * Up
     **/
    public function up()
    {
        $this->rewrite(function ($pagename, $text) {
            switch ($pagename) {
                case 'Help:WikiMacros':
                    // The hand-written list is replaced by the generated one
                    if (strpos($text, "`[[BR]]`::") !== false) {
                        $text = preg_replace('/== Available Macros ==\s*\r?\n `\[\[BR\]\]`::.*$/s', "== Available Macros ==\n\n[[Macrolist]]", $text);
                    }
                    break;

                case 'Help:WikiMath':
                    // A {{{ opener swallows the rest of its line, so one-line blocks lost their content
                    $text = preg_replace('/^\{\{\{(<math>.*?<\/math>)\}\}\}[ \t]*$/m', "{{{\n$1\n}}}", $text);
                    break;

                case 'Help:Processors':
                    if (strpos($text, '||=Processor=||') === false) {
                        $text = preg_replace('/(=== Available Processors ===\s*\r?\n\r?\n)(\|\| `#!default`)/', "$1||=Processor=||=Description=||\n$2", $text);
                    }
                    break;

                case 'MainPage':
                    $text = str_replace($this->oldForm, $this->newForm, $text);
                    // Already linked further down the page
                    $text = str_replace(" * [Special:AllPages Full Article List]\n", '', $text);
                    break;
            }

            return $text;
        });
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->rewrite(function ($pagename, $text) {
            switch ($pagename) {
                case 'Help:WikiMacros':
                    // The hand-written list is not restored; the generated one still documents every macro
                    break;

                case 'Help:WikiMath':
                    // Left as is: the page always had some multi-line examples, and both forms render
                    break;

                case 'Help:Processors':
                    $text = str_replace("||=Processor=||=Description=||\n|| `#!default`", "|| `#!default`", $text);
                    break;

                case 'MainPage':
                    $text = str_replace($this->newForm, $this->oldForm, $text);
                    $text = str_replace(" * [Help:Index Help Articles]\n\n", " * [Help:Index Help Articles]\n * [Special:AllPages Full Article List]\n\n", $text);
                    break;
            }

            return $text;
        });
    }

    /**
     * Apply a rewrite to the current version of every copy of the affected pages
     *
     * @param   callable  $rewrite  Takes (pagename, text) and returns the new text
     * @return  void
     **/
    private function rewrite($rewrite)
    {
        if (!$this->db->tableExists('#__wiki_versions') || !$this->db->tableExists('#__wiki_pages')) {
            return;
        }

        $query = "SELECT v.`id`, v.`pagetext`, p.`pagename`, p.`scope`, p.`scope_id`
				FROM `#__wiki_versions` AS v
				INNER JOIN `#__wiki_pages` AS p ON p.`version_id`=v.`id`
				WHERE p.`pagename` IN ('Help:WikiMacros', 'Help:WikiMath', 'Help:Processors', 'MainPage')";
        $this->db->setQuery($query);
        $versions = $this->db->loadObjectList();

        $changed = 0;

        foreach ($versions as $version) {
            $text = $rewrite($version->pagename, $version->pagetext);

            if ($text === $version->pagetext) {
                continue;
            }

            $query = "UPDATE `#__wiki_versions` SET `pagetext`=" . $this->db->quote($text) . " WHERE `id`=" . (int) $version->id;
            $this->db->setQuery($query);
            $this->db->query();

            $changed++;
        }

        $this->log(sprintf('Updated %d of %d wiki help page(s)', $changed, count($versions)));
    }
}
