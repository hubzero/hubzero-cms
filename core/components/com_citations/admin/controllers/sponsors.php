<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Citations\Models\Sponsor;
use Hubzero\Facades\Request;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Facades\User;

/**
 * Controller class for citation types
 */
class Sponsors extends AdminController
{
    /**
     * Execute a task
     *
     * @return  void
     */
    public function execute()
    {
        $this->registerTask('add', 'edit');
        $this->registerTask('apply', 'save');

        parent::execute();
    }

    /**
     * List types
     *
     * @return  void
     */
    public function displayTask()
    {
        // The rows, not the query; wrapping the query in an array listed
        // one empty sponsor
        $sponsors = Sponsor::all()->rows();

        // Output the HTML
        $this->view
            ->set('sponsors', $sponsors)
            ->display();
    }

    /**
     * Edit an entry
     *
     * @param   \Components\Citations\Models\Sponsor|null  $row
     *
     * @return  void
     */
    public function editTask($row = null)
    {
        if (
            !User::authorise('core.edit', $this->option)
            && !User::authorise('core.create', $this->option)
        ) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        Request::setVar('hidemainmenu', 1);

        if (!($row instanceof Sponsor)) {
            // Incoming
            $id = Request::getArray('id', [0]);
            if (!empty($id)) {
                $id = $id[0];
            }

            $row = Sponsor::oneOrNew($id);
        }

        // Output the HTML
        $this->view
            ->set('config', $this->config)
            ->set('sponsor', $row)
            ->setLayout('edit')
            ->display();
    }

    /**
     * Save a type
     *
     * @return  void
     */
    public function saveTask()
    {
        // Check for request forgeries
        Request::checkToken();

        if (
            !User::authorise('core.edit', $this->option)
            && !User::authorise('core.create', $this->option)
        ) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $s = Request::getArray('sponsor', [], 'post');
        $sponsorId = !empty($s['id']) ? $s['id'] : null;
        unset($s['id']);

        $row = Sponsor::oneOrNew($sponsorId);
        $row->set($s);

        // Store new content
        if (!$row->save()) {
            Notify::error($row->getError());
            $this->editTask($row);

            return;
        }

        Notify::success(Lang::txt('CITATION_SPONSOR_SAVED'));

        $this->cancelTask();
    }

    /**
     * Remove one or more types
     *
     * @return  void
     */
    public function removeTask()
    {
        // Check for request forgeries
        Request::checkToken(['get', 'post']);

        if (!User::authorise('core.delete', $this->option)) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        // Incoming
        $ids = Request::getArray('id', []);

        $removed = 0;

        foreach ($ids as $id) {
            $sponsor = Sponsor::oneOrFail(intval($id));

            if (!$sponsor->destroy()) {
                Notify::error(Lang::txt('CITATION_SPONSOR_NOT_REMOVED', $id));
                continue;
            }

            $sponsor->citations()->sync([]);

            $removed++;
        }

        if ($removed) {
            Notify::success(Lang::txt('CITATION_SPONSOR_REMOVED'));
        }

        $this->cancelTask();
    }
}
