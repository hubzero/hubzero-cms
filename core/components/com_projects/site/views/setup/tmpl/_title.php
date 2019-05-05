<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$title = $this->model->get('title') ? Lang::txt('COM_PROJECTS_NEW_PROJECT') . ': ' . $this->model->get('title') : $this->title;

?>
<header id="content-header">
	<h2><?php echo $title; ?> <?php if ($this->model->groupOwner() && $cn = $this->model->groupOwner('cn')) { ?> <?php echo Lang::txt('COM_PROJECTS_FOR').' '.ucfirst(Lang::txt('COM_PROJECTS_GROUP')); ?> <a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $cn); ?>"><?php echo \Hubzero\Utility\Str::truncate($this->model->groupOwner('description'), 50); ?></a><?php } ?></h2>
</header><!-- / #content-header -->
