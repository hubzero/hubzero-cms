<?php

if (!function_exists('stem'))
{
	function stem($str) { return $str; }
}

ini_set('display_errors', 1);

require 'include.php';

jimport('joomla.application.component.controller');

class YSearchController extends JController
{
	public function display()
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		$pathway->addItem('Search', '/search');

		$terms = new YSearchModelTerms(JRequest::getString('terms'));
		JFactory::getDocument()->setTitle($terms->is_set() ? 'Search results for \''.htmlspecialchars($terms->get_raw(), ENT_NOQUOTES).'\'' : 'Search');
		
		$app =& JFactory::getApplication();
		$results = new YSearchModelResultSet($terms);
		$results->set_limit($app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int'));
		$results->set_offset(JRequest::getInt('limitstart', 0));
		$results->collect(JRequest::getBool('force-generic'));

		$view =& $this->getView('', JRequest::getCmd('format', 'html'), '');
		$view->set_application($app);
		$view->set_terms($terms);
		$view->set_results($results);
		$view->display();
	}
}

$controller = new YSearchController();
$controller->execute(JRequest::getCmd('task'));
