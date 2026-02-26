<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Site\Controllers;

use Hubzero\User\Group;
use Components\Groups\Models\Page;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;
use Hubzero\Facades\Lang;
use Hubzero\Facades\App;
use Hubzero\Facades\Event;

/**
 * Groups controller class
 */
class Categories extends Base
{
    /**
     * Override Execute Method
     *
     * @return  void
     */
    public function execute()
    {
        //get the cname, active tab, and action for plugins
        $this->cn     = Request::getString('cn', '');
        $this->active = Request::getCmd('active', '');
        $this->action = Request::getCmd('action', '');

        // Check if they're logged in
        if (User::isGuest()) {
            $this->loginTask(Lang::txt('COM_GROUPS_ERROR_MUST_BE_LOGGED_IN'));
            return;
        }

        //check to make sure we have  cname
        if (!$this->cn) {
            $this->errorHandler(400, Lang::txt('COM_GROUPS_ERROR_NO_ID'));
        }

        // Load the group page
        $this->group = Group::getInstance($this->cn);

        // Ensure we found the group info
        if (!$this->group || !$this->group->get('gidNumber')) {
            $this->errorHandler(404, Lang::txt('COM_GROUPS_ERROR_NOT_FOUND'));
        }

        // Check authorization
        $isNotManager = $this->_authorize() != 'manager';
        $isNotAuthorizedForPages = !$this->_authorizedForTask('group.pages');
        if ($this->group->published == 2 || ($isNotManager && $isNotAuthorizedForPages)) {
            $this->errorHandler(403, Lang::txt('COM_GROUPS_ERROR_NOT_AUTH'));
        }

        //continue with parent execute method
        parent::execute();
    }

    /**
     * Display Page Categories
     *
     * @return void
     */
    public function displayTask()
    {
        $url = 'index.php?option=com_groups&cn=' . $this->group->get('cn')
            . '&controller=pages#categories';
        App::redirect(Route::url($url));
    }

    /**
     * Add Page Category
     *
     * @return  void
     */
    public function addTask()
    {
        $this->editTask();
    }

    /**
     * Edit Page Category
     */
    public function editTask()
    {
        // are we passing a category object
        if ($this->category) {
            $category = $this->category;
        } else {
            // get the category object
            $category = new Page\Category(Request::getInt('categoryid', 0));
        }

        // build the title
        $this->_buildTitle();

        // build pathway
        $this->_buildPathway();

        // get view notifications
        $notifications = ($this->getNotifications()) ? $this->getNotifications() : array();

        //display layout
        $this->view
            ->set('group', $this->group)
            ->set('category', $category)
            ->set('notifications', $notifications)
            ->setLayout('edit')
            ->display();
    }

    /**
     * Save Page Category
     *
     * @return  void
     */
    public function saveTask()
    {
        // get request vars
        $category = Request::getArray('category', array(), 'post');

        // add group id to category
        $category['gidNumber'] = $this->group->get('gidNumber');

        // load category object
        $this->category = new Page\Category($category['id']);

        // bind to our new results
        if (!$this->category->bind($category)) {
            $this->setNotification($this->category->getError(), 'error');
            return $this->editTask();
        }

        // Store new content
        if (!$this->category->store(true)) {
            $this->setNotification($this->category->getError(), 'error');
            return $this->editTask();
        }

        $url = Route::url(
            'index.php?option=' . $this->_option
            . '&cn=' . $this->group->get('cn')
            . '&controller=pages#categories'
        );

        // Log activity
        $recipients = array(
            ['group', $this->group->get('gidNumber')],
            ['user', User::get('id')]
        );
        foreach ($this->group->get('managers') as $recipient) {
            $recipients[] = ['user', $recipient];
        }

        $activityAction = ($category['id'] ? 'updated' : 'created');
        $activityKey = ($this->_task == 'new' ? 'CREATED' : 'UPDATED');
        $groupLink = '<a href="' . $url . '">' . $this->group->get('description') . '</a>';
        $activityDesc = Lang::txt(
            'COM_GROUPS_ACTIVITY_CATEGORY_' . $activityKey,
            $this->category->get('title'),
            $groupLink
        );
        Event::trigger('system.logActivity', [
            'activity' => [
                'action'      => $activityAction,
                'scope'       => 'group.category',
                'scope_id'    => $this->category->get('id'),
                'description' => $activityDesc,
                'details'     => array(
                    'title'     => $this->category->get('title'),
                    'url'       => $url,
                    'gidNumber' => $this->group->get('gidNumber')
                )
            ],
            'recipients' => $recipients
        ]);

        //inform user & redirect
        $this->setNotification(Lang::txt('COM_GROUPS_PAGES_CATEGORY_SAVED'), 'passed');
        App::redirect($url);
    }

    /**
     * Delete Page Category
     *
     * @return void
     */
    public function deleteTask()
    {
        // get request vars
        $categoryid = Request::getInt('categoryid', 0);

        // load category object
        $category = new Page\Category($categoryid);

        $url = Route::url(
            'index.php?option=' . $this->_option
            . '&cn=' . $this->group->get('cn')
            . '&controller=pages#categories'
        );

        // make sure this is our groups cat
        if ($category->get('gidNumber') != $this->group->get('gidNumber')) {
            $this->setNotification(Lang::txt('COM_GROUPS_PAGES_CATEGORY_DELETE_ERROR'), 'error');
            App::redirect($url);
        }

        // delete row
        if (!$category->delete()) {
            $this->setNotification($category->getError(), 'error');
            App::redirect($url);
        }

        // Log activity
        $recipients = array(
            ['group', $this->group->get('gidNumber')],
            ['user', User::get('id')]
        );
        foreach ($this->group->get('managers') as $recipient) {
            $recipients[] = ['user', $recipient];
        }

        $groupLink = '<a href="' . $url . '">' . $this->group->get('description') . '</a>';
        $deleteDesc = Lang::txt(
            'COM_GROUPS_ACTIVITY_CATEGORY_DELETED',
            $category->get('title'),
            $groupLink
        );
        Event::trigger('system.logActivity', [
            'activity' => [
                'action'      => 'deleted',
                'scope'       => 'group.category',
                'scope_id'    => $category->get('id'),
                'description' => $deleteDesc,
                'details'     => array(
                    'title'     => $category->get('title'),
                    'url'       => $url,
                    'gidNumber' => $this->group->get('gidNumber')
                )
            ],
            'recipients' => $recipients
        ]);

        //inform user & redirect
        $this->setNotification(Lang::txt('COM_GROUPS_PAGES_CATEGORY_DELETED'), 'passed');
        App::redirect($url);
    }
}
