<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$database = App::get('db');
$html = '';

// Get parameters
$rparams = new \Hubzero\Config\Registry($this->resource->params);
$params = $this->config;
$params->merge($rparams);

// Get attributes
$attribs = new \Hubzero\Config\Registry($this->resource->attribs);

// Get the resource's children
$helper = new \Components\Resources\Helpers\Helper($this->resource->id, $database);

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
	<form action="index.php" method="post" id="hubForm">
		<input type="hidden" name="app" value="<?php echo $this->resource->alias; ?>" />
		<input type="hidden" name="rid" value="<?php echo $this->resource->id; ?>" />

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="pipeline" />
		<input type="hidden" name="task" value="status" />

		<input type="hidden" name="msg" value="<?php echo Lang::txt('COM_TOOLS_NOTICE_RES_UPDATED'); ?>" />
		<input type="hidden" name="step" value="6" />
		<input type="hidden" name="editversion" value="<?php echo $this->version; ?>" />
		<input type="hidden" name="toolname" value="<?php echo $this->resource->alias; ?>" />

		<div style="float:left; width:70%;padding:1em 0 1em 0;">
			<span style="float:left;width:100px;"><input type="button" value="&lt; <?php echo ucfirst(Lang::txt('COM_TOOLS_PREVIOUS')); ?>" class="returntoedit" /></span>
			<span style="float:right;width:100px;"><input type="submit" value="<?php echo ucfirst(Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_FINALIZE')); ?> &gt;" /></span>
		</div>
		<div class="clear"></div>
	</form>

	<h1 id="preview-header"><?php echo Lang::txt('COM_TOOLS_Preview'); ?></h1>
	<div id="preview-pane">
		<iframe id="preview-frame" name="preview-frame" width="100%" frameborder="0" src="<?php echo Route::url('index.php?option=com_resources&id=' . $this->resource->id . '&tmpl=component&mode=preview&rev=' . $this->version); ?>"></iframe>
	</div>
</section>