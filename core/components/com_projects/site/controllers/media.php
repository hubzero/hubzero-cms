<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Site\Controllers;

use Components\Projects\Helpers;
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;
use Hubzero\Facades\App;

/**
 * Projects media controller class
 */
class Media extends Base
{
    /**
     * Determines task being called and attempts to execute it
     *
     * @return  void
     */
    public function execute()
    {
        // Set the default task
        $this->registerTask('__default', 'media');
        $this->registerTask('thumb', 'media');

        parent::execute();
    }

    /**
     * Upload a file to the profile via AJAX
     *
     * @return  string|null
     */
    public function doajaxuploadTask()
    {
        //allowed extensions for uplaod
        $allowedExtensions = ['png', 'jpe', 'jpeg', 'jpg', 'gif'];

        //max upload size
        $sizeLimit = $this->config->get('maxAllowed', '40000000');

        // get the file
        if (isset($_GET['qqfile'])) {
            $stream = true;
            $file = $_GET['qqfile'];
            $size = (int) ($_SERVER["CONTENT_LENGTH"] ?? 0);
        } elseif (isset($_FILES['qqfile'])) {
            $stream = false;
            $file = $_FILES['qqfile']['name'];
            $size = (int) $_FILES['qqfile']['size'];
        } else {
            echo json_encode(['error' => Lang::txt('Please select a file to upload')]);
            return null;
        }

        //check to make sure we have a file and its not too big
        if ($size == 0) {
            echo json_encode(['error' => Lang::txt('File is empty')]);
            return null;
        }
        if ($size > $sizeLimit) {
            $max = preg_replace(
                '/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/',
                '$1',
                \Hubzero\Utility\Number::formatBytes($sizeLimit),
            );
            echo json_encode(['error' => Lang::txt('File is too large. Max file upload size is ') . $max]);
            return null;
        }

        //check to make sure we have an allowable extension
        $pathinfo = pathinfo($file);
        $filename = $pathinfo['filename'];
        $ext      = $pathinfo['extension'] ?? '';
        if (!in_array(strtolower($ext), $allowedExtensions)) {
            $these = implode(', ', $allowedExtensions);
            echo json_encode(
                ['error' => Lang::txt('File has an invalid extension, it should be one of ' . $these . '.')],
            );
            return null;
        }

        // Make the filename safe
        $file = Filesystem::clean($file);

        // Check project exists
        if (!$this->model->exists()) {
            echo json_encode(['error' => Lang::txt('Error loading project')]);
            return null;
        }

        // Make sure user is authorized (project manager)
        if (
            !$this->model->access('manager')
            && !($this->model->access('content') && $this->config->get('edit_description'))
        ) {
            echo json_encode(['error' => Lang::txt('Unauthorized action')]);
            return null;
        }

        // Build project image path
        $path  = PATH_APP . DS . trim($this->config->get('imagepath', '/site/projects'), DS);
        $path .= DS . $this->model->get('alias') . DS . 'images';

        if (!is_dir($path)) {
            if (!Filesystem::makeDirectory($path, 0o755, true, true)) {
                echo json_encode(['error' => Lang::txt('COM_PROJECTS_UNABLE_TO_CREATE_UPLOAD_PATH')]);
                return null;
            }
        }

        // Delete older file with same name
        if (file_exists($path . DS . $file)) {
            //Filesystem::delete($path . DS . 'original.' . Filesystem::extension($file));
            Filesystem::delete($path . DS . $file);
        }

        if ($stream) {
            //read the php input stream to upload file
            // Save the request body, the upload's contents, to the file
            if (!\Hubzero\Filesystem\Util::saveRequestBody($path . DS . $file)) {
                echo json_encode(['error' => Lang::txt('Unable to save the uploaded file.')]);
                return null;
            }

            // Scan it where it landed; virusCheck() deletes an infected file
            if (Helpers\Html::virusCheck($path . DS . $file)) {
                echo json_encode(['error' => Lang::txt('Virus detected, refusing to upload')]);
                return null;
            }
        } else {
            move_uploaded_file($_FILES['qqfile']['tmp_name'], $path . DS . $file);
        }

        // Perform the upload
        if (!is_file($path . DS . $file)) {
            echo json_encode(['error' => Lang::txt('COM_PROJECTS_ERROR_UPLOADING')]);
            return null;
        } else {
            //resize image to max 200px and rotate in case user didnt before uploading
            $hi = new \Hubzero\Image\Processor($path . DS . $file);
            if (count($hi->getErrors()) == 0) {
                // Delete previous master
                if (file_exists($path . DS . 'master.png')) {
                    Filesystem::delete($path . DS . 'master.png');
                }

                // PHP versions < 7 has issues with reading EXIF data
                // So, we want to try and catch those specific errors
                // and ignore them.
                try {
                    $hi->autoRotate();
                } catch (\Exception $e) {
                    if (
                        str_contains($e->getMessage(), 'Illegal IFD size')
                        || str_contains($e->getMessage(), 'Illegal IFD offset')
                    ) {
                        // PURR #1186, QUBES #618
                        // Ignore. Let's just move on.
                    } else {
                        echo json_encode(['error' => $e->getMessage()]);
                        return null;
                    }
                }

                try {
                    $hi->resize(200);
                    $hi->setImageType(IMAGETYPE_PNG);
                    $hi->save($path . DS . 'master.png');// $file);
                } catch (\Exception $e) {
                    echo json_encode(['error' => $e->getMessage()]);
                    return null;
                }
            } else {
                echo json_encode(['error' => $hi->getError()]);
                return null;
            }

            // Delete previous thumb
            if (file_exists($path . DS . 'thumb.png')) {
                Filesystem::delete($path . DS . 'thumb.png');
            }

            // create thumb
            $hi = new \Hubzero\Image\Processor($path . DS . $file);
            if (count($hi->getErrors()) == 0) {
                $hi->resize(50, false, true, true);
                try {
                    $hi->save($path . DS . 'thumb.png');
                } catch (\Exception $e) {
                    echo json_encode(['error' => $e->getMessage()]);
                    return null;
                }
            } else {
                echo json_encode(['error' => $hi->getError()]);
                return null;
            }

            // Save picture name
            $this->model->set('picture', $file);
            if (!$this->model->store()) {
                echo json_encode(['error' => $this->model->getError()]);
                return null;
            } elseif (!$this->model->inSetup()) {
                // Record activity
                $this->model->recordActivity(Lang::txt('COM_PROJECTS_REPLACED_PROJECT_PICTURE'));
            }
        }

        echo json_encode([
            'success' => true,
        ]);
        return null;
    }

