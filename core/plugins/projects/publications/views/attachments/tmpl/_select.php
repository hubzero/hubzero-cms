<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$prov = $this->pub->_project->isProvisioned() ? 1 : 0;
$action = 'select';

switch ($this->type)
{
	case 'file':
	default:
		$active = 'files';
		break;
	case 'data':
		$active = 'databases';
		break;
	case 'link':
		$active = 'links';
		break;
	case 'publication':
		$active = 'publications';
		$this->js('jquery.hideseek.min.js')
		     ->js('jQuery(document).on("afterShowinboxLoad", function(e){
		     $("#pub-search").hideseek({
				highlight: true
			});
		});');
		//$action = 'choose';
		break;
}

$route = $this->pub->link('editbase');
$selectUrl = $prov
		? Route::url($route) . '?active=' . $active . '&amp;action=' . $action . '&amp;p=' . $this->props . '&amp;pid=' . $this->pub->id . '&amp;vid=' . $this->pub->version_id
		: Route::url($route . '&active=' . $active . '&action=' . $action) . '/?p=' . $this->props . '&amp;pid=' . $this->pub->id . '&amp;vid=' . $this->pub->version_id;

?>
<div class="item-new">
	<span><a href="<?php echo $selectUrl; ?>" class="item-add showinbox nox"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SELECT_' . strtoupper($this->type)); ?></a></span>
</div>