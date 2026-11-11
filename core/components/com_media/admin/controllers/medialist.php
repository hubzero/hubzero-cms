<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Media\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Media\Models\Files;
use Components\Media\Admin\Helpers\MediaHelper;
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Request;
use Hubzero\Facades\User;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;

/**
 * Media list controller
 */
class Medialist extends AdminController
{
    /**
     * Display a list of files
     *
     * @return  void
     */
    public function displayTask()
    {
        $folder = Request::getString('folder', '');
        /*$tmpl   = Request::getCmd('tmpl');

        $filters = array();

        $redirect = 'index.php?option=com_media&folder=' . $folder;
        if ($tmpl == 'component')
        {
            $redirect .= '&view=medialist&tmpl=component';
        }
        $this->setRedirect($redirect);*/

        //$session = App::get('session');
        $state = User::getState('folder');
        $folders = Filesystem::directoryTree(MediaHelper::base());
        $folderTree = MediaHelper::buildFolderTree($folders);

        $children = MediaHelper::getChildren(MediaHelper::base(), $folder);
        $parent = MediaHelper::getParent($folder);

        $style = Request::getState(
            'media.list.layout',
            'layout',
            'thumbs',
            'word',
        );

        $this->view
            ->set('folderTree', $folderTree)
            ->set('folders', $folders)
            ->set('folder', $folder)
            ->set('children', $children)
            ->set('parent', $parent)
            ->set('layout', $style)
            ->setLayout('default')
            ->display();
    }

    /**
     * Display information about a file
     *
     * @return  void
     */
    public function infoTask()
    {
        Request::checkToken(['get', 'post']);

        // Get some data from the request
        $tmpl = Request::getCmd('tmpl');

        $file = urldecode(Request::getString('file', ''));
        $folder = urldecode(Request::getString('folder', ''));

        if ($file) {
            $file = \Hubzero\Filesystem\Util::checkPath(MediaHelper::base() . $file);
            $path = $file;

            if (!is_file($file)) {
                App::abort(404, Lang::txt('Specified file "%s" does not exist', $file));
            }
        } elseif ($folder) {
            $folder = \Hubzero\Filesystem\Util::checkPath(MediaHelper::base() . $folder);
            $path = $folder;

            if (!is_dir($folder)) {
                App::abort(404, Lang::txt('Specified folder "%s" does not exist', $folder));
            }
        } else {
            App::abort(404, Lang::txt('No file or folder specified'));
        }

        // Compile info
        $data = [
            'type'          => ($file ? 'file' : 'folder'),
            'path'          => substr($path, strlen(MediaHelper::base())),
            'absolute_path' => $path,
            'full_path'     => substr($path, strlen(PATH_ROOT)),
            'name'          => basename($path),
            'modified'      => filemtime($path),
            'size'          => 0,
            'width'         => 0,
            'height'        => 0,
        ];

        if ($data['type'] == 'file') {
            $data['size'] = filesize($file);
        }

        if (preg_match("/\.(bmp|gif|jpg|jpe|jpeg|png)$/i", $data['name'])) {
            $data['type'] = 'img';
            try {
                $dimensions = getimagesize($data['absolute_path']);

                $data['width'] = $dimensions[0];
                $data['height'] = $dimensions[1];
            } catch (\Exception $e) {
                $this->setError(Lang::txt('There was a problem reading the image dimensions.'));
            }
        }

        $this->view
            ->set('data', $data)
            ->setErrors($this->getErrors())
            ->display();
    }

    /**
     * Display a link to download a file
     *
     * @return  void
     */
    public function pathTask()
    {
        Request::checkToken(['get', 'post']);

        // Get some data from the request
        $tmpl = Request::getCmd('tmpl');
        $file = urldecode(Request::getString('file', ''));
        $file = \Hubzero\Filesystem\Util::checkPath(MediaHelper::base() . $file);

        if (!is_file($file)) {
            App::abort(404);
        }

        $file = MediaHelper::baseUrl() . substr($file, strlen(MediaHelper::base()));

        $this->view
            ->set('file', $file)
            ->display();
    }
}
