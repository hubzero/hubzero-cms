<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Site\Controllers;

require_once Component::path('com_courses') . '/models/assets/tool.php';
use Components\Courses\Models\Assets\Tool;
use Components\Courses\Models;
use Hubzero\Component\SiteController;
use Hubzero\Content\Server;
use Hubzero\Config\Registry;
use Exception;
use Pathway;
use Request;
use Config;
use Route;
use Event;
use User;
use Lang;
use App;

/**
 * Courses controller class for an offering
 */
class Offering extends SiteController
{
    /**
     * Execute a task
     *
     * @return     void
     */
    public function execute()
    {
        $this->gid = Request::getString('gid', '');
        if (!$this->gid) {
            App::redirect(
                'index.php?option=' . $this->_option
            );
            return;
        }

        // Load the course page
        $this->course = Models\Course::getInstance($this->gid);

        // Ensure we found the course info
        $courseNotFound = !$this->course->exists()
            || $this->course->isDeleted()
            || $this->course->isUnpublished();
        if ($courseNotFound) {
            return App::abort(404, Lang::txt('COM_COURSES_NO_COURSE_FOUND'));
        }

        // No offering provided
        if (!($offering = Request::getString('offering', ''))) {
            $url = 'index.php?option=' . $this->_option
                . '&controller=course&gid=' . $this->course->get('alias');
            App::redirect(
                Route::url($url)
            );
            return;
        }

        // Ensure we found the course info
        $offeringObj = $this->course->offering($offering);
        $canManageSection = $offeringObj->access('manage', 'section');
        if (
            !$offeringObj->exists()
            || $offeringObj->isDeleted()
            || (!$canManageSection && $offeringObj->isUnpublished())
        ) {
            return App::abort(404, Lang::txt('COM_COURSES_NO_OFFERING_FOUND'));
        }

        // Ensure the course has been published or has been approved
        $canManage = $this->course->offering()->access('manage', 'section');
        if (!$canManage && !$this->course->isAvailable()) {
            return App::abort(404, Lang::txt('COM_COURSES_NOT_PUBLISHED'));
        }

        parent::execute();
    }

    /**
     * Method to set the document path
     *
     * @param      array $course_pages List of roup pages
     * @return     void
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    public function _buildPathway()
    {
        if (Pathway::count() <= 0) {
            Pathway::append(
                Lang::txt(strtoupper($this->_option)),
                'index.php?option=' . $this->_option
            );
        }

        if ($this->course->exists()) {
            $courseUrl = 'index.php?option=' . $this->_option
                . '&gid=' . $this->course->get('alias');
            Pathway::append(
                stripslashes($this->course->get('title')),
                $courseUrl
            );

            if ($this->course->offering()->exists()) {
                $offeringUrl = 'index.php?option=' . $this->_option
                    . '&gid=' . $this->course->get('alias')
                    . '&offering=' . $this->course->offering()->get('alias');
                Pathway::append(
                    stripslashes($this->course->offering()->get('title')),
                    $offeringUrl
                );
            }
        }
    }

    /**
     * Method to build and set the document title
     *
     * @return     void
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    public function _buildTitle()
    {
        //set title used in view
        $this->_title = Lang::txt(strtoupper($this->_option));

        if ($this->course->exists()) {
            $this->_title .= ': ' . stripslashes($this->course->get('title'));

            if ($this->course->offering()->exists()) {
                $offeringTitle = $this->course->offering()->get('title');
                $this->_title .= ': ' . stripslashes($offeringTitle);
            }
        }

        //set title of browser window
        \Document::setTitle($this->_title);
    }

    /**
     * Redirect to login page
     *
     * @return     void
     */
    public function loginTask($message = '')
    {
        $offeringLink = $this->course->offering()->link() . '&task=' . $this->_task;
        $rtrn = base64_encode(Route::url($offeringLink, false, true));
        $loginUrl = 'index.php?option=com_users&view=login&return=' . $rtrn;
        $link = str_replace('&amp;', '&', Route::url($loginUrl));
        App::redirect(
            $link,
            Lang::txt($message),
            'warning'
        );
        return;
    }

