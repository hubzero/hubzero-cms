<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$componentPath = Component::path('com_forms');

use Components\Forms\Helpers\FormsRouter as RoutesHelper;

$pages = $this->pages;
$routes = new RoutesHelper();
?>

<ul class="page-list">
	<?php
		foreach ($pages as $page)
		{
			$this->view('_page_item')
				->set('editUrl', $routes->pagesEditUrl($page->get('id')))
				->set('page', $page)
				->display();
		}
	?>
</ul>
