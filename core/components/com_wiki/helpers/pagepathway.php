<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Helpers;

use Pathway;

require_once __DIR__ . DS . 'settings.php';

/**
 * Breadcrumb helpers shared by the wiki site controllers
 */
trait PagePathway
{
    /**
     * Append the current page's ancestors and the page itself to the pathway
     *
     * @return  array  The ancestors, root first, when the page views should list them as crumbs; otherwise empty
     */
    protected function appendPagePathway()
    {
        $parents = array();

        if ($this->page->get('parent')) {
            $parents = $this->page->ancestors();

            foreach ($parents as $p) {
                Pathway::append(
                    $p->get('title'),
                    $p->link()
                );
            }
        }

        Pathway::append(
            $this->page->title,
            $this->page->link()
        );

        // Off by default, since a site or group template may already show the pathway
        if ($parents && !Settings::get('wiki_crumbs', 0, $this->page->get('scope'), $this->page->get('scope_id'))) {
            $parents = array();
        }

        return $parents;
    }
}
