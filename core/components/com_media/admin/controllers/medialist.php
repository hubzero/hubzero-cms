<?php

namespace Components\Media\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Media\Models\Files;
use User;
use Html;

class Medialist extends AdminController
{
	public function execute()
	{
		parent::execute();
	}

	public function displayTask()
	{
		$filters = array();

		$tmpl = Request::getCmd('tmpl');
		$folder = Request::getVar('folder', '', '', 'path');

		$redirect = 'index.php?option=com_media&folder=' . $folder;
		if ($tmpl == 'component')
		{
			$redirect .= '&view=medialist&tmpl=component';
		}
		$this->setRedirect($redirect);

                $session = \App::get('session');
                $state = \User::getState('folder');
                $directory = '/var/www/hub/app/site/media/';
                $folders = Filesystem::directoryTree($directory);
		$folderTree = $this->_buildFolderTree($folders);

		$children = $this->getChildren($directory, $folder);
		$parent = $this->getParent($folder);

		$style = Request::getState('media.list.layout', 'layout', 'thumbs', 'word');
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

	public function deleteTask()
	{
		ddie("Deleting?");
	}

	private function getParent($folder)
	{
		$parent = substr($folder, 0, strrpos($folder, '/'));
		return $parent;
	}

	private function getChildren($directory, $folder)
	{
		$children = \Filesystem::listContents($directory . $folder);
		foreach ($children as &$child)
		{
			$child['name'] = str_replace('/', '', substr($child['path'], 0, strlen($child['path'])));
			$child['path'] = $folder . $child['path'];
			if (preg_match("/\.(bmp|gif|jpg|jpe|jpeg|png)$/i", $child['name']))
			{
				$child['type'] = 'img';
			}
		}
		return $children;;	
	}

	private function _buildFolderTree($folders, $parent_id = 0)
	{
		$branch = array();
		foreach ($folders as $folder)
		{
			if ($folder['parent'] == $parent_id)
			{
				$children = $this->_buildFolderTree($folders, $folder['id']);
				if ($children)
				{
					$folder['children'] = $children;
				}
				$branch[] = $folder;
			}
		}
		return $branch;
	}
}
