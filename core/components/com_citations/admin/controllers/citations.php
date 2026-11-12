<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Admin\Controllers;

use Components\Citations\Models\Citation;
use Components\Citations\Models\Association;
use Components\Citations\Models\Type;
use Components\Citations\Models\Sponsor;
use Components\Citations\Helpers\Format;
use Hubzero\Component\AdminController;
use Hubzero\Config\Registry;
use Exception;
use Hubzero\Facades\Request;
use Config;
use Route;
use Hubzero\Facades\Event;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Plugin;
use Hubzero\Facades\User;

/**
 * Controller class for citations
 *
 * Set on the controller as it runs:
 *
 * @property  string  $tags      The tags typed in, set by saveTask()
 * @property  string  $badges    The badges typed in, set by saveTask()
 * @property  array   $sponsors  The sponsor IDs chosen, set by saveTask()
 */
class Citations extends AdminController
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
        $this->registerTask('publish', 'state');
        $this->registerTask('unpublish', 'state');

        parent::execute();
    }

    /**
     * List citations
     *
     * @return  void
     */
    public function displayTask()
    {
        $filters = [
            // Get filters
            'sort' => Request::getState(
                $this->option . '.' . $this->controller . '.sort',
                'filter_order',
                'id',
            ),
            'sort_Dir' => Request::getState(
                $this->option . '.' . $this->controller . '.sortdir',
                'filter_order_Dir',
                'ASC',
            ),
            'search' => urldecode(Request::getState(
                $this->option . '.' . $this->controller . '.search',
                'search',
                '',
            )),
            'published' => [0, 1],
            'scope' => urldecode(Request::getState(
                $this->option . '.' . $this->controller . '.scope',
                'scope',
                'all',
            )),
        ];

        // Get records
        $rows = Citation::getFilteredRecords($filters)
            ->order($filters['sort'], $filters['sort_Dir'])
            ->paginated('limitstart', 'limit')
            ->rows();

        // Output the HTML
        $this->view
            ->set('filters', $filters)
            ->set('rows', $rows)
            ->display();
    }

    /**
     * Edit a citation
     *
     * @param   object
     */
    public function editTask($row = null)
    {
        if (
            !User::authorise('core.edit', $this->option)
            && !User::authorise('core.create', $this->option)
        ) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        // Stop menu from working?
        Request::setVar('hidemainmenu', 1);

        if (!($row instanceof Citation)) {
            // Get request vars - expecting an array id[]=4232
            $id = Request::getArray('id', [0]);
            if (is_array($id) && !empty($id)) {
                $id = $id[0];
            }

            $row = Citation::oneOrNew($id);
        }

        //if we have an id load that citation data
        if (!$row->isNew()) {
            $assocs = [];
            foreach ($row->associations as $assoc) {
                $assocs[] = $assoc;
            }

            // Get sponsors for citation
            $row_sponsors = $row->sponsors->fieldsByKey('id');

            // Get the citations tags
            $tags = Format::citationTags($row, false);

            // Get the badges
            $badges = Format::citationBadges($row, false);

            // Parse citation params
            $params = new Registry($row->params);
        } else {
            // Set the creator
            $row->set('uid', User::get('id'));

            // It's new - no associations to get
            $assocs = [];

            // Array of sponsors - empty
            $row_sponsors = [];

            // Empty tags and badges arrays
            $tags = [];
            $badges = [];

            // Empty params object
            $params = new Registry('');
        }

        // Get all citations sponsors
        $sponsors = Sponsor::all();

        // Get all citation types
        $types = Type::all();

        // Set any errors
        if ($this->getError()) {
            Notify::error($this->getError());
        }

        // Output the HTML
        $this->view
            ->set('config', $this->config)
            ->set('row', $row)
            ->set('sponsors', $sponsors)
            ->set('types', $types)
            ->set('tags', $tags)
            ->set('badges', $badges)
            ->set('params', $params)
            ->set('row_sponsors', $row_sponsors)
            ->set('assocs', $assocs)
            ->setLayout('edit')
            ->display();
    }

    /**
     * Publish a citation
     *
     * @return  void
     */
    public function stateTask()
    {
        // Check for request forgeries
        Request::checkToken(['get', 'post']);

        if (!User::authorise('core.edit.state', $this->option)) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $state = $this->getTask() == 'publish' ? 1 : 0;

        // Incoming
        $ids = Request::getArray('id', []);

        $success = 0;
        foreach ($ids as $id) {
            $row = Citation::oneOrFail($id);

            // Mark record and save
            $row->set('published', $state);

            if (!$row->save()) {
                Notify::error($row->getError());
                continue;
            }

            $success++;
        }

        if ($success) {
            $msg = $this->getTask() == 'publish'
                ? Lang::txt('CITATION_PUBLISHED')
                : Lang::txt('CITATION_UNPUBLISHED');

            Notify::success($msg);
        }

        // Redirect
        $this->cancelTask();
    }

    /**
     * Toggle affliliation
     *
     * @return  void
     */
    public function affiliateTask()
    {
        // Check for request forgeries
        Request::checkToken(['get', 'post']);

        if (!User::authorise('core.edit.state', $this->option)) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $ids = Request::getArray('id', []);

        $success = 0;
        foreach ($ids as $id) {
            $row = Citation::oneOrFail($id);

            $affiliatedId = ($row->get('affiliated') == 1) ? 0 : 1;

            $row->set('affiliated', $affiliatedId);

            if (!$row->save()) {
                Notify::error($row->getError());
                continue;
            }

            $success++;
        }

        if ($success) {
            Notify::success(Lang::txt('CITATION_AFFILIATED'));
        }

        // Redirect
        $this->cancelTask();
    }

    /**
     * Toggle fundedby
     *
     * @return  void
     */
    public function fundTask()
    {
        // Check for request forgeries
        Request::checkToken(['get', 'post']);

        if (!User::authorise('core.edit.state', $this->option)) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $ids = Request::getArray('id', []);

        $success = 0;
        foreach ($ids as $id) {
            $row = Citation::oneOrFail($id);

            $fundedbyId = ($row->get('fundedby') == 1) ? 0 : 1;

            $row->set('fundedby', $fundedbyId);

            if (!$row->save()) {
                Notify::error($row->getError());
                continue;
            }

            $success++;
        }

        if ($success) {
            Notify::success(Lang::txt('CITATION_FUNDED'));
        }

        // Redirect
        $this->cancelTask();
    }

    /**
     * Display stats for citations
     *
     * @return  void
     */
    public function statsTask()
    {
        $filters = [
            'scope'     => 'all',
            'published' => [0, 1],
        ];

        $stats = Citation::getYearlyStats($filters);

        // Output the HTML
        $this->view
            ->set('stats', $stats)
            ->display();
    }

    /**
     * Save a citation
     *
     * @return  void
     */
    public function saveTask()
    {
        // Check for request forgeries
        Request::checkToken();

        Plugin::import('citation');

        if (
            !User::authorise('core.edit', $this->option)
            && !User::authorise('core.create', $this->option)
        ) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        $citation = array_map('trim', Request::getArray('citation', [], 'post'));
        $exclude  = Request::getString('exclude', '', 'post');
        $rollover = Request::getInt("rollover", 0);
        $this->tags = Request::getString('tags', '');
        $this->badges = Request::getString('badges', '');
        $this->sponsors = array_filter(Request::getArray('sponsors', [], 'post'));
        $citationId = !empty($citation['id']) ? $citation['id'] : null;
        unset($citation['id']);

        // Blank date fields post ''; strict SQL mode rejects '' for a datetime, so store NULL
        foreach (['created', 'date_submit', 'date_accept', 'date_publish'] as $datefield) {
            if (isset($citation[$datefield]) && $citation[$datefield] === '') {
                $citation[$datefield] = null;
            }
        }
        // A new citation's hidden created field is blank; stamp it as the site form does
        if (!$citationId && empty($citation['created'])) {
            $citation['created'] = \Date::toSql();
        }

        // toggle the affiliation
        if (!isset($citation['affiliated']) || $citation['affiliated'] == null) {
            $citation['affiliated'] = 0;
        }

        // toggle fundeby
        if (!isset($citation['fundedby']) || $citation['fundedby'] == null) {
            $citation['fundedby'] = 0;
        }
        // Bind incoming data to object
        $row = Citation::oneOrNew($citationId);
        $row->set($citation);

        // Set params
        $cparams = new Registry($this->getParams($row->id));
        $cparams->set('exclude', $exclude);
        $cparams->set('rollover', $rollover);
        $row->set('params', $cparams->toString());

        // Incoming associations
        $assocParams = Request::getArray('assocs', [], 'post');
        $associations = [];
        foreach ($assocParams as $assoc) {
            $assoc = array_map('trim', $assoc);
            $assocId = !empty($assoc['id']) ? $assoc['id'] : null;
            unset($assoc['id']);
            $newAssociation = Association::oneOrNew($assocId)->set($assoc);
            if (!$newAssociation->isNew() && (empty($assoc['tbl']) || empty($assoc['oid']))) {
                $newAssociation->destroy();
            } else {
                if (!empty($assoc['tbl']) && !empty($assoc['oid'])) {
                    $associations[] = $newAssociation;
                }
            }
        }

        $row->attach('associations', $associations);

        // Trigger before save event
        $isNew  = $row->isNew();
        $result = Event::trigger('onCitationBeforeSave', [&$row, $isNew]);

        if (in_array(false, $result, true)) {
            Notify::error($row->getError());
            $this->editTask($row);

            return;
        }

        // Store new content
        if (!$row->saveAndPropagate()) {
            Notify::error($row->getError());
            $this->editTask($row);

            return;
        }

        $row->sponsors()->sync($this->sponsors);

        //add tags & badges
        $row->updateTags($this->tags);
        $row->updateTags($this->badges, 'badge');

        // Trigger after save event
        Event::trigger('onCitationAfterSave', [&$row, $isNew]);

        Notify::success(Lang::txt('CITATION_SAVED', $row->id));

        // Redirect
        $this->cancelTask();
    }

    /**
     * Remove one or more citations
     *
     * @return  void
     */
    public function removeTask()
    {
        // Check for request forgeries
        Request::checkToken();

        if (!User::authorise('core.delete', $this->option)) {
            App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
        }

        // Incoming (we're expecting an array)
        $ids = Request::getArray('id', []);

        $removed = 0;

        // Loop through the IDs and delete the citation
        foreach ($ids as $id) {
            $citation = Citation::oneOrFail($id);

            if (!$citation->destroy()) {
                Notify::error($citation->getError());
                continue;
            }

            // Trigger before delete event
            Event::trigger('onCitationAfterDelete', [$id]);

            $removed++;
        }

        if ($removed) {
            Notify::success(Lang::txt('CITATION_REMOVED', $removed));
        } else {
            Notify::error(Lang::txt('NO_SELECTION'));
        }

        $this->cancelTask();
    }

    /**
     * Get the params for a citation
     *
     * @param   integer  $citation  Citation ID
     * @return  string
     */
    private function getParams($citation = 0)
    {
        $this->database->setQuery(
            "SELECT c.params from `#__citations` c WHERE id=" . $this->database->quote($citation),
        );
        return $this->database->loadResult();
    }
}