    /**
     * View a course
     *
     * @return     void
     */
    public function displayTask()
    {
        // Check if the offering is available
        $canManage = $this->course->offering()->access('manage', 'section');
        $isAvailable = $this->course->offering()->isAvailable();
        if (!$canManage && !$isAvailable) {
            return App::abort(404, Lang::txt('COM_COURSES_NO_OFFERING_FOUND'));
        }

        $tmpl = $this->config->get('tmpl', '');

        if ($tmpl && !Request::getWord('tmpl', false)) {
            Request::setVar('tmpl', $tmpl);
        }

        // Get the active tab (section)
        $this->view->nonadmin = 0;
        if ($this->course->offering()->access('manage', 'section')) {
            $offeringId = $this->course->offering()->get('id');
            $stateKey = $this->_option . '.offering' . $offeringId . '.nonadmin';
            $this->view->nonadmin = Request::getInt('nonadmin', User::getState(
                $stateKey,
                0
            ));
            User::setState(
                $stateKey,
                $this->view->nonadmin
            );
        }

        // Build the title
        $this->_buildTitle();

        // Build pathway
        $this->_buildPathway();

        // If an active area was specified
        // check that it's a valid plugin
        if ($active = Request::getCmd('active')) {
            $plugins = Event::trigger('courses.onCourse', array(
                $this->course,
                $this->course->offering(),
                true
            ));
            foreach ($plugins as $plugin) {
                $available[] = $plugin->get('name');
            }
            if (!in_array($active, $available)) {
                App::abort(404, Lang::txt('COM_COURSES_ERROR_PAGE_NOT_FOUND'));
            }
        }

        // Trigger the functions that return the areas we'll be using
        $plugins = Event::trigger('courses.onCourse', array(
            $this->course,
            $this->course->offering()
        ));

        $this->view->course        = $this->course;
        $this->view->config        = $this->config;
        $this->view->plugins       = $plugins;
        $this->view->notifications = \Notify::messages('courses');
        $this->view->display();
    }

