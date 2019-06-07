<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//push the stylesheet to the view
\Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications');
\Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications', 'curation.css');

$this->css()
	->js()
	->css('jquery.fancybox.css', 'system')
	->css('curation.css')
	->js('curation.js');

$status = $this->pub->getStatusName();
$class  = $this->pub->getStatusCss();
$typetitle = \Components\Publications\Helpers\Html::writePubCategory($this->pub->category()->alias, $this->pub->category()->name);
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="btn icon-browse" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_LIST'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" id="curation-form" name="curation-form">
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="id" id="pid" value="<?php echo $this->pub->id; ?>" data-route="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&id=' . $this->pub->id . '&task=save'); ?>" />
		<input type="hidden" name="vid" id="vid" value="<?php echo $this->pub->version_id; ?>" />
		<input type="hidden" name="task" id="task" value="save" />

		<div class="curation-wrap">
			<div class="pubtitle">
				<h3>
					<span class="restype indlist"><?php echo $typetitle; ?></span> <?php echo \Hubzero\Utility\Str::truncate($this->pub->title, 65); ?> | <?php echo Lang::txt('COM_PUBLICATIONS_CURATION_VERSION') . ' ' . $this->pub->version_label; ?>
				</h3>
			</div>
			<p class="instruct">
				<span class="pubimage">
					<img src="<?php echo Route::url('index.php?option=com_publications&id=' . $this->pub->id . '&v=' . $this->pub->version_id) . '/Image:thumb'; ?>" alt="" />
				</span>
				<strong class="block">
					<?php
					echo $this->pub->reviewed ? Lang::txt('COM_PUBLICATIONS_CURATION_RESUBMITTED') : Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTED');
					$name = $this->pub->modifier('name');
					$name = $name ?: Lang::txt('JUNKNOWN');
					echo ' ' . Date::of($this->pub->submitted)->toLocal('M d, Y') . ' ' . Lang::txt('COM_PUBLICATIONS_CURATION_BY', $name);
					?>
				</strong>
				<?php if ($this->pub->curator()): ?>
					<span class="block">
						<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGNED_CURATOR') . ' <strong>' . $this->pub->curator('name') . ' (' . $this->pub->curator('username') . ')</strong>';  ?>
					</span>
				<?php endif; ?>
				<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_REVIEW_AND_ACT'); ?>
				<span class="legend">
					<span class="legend-checker-none"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_LEGEND_NONE'); ?></span>
					<span class="legend-checker-pass"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_LEGEND_PASS'); ?></span>
					<span class="legend-checker-fail"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_LEGEND_FAIL'); ?></span>
					<span class="legend-checker-update"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_LEGEND_UPDATE'); ?></span>
				</span>
			</p>
			<div class="clear"></div>
			<div class="submit-curation">
				<p>
					<span class="button-wrapper icon-kickback">
						<input type="submit" value="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_LOOKS_BAD'); ?>" class="btn btn-primary active icon-kickback btn-curate curate-kickback" />
					</span>
					<span class="button-wrapper icon-apply">
						<input type="submit" value="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_LOOKS_GOOD'); ?>" class="btn btn-success active icon-apply btn-curate curate-save" />
					</span>
				</p>
			</div>

			<?php if ($this->history && $this->history->comment): ?>
				<div class="submitter-comment">
					<h5><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTER_COMMENT'); ?></h5>
					<p><?php echo $this->history->comment; ?></p>
				</div>
			<?php endif; ?>

			<div class="curation-blocks">
				<?php
				foreach ($this->pub->curation('blocks') as $blockId => $block):

					// Skip inactive blocks
					if (isset($block->active) && $block->active == 0):
						continue;
					endif;

					$this->pub->_curationModel->setBlock($block->name, $blockId);

					// Get block content
					echo $block->name == 'review' ? '' : $this->pub->_curationModel->parseBlock('curator');
				endforeach;
				?>
			</div>
		</div>
	</form>

	<div class="hidden">
		<div id="addnotice" class="addnotice">
			<form id="notice-form" name="noticeForm" action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post">
				<fieldset>
					<legend><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_NOTICE_TITLE'); ?></legend>

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
					<input type="hidden" name="task" value="save" />
					<input type="hidden" name="id" value="<?php echo $this->pub->get('id'); ?>" />
					<input type="hidden" name="vid" value="<?php echo $this->pub->get('version_id'); ?>" />
					<input type="hidden" name="ajax" value="1" />
					<input type="hidden" name="no_html" value="1" />
					<input type="hidden" name="p" id="props" value="" />
					<input type="hidden" name="pass" value="0" />

					<p class="notice-item" id="notice-item"></p>

					<div class="form-group">
						<label for="notice-review">
							<span class="block"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_NOTICE_LABEL'); ?></span>
							<textarea name="review" id="notice-review" class="form-control" rows="5" cols="10"></textarea>
						</label>
					</div>
				</fieldset>
				<p class="submitarea">
					<input type="submit" id="notice-submit" class="btn" value="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_MARK_AS_FAIL'); ?>" />
				</p>
			</form>
		</div>
	</div>
</section>