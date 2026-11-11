<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Feedback\Site\Controllers;

use Components\Feedback\Models\Quote;
use Hubzero\Component\SiteController;
use Hubzero\Utility\Str;
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Request;
use Route;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;
use Hubzero\Facades\Component;

/**
 * Feedback controller class for media management
 *
 * Set on the controller as it runs:
 *
 * @property  string  $path  Where quote pictures are kept, set by execute()
 */
class Media extends SiteController
{
    /**
     * Determine task and execute it
     *
     * @return  void
     */
    public function execute()
    {
        $row = Quote::oneOrNew(0);
        $this->path = $row->filespace();

        parent::execute();
    }

    /**
     * Upload an image
     *
     * @return  void
     */
    public function uploadTask()
    {
        // Check for request forgeries
        Request::checkToken();

        if (User::isGuest()) {
            $this->setError(Lang::txt('COM_FEEDBACK_NOTAUTH'));
            $this->displayTask('', 0);

            return;
        }

        // Incoming
        if (!($id = Request::getInt('id', 0))) {
            $this->setError(Lang::txt('COM_FEEDBACK_NO_ID'));
            $this->displayTask('', $id);

            return;
        }

        // Only act on the current user's own quote space
        if (User::get('id') != $id) {
            $this->setError(Lang::txt('COM_FEEDBACK_NOTAUTH'));
            $this->displayTask('', User::get('id'));

            return;
        }

        // Incoming file
        $file = Request::getArray('upload', [], 'files');
        if (empty($file['name'])) {
            $this->setError(Lang::txt('COM_FEEDBACK_NO_FILE'));
            $this->displayTask('', $id);

            return;
        }

        // Build upload path
        $path = $this->path . DS . $id;

        if (!is_dir($path)) {
            if (!Filesystem::makeDirectory($path)) {
                $this->setError(Lang::txt('COM_FEEDBACK_UNABLE_TO_CREATE_UPLOAD_PATH'));
                $this->displayTask('', $id);

                return;
            }
        }

        // Get media config
        $mediaConfig = Component::params('com_media');

        // Size limit is in MB, so we need to turn it into just B
        $sizeLimit = $mediaConfig->get('upload_maxsize', 10);
        $sizeLimit = $sizeLimit * 1024 * 1024;

        if ($file['size'] > $sizeLimit) {
            $formattedSize = \Hubzero\Utility\Number::formatBytes($sizeLimit);
            $this->setError(Lang::txt('COM_FEEDBACKI_ERROR_UPLOADING_FILE_TOO_BIG', $formattedSize));
            $this->displayTask();

            return;
        }

        // Make the filename safe
        $file['name'] = urldecode($file['name']);
        $file['name'] = Filesystem::clean($file['name']);
        $file['name'] = str_replace(' ', '_', $file['name']);
        $ext = Filesystem::extension($file['name']);

        // Check that the file type is allowed
        $allowed = array_values(array_filter(explode(',', $mediaConfig->get('upload_extensions'))));

        if (!empty($allowed) && !in_array(strtolower($ext), $allowed)) {
            $this->setError(Lang::txt('COM_FEEDBACK_ERROR_UPLOADING_INVALID_FILE', implode(', ', $allowed)));
            $this->displayTask();

            return;
        }

        // Do we have an old file we're replacing?
        $curfile = basename(Request::getString('currentfile', ''));

        // Perform the upload
        if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name'])) {
            $this->setError(Lang::txt('COM_FEEDBACK_ERROR_UPLOADING'));
            $file = $curfile;
        } else {
            if ($curfile != '' && file_exists($path . DS . $curfile)) {
                if (!Filesystem::delete($path . DS . $curfile)) {
                    $this->setError(Lang::txt('COM_FEEDBACK_UNABLE_TO_DELETE_FILE'));
                    $this->displayTask($file['name'], $id);

                    return;
                }
            }

            $file = $file['name'];
        }

        // Push through to the image view
        $this->displayTask($file, $id);
    }

    /**
     * Delete an image
     *
     * @return  void
     */
    public function deleteTask()
    {
        // Check for request forgeries
        Request::checkToken();

        if (User::isGuest()) {
            $this->setError(Lang::txt('COM_FEEDBACK_NOTAUTH'));
            $this->displayTask('', 0);

            return;
        }

        // Incoming member ID
        if (!($id = Request::getInt('id', 0))) {
            $this->setError(Lang::txt('COM_FEEDBACK_NO_ID'));
            $this->displayTask('', $id);

            return;
        }

        if (User::get('id') != $id) {
            $this->setError(Lang::txt('COM_FEEDBACK_NOTAUTH'));
            $this->displayTask('', User::get('id'));

            return;
        }

        // Incoming file
        if (!($file = Request::getString('file', ''))) {
            $this->setError(Lang::txt('COM_FEEDBACK_NO_FILE'));
            $this->displayTask($file, $id);

            return;
        }

        $file = basename($file);

        // Build the file path
        $path = $this->path . DS . $id;

        if (!file_exists($path . DS . $file) or !$file) {
            $this->setError(Lang::txt('COM_FEEDBACK_FILE_NOT_FOUND'));
        } else {
            // Attempt to delete the file
            if (!Filesystem::delete($path . DS . $file)) {
                $this->setError(Lang::txt('COM_FEEDBACK_UNABLE_TO_DELETE_FILE'));
                $this->displayTask($file, $id);

                return;
            }

            $file = '';
        }

        // Push through to the image view
        $this->displayTask($file, $id);
    }

    /**
     * Display a form for uploading an image and any data for current uploaded image
     *
     * @param   string   $file  Image name
     * @param   integer  $id    User ID
     */
    public function displayTask($file = '', $id = 0)
    {
        // Do have an ID or do we need to get one?
        if (!$id) {
            $id = Request::getInt('id', 0);
        }
        $dir = Str::pad($id);

        // Do we have a file or do we need to get one?
        $file = basename($file ?: Request::getString('file', ''));

        // Build the directory path
        $path = $this->path . DS . $dir;

        // Output view
        $this->view
            ->set('webpath', $this->config->get('uploadpath', '/site/quotes'))
            ->set(
                'default_picture',
                $this->config->get('defaultpic', '/core/components/com_feedback/site/assets/img/contributor.gif'),
            )
            ->set('path', $dir)
            ->set('file', $file)
            ->set('file_path', $path)
            ->set('id', $id)
            ->setErrors($this->getErrors())
            ->setLayout('display')
            ->display();
    }
}