    /**
     * Display an offering asset
     *
     * @return     void
     */
    public function enrollTask()
    {
        // Check if they're logged in
        if (User::isGuest()) {
            $this->loginTask(Lang::txt('COM_COURSES_ENROLLMENT_REQUIRES_LOGIN'));
            return;
        }

        $offering = $this->course->offering();

        // Is the user a manager or student?
        if ($offering->isManager() || $offering->isStudent()) {
            // Yes! Already enrolled
            // Redirect back to the course page
            App::redirect(
                Route::url($offering->link()),
                Lang::txt('COM_COURSES_ALREADY_ENROLLED')
            );
            return;
        }

        $this->view->course = $this->course;

        // Build the title
        $this->_buildTitle();

        // Build pathway
        $this->_buildPathway();

        // Can the user enroll?
        if (!$offering->section()->canEnroll()) {
            $this->view->setLayout('enroll_closed');
            $this->view->display();
            return;
        }

        $enrolled = false;

        // If enrollment is open OR a coupon code was posted
        $enrollment = $offering->section()->get('enrollment');
        $code = Request::getString('code', '');
        if (!$enrollment || $code) {
            $section_id = $offering->section()->get('id');

            // If a coupon code was posted
            if (isset($code)) {
                // Get the coupon
                $coupon = $offering->section()->code($code);
                // Is it a valid code?
                if (!$coupon->exists()) {
                    $errorMsg = Lang::txt('COM_COURSES_ERROR_CODE_INVALID', $code);
                    $this->setError($errorMsg);
                }
                // Has it already been redeemed?
                if ($coupon->isRedeemed()) {
                    $redeemedKey = 'COM_COURSES_ERROR_CODE_ALREADY_REDEEMED';
                    $errorMsg = Lang::txt($redeemedKey, $code);
                    $this->setError($errorMsg);
                } else {
                    // Has it expired?
                    if ($coupon->isExpired()) {
                        $expiredKey = 'COM_COURSES_ERROR_CODE_EXPIRED';
                        $expiredMsg = Lang::txt($expiredKey, $code);
                        $this->setError($expiredMsg);
                    }
                }
                if (!$this->getError()) {
                    // Is this a coupon for a different section?
                    $sectionId = $offering->section()->get('id');
                    if ($sectionId != $coupon->get('section_id')) {
                        $couponSectionId = $coupon->get('section_id');
                        $SectionModel = Models\Section::class;
                        $section = $SectionModel::getInstance($couponSectionId);
                        $sectionOfferingId = $section->get('offering_id');
                        $offeringId = $offering->get('id');
                        $offeringIdsDiffer = $sectionOfferingId != $offeringId;
                        if ($section->exists() && $offeringIdsDiffer) {
                            $offering = Models\Offering::getInstance(
                                $sectionOfferingId
                            );
                            $offeringCourseId = $offering->get('course_id');
                            $thisCourseId = $this->course->get('id');
                            $courseIdsDiffer = $offeringCourseId != $thisCourseId;
                            if ($offering->exists() && $courseIdsDiffer) {
                                $this->course = Models\Course::getInstance(
                                    $offeringCourseId
                                );
                            }
                        }
                        $enrollUrl = $offering->link()
                            . '&task=enroll&code=' . $code;
                        App::redirect(
                            Route::url($enrollUrl)
                        );
                        return;
                    }
                    // Redeem the code
                    // set('redeemed_by', User::get('id'));
                    $coupon->redeem(User::get('id'));
                    //$coupon->store();
                }
            }

            // If no errors
            if (!$this->getError()) {
                // Add the user to the course
                // Previously used Models\Member::getInstance with User::get('id')
                $MemberModel = Models\Member::class;
                $model = new $MemberModel(0);
                $model->set('user_id', User::get('id'));
                $model->set('course_id', $this->course->get('id'));
                $model->set('offering_id', $offering->get('id'));
                $model->set('section_id', $offering->section()->get('id'));
                if ($roles = $offering->roles()) {
                    foreach ($roles as $role) {
                        if ($role->alias == 'student') {
                            $model->set('role_id', $role->id);
                            break;
                        }
                    }
                }
                $model->set('student', 1);
                if ($model->store(true)) {
                    $enrolled = true;
                } else {
                    $this->setError($model->getError());
                }
            }
        }

        if ($enrolled) {
            $link = $offering->link();

            $data = Event::trigger('courses.onCourseEnrolled', array(
                $this->course, $offering, $offering->section()
            ));
            if ($data && count($data) > 0) {
                $link = implode('', $data);
            }
            App::redirect(
                Route::url($link)
            );
            return;
        }

        // If enrollment is srestricted and the user isn't enrolled yet
        if ($offering->section()->get('enrollment') == 1 && !$enrolled) {
            // Show a form for entering a coupon code
            $this->view->setLayout('enroll_restricted');
        }

        if ($this->getError()) {
            \Notify::error($this->getError(), 'courses');
        }
        $this->view->notifications = \Notify::messages('courses');
        $this->view->display();
    }

    /**
     * Show a form for editing a course
     *
     * @return     void
     */
    public function newTask()
    {
        $this->editTask();
    }

    /**
     * Show a form for editing a course
     *
     * @return     void
     */
    public function editTask()
    {
        $this->view->notifications = \Notify::messages('courses');
        $this->view
            ->setLayout('edit')
            ->display();
    }