    /**
     * Delete image
     *
     * @return  void
     */
    public function deleteimgTask()
    {
        // Check for request forgeries
        Request::checkToken();

        // Incoming
        $ajax = Request::getInt('ajax', 0);

        // Check if they are logged in
        if (User::isGuest()) {
            if ($ajax) {
                echo json_encode(['error' => Lang::txt('User login required')]);
                return;
            }
            $this->showError();
            return;
        }

        // Incoming project ID
        $hasAccess = (
            $this->model->access('manager')
            || ($this->model->access('content') && $this->config->get('edit_description'))
        );

        if (!$this->model->exists() || !$hasAccess) {
            $this->setError(Lang::txt('COM_PROJECTS_ERROR_NO_ID'));
            if ($ajax) {
                echo json_encode(['error' => $this->getError()]);
                return;
            }
            $this->showError();
            return;
        }

        // Incoming file
        // Incoming file (bare filename only - no traversal)
        $file = basename(Request::getString('file', ''));
        $file = $file ? $file : $this->model->get('picture');
        if (!$file) {
            $this->setError(Lang::txt('COM_PROJECTS_FILE_NOT_FOUND'));
            if ($ajax) {
                echo json_encode(['error' => $this->getError()]);
                return;
            }
            $this->showError();
            return;
        }

        // Build path
        $webdir = DS . trim($this->config->get('imagepath', '/site/projects'), DS);
        $path   = PATH_APP . $webdir . DS . $this->model->get('alias') . DS . 'images';

        if (!file_exists($path . DS . $file)) {
            $this->setError(Lang::txt('COM_PROJECTS_FILE_NOT_FOUND'));
            if ($ajax) {
                echo json_encode(['error' => $this->getError()]);
                return;
            }
        } else {
            // Attempt to delete the original file
            if (!Filesystem::delete($path . DS . $file)) {
                $this->setError(Lang::txt('COM_PROJECTS_UNABLE_TO_DELETE_FILE'));
                if ($ajax) {
                    echo json_encode(['error' => $this->getError()]);
                    return;
                }
                $this->showError();
                return;
            }

            // Delete thumbnail
            $curthumb = Helpers\Html::createThumbName($file);
            $curthumb = file_exists($path . DS . $curthumb) ? $curthumb : 'thumb.png';
            if (file_exists($path . DS . $curthumb)) {
                if (!Filesystem::delete($path . DS . $curthumb)) {
                    $this->setError(Lang::txt('COM_PROJECTS_UNABLE_TO_DELETE_FILE'));
                    if ($ajax) {
                        echo json_encode(['error' => $this->getError()]);
                        return;
                    }
                    $this->showError();
                    return;
                }
            }

            // Delete master
            if (file_exists($path . DS . 'master.png')) {
                if (!Filesystem::delete($path . DS . 'master.png')) {
                    $this->setError(Lang::txt('COM_PROJECTS_UNABLE_TO_DELETE_FILE'));
                    if ($ajax) {
                        echo json_encode(['error' => $this->getError()]);
                        return;
                    }
                    $this->showError();
                    return;
                }
            }

            // Instantiate a project, change some info and save; the file
            // was deleted above
            $this->model->set('picture', '');
            if (!$this->model->store()) {
                $this->setError($this->model->getError());
                if ($ajax) {
                    echo json_encode(['error' => $this->model->getError()]);
                    return;
                }
                return;
            }
        }

        if ($ajax && $this->getError()) {
            echo json_encode(['error' => $this->getError()]);
            return;
        } elseif ($ajax) {
            echo json_encode([
                'success' => true,
            ]);
            return;
        }

        // Go to error page
        if ($this->getError()) {
            $this->showError();
        }

        // Return to project page
        App::redirect(Route::url($this->model->link(), false));
    }

