<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$componentPath = Component::path('com_forms');

require_once "$componentPath/helpers/formsRouter.php";

use Components\Forms\Helpers\FormsRouter as Routes;

$breadcrumbs = $this->breadcrumbs;
$page = $this->page;

$finalBreadcrumbs = [
	'Forms' => '/forms'
];
$routes = new Routes();

foreach ($breadcrumbs as $text => $functionAndArgs)
{
	$function = $functionAndArgs[0];
	$args = isset($functionAndArgs[1]) ? $functionAndArgs[1] : [];

	$finalBreadcrumbs[$text] = $routes->$function(...$args);
}


$this->view('_breadcrumbs', 'shared')
	->set('breadcrumbs', $finalBreadcrumbs)
	->set('page', $page)
	->display();
