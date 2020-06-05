<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$asset = new \Components\Courses\Models\Asset($this->asset->id);

$config = array(
	'option'   => 'com_courses',
	'scope'    => $this->course->get('alias') . DS . $this->course->offering()->alias() . DS . 'asset',
	'pagename' => $this->asset->id,
	'pageid'   => '',
	'filepath' => $asset->path($this->course->get('id')),
	'domain'   => $this->course->get('alias')
);

$this->model->set('content', stripslashes($this->model->get('content')));

Event::trigger('content.onContentPrepare', array(
	'com_courses.asset.content',
	&$this->model,
	&$config
));
?>

<header id="content-header">
	<h2><?php echo $this->asset->title ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-prev back btn" href="<?php echo Route::url($this->course->offering()->link() . '&active=outline'); ?>">
				<?php echo Lang::txt('Back to course'); ?>
			</a>
		</p>
	</div>
</header>

<div class="wiki-page-body">
	<p>
		<?php echo $this->model->get('content'); ?>
	</p>
</div>