    /**
     * Show images within projects
     *
     * @return  bool|null
     */
    public function mediaTask()
    {
        // Incoming
        $media    = trim(Request::getString('media', 'thumb'));
        $source   = null;
        $redirect = false;
        $dir      = 'preview';

        if (!$this->model->exists()) {
            return false;
        }

        $uri = Request::getString('SCRIPT_URL', '', 'server');
        if (strstr($uri, 'Compiled:')) {
            $media = str_replace('Compiled:', '', (string) strstr($uri, 'Compiled:'));
            $dir   = 'compiled';
        }
        if (strstr($uri, 'Tool:')) {
            $media = str_replace('Tool:', '', (string) strstr($uri, 'Tool:'));
            $media = $media ? $media : 'default';
            $dir   = 'tools';
        }

        // Show project thumbnail
        if ($media == 'thumb' || $media == 'master') {
            $source = $this->model->picture($media, true);
        } elseif ($media) {
            if ($dir == 'tools') {
                $path     = trim($this->config->get('imagepath', '/site/projects'), DS);
                $source   = PATH_APP . DS . $path . DS . $this->model->get('alias') . DS . $dir . DS . $media . '.png';

                if (!is_file($source)) {
                    // Get default tool image
                    $source = PATH_CORE
                        . DS
                        . trim($this->config->get('toolpic', 'plugins/projects/tools/images/default.gif'), DS);
                }
            } else {
                // Authorization required
                if (!$this->model->access('member')) {
                    return null;
                }

                // basename after urldecode, as deleteimgTask does: the value
                // arrives percent-encoded from the SCRIPT_URL branches above, so
                // %2e%2e%2f becomes ../ here and Hubzero\Content\Server performs
                // no containment of its own -- this serves the file inline.
                $path     = trim($this->config->get('imagepath', '/site/projects'), DS);
                $source   = PATH_APP
                    . DS
                    . $path
                    . DS
                    . $this->model->get('alias')
                    . DS
                    . $dir
                    . DS
                    . basename(str_replace('\\', '/', urldecode($media)));
                $redirect = true;
            }
        }

        if (is_file((string) $source)) {
            $server = new \Hubzero\Content\Server();
            $server->filename($source);
            $server->serveInline((string) $source);
            exit();
        } elseif ($redirect) {
            App::redirect(
                Route::url('index.php?option=' . $this->option, false),
            );
        }

        return null;
    }
}
