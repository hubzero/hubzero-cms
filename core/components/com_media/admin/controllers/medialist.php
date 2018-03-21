<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Drew Thoennes <dthoenne@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Media\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Media\Models\Files;
use Components\Media\Admin\Helpers\MediaHelper;
use App;
use Request;
use User;
use Filesystem;

class Medialist extends AdminController
{
	public function execute()
	{
		parent::execute();
	}

	public function displayTask()
	{
		$filters = array();

		$tmpl = \Request::getCmd('tmpl');
		$folder = \Request::getVar('folder', '', '', 'path');

		$redirect = 'index.php?option=com_media&folder=' . $folder;
		if ($tmpl == 'component')
		{
			$redirect .= '&view=medialist&tmpl=component';
		}
		$this->setRedirect($redirect);

                $session = \App::get('session');
                $state = \User::getState('folder');
                $folders = \Filesystem::directoryTree(COM_MEDIA_BASE);
		$folderTree = MediaHelper::_buildFolderTree($folders);

		$children = MediaHelper::getChildren(COM_MEDIA_BASE, $folder);
		$parent = MediaHelper::getParent($folder);

		$style = \Request::getState('media.list.layout', 'layout', 'thumbs', 'word');
		\Hubzero\Document\Assets::addComponentStylesheet('com_media', 'medialist-'.$style.'.css');
\Hubzero\Document\Assets::addComponentStylesheet('com_media', 'mediamanager.css');
		$this->setView('medialist', 'thumbs');
                $this->view
			->set('folderTree', $folderTree)
			->set('folders', $folders)
			->set('folder', $folder)
			->set('children', $children)
			->set('parent', $parent)
                        ->display();
	}
}
