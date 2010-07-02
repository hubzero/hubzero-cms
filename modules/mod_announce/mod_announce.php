<?php

require 'modelannounce.php';
jimport('joomla.application.component.view');
jimport('joomla.application.component.controller');

class AnnouncementsViewDefault extends JView
{
	protected $show_admin_link = false;

	public function __construct($params)
	{
		$this->params = $params;
		$this->addTemplatePath(dirname(JModuleHelper::getLayoutPath('mod_announce')));
	}

	protected function get_new_indicator($timestamp)
	{
		if (strtotime($timestamp) >= strtotime('now - '.$this->params->get('new_period')))
			return '<span class="new-announcement-indicator">New</span>';
		return '';
	}
}

class AnnouncementControllerModule extends JController
{
	public function execute($params)
	{
		$doc =& JFactory::getDocument();
		$doc->addStyleSheet('/modules/mod_announce/announce.css');

		$view = new AnnouncementsViewDefault($params);
		$mdl = new ModelAnnouncements();
		$mdl->set_minimum($params->get('min_front_page'));
		$mdl->set_maximum($params->get('max_front_page'));
		$view->setModel($mdl, 'announcements');
		$view->display();
	}
}

$cont = new AnnouncementControllerModule();
$cont->execute($params);
