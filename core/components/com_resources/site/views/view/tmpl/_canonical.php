<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

if ($canonical = $this->model->attribs->get('canonical', ''))
{
	$title = $canonical;
	$url   = $canonical;

	if (preg_match('/^(\/?resources\/(.+))/i', $canonical, $matches))
	{
		$model = \Components\Resources\Models\Entry::getInstance($matches[2]);
		$title = $model->title;
		$url   = Route::url($model->link());
	}
	else if (is_numeric($canonical))
	{
		$model = \Components\Resources\Models\Entry::getInstance(intval($canonical));
		$title = $model->title;
		$url   = Route::url($model->link());
	}

	if (!preg_match('/^(https?:|mailto:|ftp:|gopher:|news:|file:|rss:)/i', $url))
	{
		$url = rtrim(Request::base(), DS) . DS . ltrim($url, DS);
	}
	?>
	<div class="new-version grid">
		<div class="col span8">
			&nbsp;
		</div>
		<div class="col span4 omega">
			<p><?php echo Lang::txt('COM_RESOURCES_NEWER_VER_AVAIL'); ?></p>
		</div>
	</div>
	<div class="new-version-message">
		<div class="inner">
			<h3><?php echo Lang::txt('COM_RESOURCES_NEWER_VER_AVAIL'); ?></h3>
			<p><?php echo Lang::txt('COM_RESOURCES_NEWER_VER_AVAIL_EXTENDED'); ?> <a href="<?php echo $url; ?>"><?php echo $title; ?></a></p>
		</div>
	</div>
	<?php
}