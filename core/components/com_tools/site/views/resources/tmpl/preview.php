<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$html = '';

// Get parameters
$rparams = $this->resource->params;
$params = $this->config;
$params->merge($rparams);

// Get attributes
$attribs = $this->resource->attribs;

$this->css('resource.css')
     ->css('resources', 'com_resources')
     ->js('resource.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="icon-status status btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=pipeline&task=status&app=' . $this->resource->alias); ?>"><?php echo Lang::txt('COM_TOOLS_TOOL_STATUS'); ?></a></li>
			<li class="last"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=pipeline&task=create'); ?>" class="icon-add add btn"><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="section steps-section">
	<?php
	$this->view('stage')
	     ->set('stage', $this->step)
	     ->set('option', $this->option)
	     ->set('controller', $this->controller)
	     ->set('version', $this->version)
	     ->set('row', $this->resource)
	     ->set('status', $this->status)
	     ->set('vnum', 0)
	     ->display();
	?>
</section>

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
		<input type="hidden" name="app" value="<?php echo $this->resource->alias; ?>" />
		<input type="hidden" name="rid" value="<?php echo $this->resource->id; ?>" />

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="pipeline" />
		<input type="hidden" name="task" value="status" />

		<input type="hidden" name="msg" value="<?php echo Lang::txt('COM_TOOLS_NOTICE_RES_UPDATED'); ?>" />
		<input type="hidden" name="step" value="6" />
		<input type="hidden" name="editversion" value="<?php echo $this->version; ?>" />
		<input type="hidden" name="toolname" value="<?php echo $this->resource->alias; ?>" />

		<div class="steps-nav">
			<span class="step-prev"><input type="button" value="&lt; <?php echo ucfirst(Lang::txt('COM_TOOLS_PREVIOUS')); ?>" class="returntoedit" /></span>
			<span class="step-next"><input type="submit" value="<?php echo ucfirst(Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_FINALIZE')); ?> &gt;" /></span>
		</div>
		<div class="clear"></div>
	</form>

	<h1 id="preview-header"><?php echo Lang::txt('COM_TOOLS_Preview'); ?></h1>
	<div id="preview-pane">
		<iframe id="preview-frame" name="preview-frame" width="100%" frameborder="0" src="<?php echo Route::url('index.php?option=com_resources&id=' . $this->resource->id . '&tmpl=component&mode=preview&rev=' . $this->version); ?>"></iframe>
	</div>
</section>