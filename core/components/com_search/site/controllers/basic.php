<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Search\Models\Basic\Result\Set;
use Components\Search\Models\Basic\Terms;
use Hubzero\Facades\Document;
use Hubzero\Facades\Pathway;
use Hubzero\Facades\Request;
use Hubzero\Facades\Plugin;
use Hubzero\Facades\Config;
use Hubzero\Facades\Lang;

/**
 * Search controller class
 */
class Basic extends SiteController
{
    /**
     * Display search form and results (if any)
     *
     * @return  void
     */
    public function displayTask()
    {
        Plugin::import('search');

        // Set breadcrumbs
        Pathway::append(
            Lang::txt('COM_SEARCH'),
            'index.php?option=com_search',
        );

        $terms = new Terms(Request::getString('terms'));

        // Set the document title
        $title = $terms->isSet()
            ? Lang::txt('COM_SEARCH_RESULTS_FOR', $this->view->escape($terms->getRaw()))
            : 'Search';
        Document::setTitle($title);

        // Get search results
        $results = new Set($terms);
        $results->setLimit(Request::getState('global.list.limit', 'limit', Config::get('list_limit'), 'int'));
        $results->setOffset(Request::getInt('limitstart', 0));
        $results->collect(Request::getBool('force-generic'));

        $this->view->set('url_terms', urlencode($terms->getRawWithoutSection()));
        @list($plugin, $section) = $terms->getSection();

        $total = 0;
        if ($plugin) {
            foreach ($results->getResultCounts() as $cat => $def) {
                if ($plugin == $cat) {
                    $total = $def['count'];
                    break;
                }
            }
        } else {
            $total = $results->getTotalCount();
        }

        $this->view->set('terms', $terms);
        $this->view->set('results', $results);
        $this->view->set('total', $total);
        $this->view->set('plugin', $plugin);
        $this->view->set('section', $section);

        $this->view->display();
    }
}
