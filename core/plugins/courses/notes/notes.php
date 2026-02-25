<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Courses\Notes;

use Hubzero\Plugin\Plugin;
use Hubzero\Utility\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;

/**
 * Courses Plugin class for notes
 */
class Notes extends Plugin
{
    /**
     * Constructor
     *
     * @param   object  &$subject  The object to observe
     * @param   array   $config    An optional associative array of configuration settings
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        require_once __DIR__ . DS . 'models' . DS . 'note.php';
    }

    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_autoloadLanguage = true;

    /**
     * View object
     *
     * @var object
     */
    public $view = null;

    /**
     * Course object
     *
     * @var object
     */
    protected $course = null;

    /**
     * Offering object
     *
     * @var object
     */
    protected $offering = null;

    /**
     * Return data on a course view (this will be some form of HTML)
     *
     * @param   object   $course    Current course
     * @param   object   $offering  Name of the component
     * @param   boolean  $describe  Return plugin description only?
     * @return  object
     */
    public function onCourse($course, $offering, $describe = false)
    {
        $response = with(new \Hubzero\Base\Obj())
            ->set('name', $this->_name)
            ->set('title', Lang::txt('PLG_COURSES_' . strtoupper($this->_name)))
            ->set('description', Lang::txt('PLG_COURSES_' . strtoupper($this->_name) . '_BLURB'))
            ->set('default_access', $this->params->get('plugin_access', 'members'))
            ->set('display_menu_tab', true)
            ->set('icon', '270D');

        if ($describe) {
            return $response;
        }

        if (!($active = Request::getString('active'))) {
            Request::setVar('active', ($active = $this->_name));
        }

        if ($response->get('name') == $active) {
            $this->course = $course;
            $this->offering = $offering;

            $this->view = $this->view('default', 'notes')
                ->set('option', Request::getCmd('option', 'com_courses'))
                ->set('controller', Request::getWord('controller', 'course'))
                ->set('course', $course)
                ->set('offering', $offering)
                ->set('no_html', Request::getInt('no_html', 0));

            $this->view->filters = array(
                'section_id' => $offering->section()->get('id'),
                'search' => Request::getString('search', '')
            );

            if ($action = strtolower(Request::getWord('action', ''))) {
                switch ($action) {
                    case 'add':
                        $result = $this->edit();
                        break;
                    case 'edit':
                        $result = $this->edit();
                        break;
                    case 'save':
                        $result = $this->_save();
                        break;
                    case 'delete':
                        $result = $this->_delete();
                        break;
                    case 'download':
                        $result = $this->_download();
                        break;

                    default:
                        $result = $this->listNotes();
                        break;
                }
            }

            if ($this->view->no_html && $result) {
                $note = new \stdClass();
                $note->id = $result;
                $note->success = true;
                if ($this->getError()) {
                    $note->success = false;
                    $note->error = $this->getError();
                }

                ob_clean();
                return $response;
            }

            $response->set('html', $this->view->loadTemplate());
        }

        // Return the output
        return $response;
    }

    /**
     * After lecture event
     *
     * @param   object  $course
     * @param   object  $unit
     * @param   object  $lecture
     * @return  string
     */
    public function onCourseAfterLecture($course, $unit, $lecture)
    {
        if (!$course->offering()->section()->access('view')) {
            return '';
        }

        $this->view = $this->view('default', 'lecture')
            ->set('course', $course)
            ->set('offering', $course->offering())
            ->set('unit', $unit)
            ->set('lecture', $lecture);

        return $this->view->loadTemplate();
    }

    /**
     * Set layout to the listing
     */
    public function listNotes()
    {
        if (!$this->view->no_html) {
            $this->view->setLayout('default');
        }
    }

    /**
     * Download
     */
// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    public function _download()
    {
        $format = strtolower(Request::getWord('frmt', 'txt'));
        if (!in_array($format, array('txt', 'csv'))) {
            $format = 'txt';
        }
        $this->view->setLayout('download_' . $format);

        @ob_end_clean();

        header("Pragma: public");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Expires: 0");

        header("Content-Transfer-Encoding: binary");
        header('Content-Disposition:attachment; filename="notes.' . $format . '";'); //RFC2183
        header("Content-Type: text/plain"); // MIME type

        echo $this->view->loadTemplate();
        die();
    }

    /**
     * Set layout to the edit view
     *
     * @param   mixed  $model
     */
    public function edit($model = null)
    {
        if (!($model instanceof \Plugins\Courses\Notes\Models\Note)) {
            $note_id = Request::getInt('note', 0);

            $model = \Plugins\Courses\Notes\Models\Note::oneOrNew($note_id);
        }

        $this->view->set('model', $model);

        if (!$this->view->no_html) {
            $this->view->setLayout('edit');
        }
    }

    /**
     * Save a record
     *
     * @return  mixed
     */
// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    public function _save()
    {
        $note_id = Request::getInt('note', 0);

        $model = \Plugins\Courses\Notes\Models\Note::oneOrNew($note_id);

        if ($scope = Request::getWord('scope', 'lecture')) {
            $model->set('scope', $scope);
        }
        if ($scope_id = Request::getInt('scope_id', 0)) {
            $model->set('scope_id', $scope_id);
        }
        if ($pos_x = Request::getInt('x', 0)) {
            $model->set('pos_x', $pos_x);
        }
        if ($pos_y = Request::getInt('y', 0)) {
            $model->set('pos_y', $pos_y);
        }
        if ($width = Request::getInt('w', 0)) {
            $model->set('width', $width);
        }
        if ($height = Request::getInt('h', 0)) {
            $model->set('height', $height);
        }
        if ($state = Request::getInt('state', 0)) {
            $model->set('state', $state);
        }
        if ($timestamp = Request::getString('time', '')) {
            $model->set('timestamp', $timestamp);
        }
        if ($content = Request::getString('txt', '')) {
            $model->set('content', $content);
        }
        $model->set('access', Request::getInt('access', 0));
        $model->set('section_id', $this->view->offering->section()->get('id'));

        if (!$model->save()) {
            $this->setError($model->getError());
            if (!$this->view->no_html) {
                return $this->edit($model);
            }
        }

        if (!$this->view->no_html) {
            return $this->listNotes();
        }

        return $model->get('id');
    }

    /**
     * Delete a record
     *
     * @return  mixed
     */
// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    public function _delete()
    {
        $note_id = Request::getInt('note', 0);

        $model = \Plugins\Courses\Notes\Models\Note::oneOrFail($note_id);
        $model->set('state', 2);
        if (!$model->save()) {
            $this->setError($model->getError());
        }

        if (!$this->view->no_html) {
            return $this->listNotes();
        }
        return $note_id;
    }
}