    /**
     * Display an offering asset
     *
     * @return     void
     */
    public function assetTask()
    {
        $sectionParams = $this->course->offering()->section()->get('params');
        $sparams    = new Registry($sectionParams);
        $section_id = $this->course->offering()->section()->get('id');
        $asset      = new Models\Asset(Request::getInt('asset_id', null));
        $asset->set('section_id', $section_id);

        // First, check if current user has access to course
        if (!$this->course->offering()->access('view')) {
            // Is a preview available?
            $preview = $sparams->get('preview', 0);

            $enrollKey = 'COM_COURSES_ENROLLMENT_REQUIRED_FOR_ASSET';
            $enrollmentRequiredMsg = Lang::txt($enrollKey);
            // If no preview is available or if type is form (can't preview forms)
            if (!$preview || $asset->get('type') == 'form') {
                // Check if they're logged in
                if (User::isGuest()) {
                    $this->loginTask($enrollmentRequiredMsg);
                    return;
                } else {
                    // Redirect back to the course outline
                    App::redirect(
                        Route::url($this->course->offering()->link()),
                        $enrollmentRequiredMsg,
                        'warning'
                    );
                    return;
                }
            } elseif ($preview == 2) { // Preview is only for first unit
                $units = $asset->units();
                if ($units && count($units) > 0) {
                    foreach ($units as $unit) {
                        if ($unit->get('ordering') > 1) {
                            // Check if they're logged in
                            if (User::isGuest()) {
                                $this->loginTask($enrollmentRequiredMsg);
                                return;
                            } else {
                                // Redirect back to the course outline
                                App::redirect(
                                    Route::url($this->course->offering()->link()),
                                    $enrollmentRequiredMsg,
                                    'warning'
                                );
                                return;
                            }
                        }
                    }
                }
            }
        }

        // If not a manager and either the offering or section is unpublished...
        $canManage = $this->course->offering()->access('manage');
        $offeringPublished = $this->course->offering()->isPublished();
        $sectionPublished = $this->course->offering()->section()->isPublished();
        if (!$canManage && (!$offeringPublished || !$sectionPublished)) {
            $unavailableMsg = Lang::txt('COM_COURSES_ERROR_ASSET_UNAVAILABLE');
            return App::abort(403, $unavailableMsg);
        }

        $canManageAsset = $this->course->offering()->access('manage');
        if (!$canManageAsset && !$asset->isAvailable()) {
            // Allow expired forms to pass through (so students can see results)
            $isForm = $asset->get('type') == 'form';
            if (!$isForm || !$asset->ended()) {
                // Redirect back to the course outline
                App::redirect(
                    Route::url($this->course->offering()->link()),
                    Lang::txt('COM_COURSES_ERROR_ASSET_UNAVAILABLE'),
                    'warning'
                );
                return;
            }
        }

        // Check prerequisites
        $userId = User::get('id');
        $member = $this->course->offering()->section()->member($userId);
        if (is_null($member->get('section_id'))) {
            $member->set('section_id', $section_id);
        }
        $gradebook = $this->course->offering()->gradebook();
        $prerequisites = $member->prerequisites($gradebook);

        $assetId = $asset->get('id');
        $hasMet = $prerequisites->hasMet('asset', $assetId);
        if (!$canManageAsset && !$hasMet) {
            $prereqs      = $prerequisites->get('asset', $assetId);
            $requirements = array();
            foreach ($prereqs as $pre) {
                $reqAsset = new Models\Asset($pre['scope_id']);
                $requirements[] = $reqAsset->get('title');
            }

            $requirements = implode(', ', $requirements);

            // Redirect back to the course outline
            $prereqKey = 'COM_COURSES_ERROR_ASSET_HAS_PREREQ';
            $prereqMsg = Lang::txt($prereqKey, $requirements);
            App::redirect(
                Route::url($this->course->offering()->link()),
                $prereqMsg,
                'warning'
            );
            return;
        }

        // If requesting a file from a wiki type asset, then serve that up directly
        $isWiki = $asset->get('subtype') == 'wiki';
        if ($isWiki && Request::getString('file', false)) {
            echo $asset->download($this->course);
        }

        echo $asset->render($this->course);
    }

    /**
     * Save a course
     *
     * @return     void
     */
    public function saveTask()
    {
        // Check if they're logged in
        if (User::isGuest()) {
            $this->loginTask(Lang::txt('COM_COURSES_NOT_LOGGEDIN'));
            return;
        }

        // Redirect back to the course page
        $url = 'index.php?option=' . $this->_option
            . '&controller=' . $this->_controller
            . '&gid=' . $this->course->get('alias')
            . '&task=offerings';
        App::redirect(
            Route::url($url)
        );
    }

    /**
     * Delete a course
     * This method initially displays a form for confirming deletion
     * then deletes course and associated information upon POST
     *
     * @return     void
     */
    public function deleteTask()
    {
        $url = 'index.php?option=' . $this->_option
            . '&controller=' . $this->_controller
            . '&gid=' . $this->course->get('alias')
            . '&task=offerings';
        App::redirect(
            Route::url($url)
        );
    }

    /**
     * Serve up an offering logo
     *
     * @return  void
     */
    public function logoTask()
    {
        if (!($logo = $this->course->offering()->section()->logo())) {
            $logo = $this->course->offering()->logo();
        }
        $file = PATH_APP . $logo;

        // Initiate a new content server and serve up the file
        $server = new Server();
        $server->filename($file);
        $server->disposition('inline');
        $server->acceptranges(false);

        if (!$server->serve()) {
            // Should only get here on error
            throw new Exception(Lang::txt('COM_COURSES_SERVER_ERROR'), 404);
        }

        exit;
    }
}
