<?php
jimport('joomla.application.component.view');
jimport('joomla.application.component.controller');

require_once 'modules/mod_announce/modelannounce.php';

class AnnouncementsViewArchive extends JView
{
	protected $show_admin_link = false;

	public function __construct($params)
	{
		$this->params = $params;
		$this->addTemplatePath('components/com_announce/tmpl');
	}
}

class AnnouncementControllerArchive extends JController
{
	public function execute($params)
	{
		$doc =& JFactory::getDocument();
		$doc->addStyleSheet('/modules/mod_announce/announce.css');
		$doc->addScript('/modules/mod_announce/announce.js');

		$view = new AnnouncementsViewArchive($params);
		$mdl = new ModelAnnouncements();
   		$view->setModel($mdl, 'announcements');
		if (($terms = JRequest::getString('terms', '')) != '' && JRequest::getString('task', '') == 'search')
			$mdl->set_search_terms($terms);
		$view->display();
	}
}

$cont = new AnnouncementControllerArchive();
$cont->execute($params);
