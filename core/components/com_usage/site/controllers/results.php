<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Usage\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Usage\Helpers\Helper;
use Exception;
use Hubzero\Facades\Document;
use Hubzero\Facades\Pathway;
use Hubzero\Facades\Request;
use Hubzero\Facades\Event;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;

/**
 * Usage controller class for results
 */
class Results extends SiteController
{
    /**
     * Execute a task
     *
     * @return     void
     */
    public function execute()
    {
        $this->registerTask('__default', 'default');

        parent::execute();
    }

    /**
     * Display usage data
     *
     * @return     void
     */
    public function defaultTask()
    {
        // Set some common variables
        $thisyear = date("Y");
        $months = [
            '01' => 'Jan',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Apr',
            '05' => 'May',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Aug',
            '09' => 'Sep',
            '10' => 'Oct',
            '11' => 'Nov',
            '12' => 'Dec',
        ];
        $monthsReverse = array_reverse($months, true);

        // Incoming
        $enddate = Request::getInt('selectedPeriod', 0, 'post');

        // Establish a connection to the usage database
        $udb = Helper::getUDBO();
        if (!is_object($udb)) {
            throw new Exception(Lang::txt('COM_USAGE_ERROR_CONNECTING_TO_DATABASE'), 500);
        }

        $this->view->set('no_html', Request::getInt('no_html', 0));

        // Trigger the functions that return the areas we'll be using
        $this->view->set('cats', Event::trigger('usage.onUsageAreas'));

        if (is_array($this->view->get('cats'))) {
            if (!$this->task || $this->task == 'default') {
                $hasCats = isset($this->view->cats[0]) && is_array($this->view->cats[0]);
                $this->task = $hasCats ? (string) key($this->view->cats[0]) : 'overview';
            }
        }
        $this->task = ($this->task) ? $this->task : 'overview';

        // Only areas an enabled usage plugin offers: /usage/tools with the tools
        // plugin off drew an empty page titled with its language key
        $areas = [];
        foreach ((array) $this->view->get('cats') as $cat) {
            if (is_array($cat)) {
                $areas = array_merge($areas, array_keys($cat));
            }
        }
        if ($areas && !in_array($this->task, $areas)) {
            App::abort(404, Lang::txt('COM_USAGE_ERROR_AREA_NOT_FOUND'));
        }

        $this->view->task = $this->task;

        // Set the pathway
        if (Pathway::count() <= 0) {
            Pathway::append(
                Lang::txt(strtoupper($this->option)),
                'index.php?option=' . $this->option,
            );
        }
        Pathway::append(
            Lang::txt('PLG_' . strtoupper($this->name) . '_' . strtoupper($this->task)),
            'index.php?option=' . $this->option . '&task=' . $this->task,
        );

        // Get the sections
        $this->view->set('sections', Event::trigger('usage.onUsageDisplay', [
            $this->option,
            $this->task,
            $udb,
            $months,
            $monthsReverse,
            $enddate,
        ]));

        // Build the page title
        $title = Lang::txt(strtoupper($this->option));
        $langKey = 'PLG_' . strtoupper($this->name) . '_' . strtoupper($this->task);
        $title .= ': ' . Lang::txt($langKey);

        // Set the page title
        Document::setTitle($title);
        $this->view->set('title', $title);

        // Output HTML
        $this->view
            ->setLayout('default')
            ->setErrors($this->getErrors())
            ->display();
    }
}
