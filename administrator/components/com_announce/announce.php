<?php

#set_include_path(get_include_path() . PATH_SEPARATOR . '.');

jimport('joomla.application.component.view');
jimport('joomla.application.component.controller');

require_once JPATH_BASE.DS.'..'.DS.'modules'.DS.'mod_announce'.DS.'modelannounce.php';

class AnnouncementsViewAdmin extends JView
{
	protected $show_admin_link = false;

	public function __construct()
	{
		$this->addTemplatePath('../administrator/components/com_announce/tmpl');
		JToolBarHelper::title('Announcements', 'user.png' );
	}
}


class AnnouncementControllerAdmin extends JController
{
	private $mdl;

	public function __construct()
	{
		$this->mdl = new ModelAnnouncements();
	}

	public function execute()
	{
		$view = new AnnouncementsViewAdmin();
   		$view->setModel($this->mdl, 'announcements');

		switch (($task = JRequest::getString('task', 'list')))
		{
			case 'edit':
			case 'add':
				JToolBarHelper::save();
				JToolBarHelper::back();
				$view->setLayout('edit');

				$this->mdl->set_current(JRequest::getInt('id', 0));
			break;
			default:
				if ($task == 'save')
					$this->save();
				if ($task == 'remove')
					$this->remove();
				if ($task == 'archive')
					$this->archive();
				if ($task == 'unarchive')
					$this->unarchive();

				JToolBarHelper::addNew();
				JToolBarHelper::publishList('unarchive', 'Publish');
				JToolBarHelper::unpublishList('archive', 'Archive');
				JToolBarHelper::deleteList();
				
				if (($terms = JRequest::getString('terms', '')) != '')
					$this->mdl->set_search_terms($terms);
		}
		$view->display();
	}

	public function save()
	{
		$tbl = new TableAnnouncements();
        if (!$tbl->bind($_POST) || !$tbl->check() || !$tbl->store()) 
		{
        	echo $tbl->getError();
        	exit();
        }
	}

	public function remove()
	{
		$this->mdl->remove_in(JRequest::getVar('id', array()));
	}
	
	public function unarchive()
	{
		$this->mdl->unarchive_in(JRequest::getVar('id', array()));
	}

	public function archive()
	{
		$this->mdl->archive_in(JRequest::getVar('id', array()));
	}

}

$cont = new AnnouncementControllerAdmin();
$cont->execute();

