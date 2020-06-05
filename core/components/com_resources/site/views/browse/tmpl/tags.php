<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Import share CSS to style share features on right side of trifold
\Hubzero\Document\Assets::addPluginStylesheet('resources', 'share');

$this->css()
     ->js()
     ->js('tagbrowser');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<?php foreach ($this->types as $type) { ?>
		<?php if ($type->id == $this->filters['type'] && $type->contributable) { ?>
		<div id="content-header-extra">
			<p>
				<?php if ($type->id == 7) { ?>
					<a class="icon-add btn" href="<?php echo Route::url('index.php?option=com_tools&task=create'); ?>">
						<?php
						$name = $type->type;
						if (substr($type->type, -1) == 's')
						{
							$name = substr($type->type, 0, -1);
						}
						echo Lang::txt('COM_RESOURCES_START_NEW_TYPE', $this->escape(stripslashes($name))); ?>
					</a>
				<?php } else { ?>
					<a class="icon-add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft&step=1&type=' . $type->id); ?>">
						<?php
						$name = $type->type;
						if (substr($type->type, -1) == 's')
						{
							$name = substr($type->type, 0, -1);
						}
						echo Lang::txt('COM_RESOURCES_START_NEW_TYPE', $this->escape(stripslashes($name))); ?>
					</a>
				<?php } ?>
			</p>
		</div>
		<?php } ?>
	<?php } ?>
</header><!-- / #content-header -->

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="get" id="tagBrowserForm">
	<section class="main section" id="browse-resources">
		<fieldset>
			<label for="browse-type">
				<span><?php echo Lang::txt('COM_RESOURCES_TYPE'); ?>:</span>
				<select name="type" id="browse-type">
				<?php foreach ($this->types as $type) {
					if (!$type->state)
					{
						continue;
					}
					?>
					<option value="<?php echo $this->escape($type->alias); ?>"<?php if ($type->id == $this->filters['type']) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($type->type)); ?></option>
				<?php } ?>
				</select>
			</label>
			<input type="submit" value="<?php echo Lang::txt('COM_RESOURCES_GO'); ?>"/>
			<input type="hidden" name="task" value="browsetags" />
		</fieldset>

		<div id="tagbrowser" data-loader="<?php echo Request::base(true); ?>/core/components/com_resources/site/assets/img/loading.gif">
			<p class="info"><?php echo Lang::txt('COM_RESOURCES_TAGBROWSER_EXPLANATION'); ?></p>

			<div id="level-1">
				<h3><?php echo Lang::txt('COM_RESOURCES_TAG'); ?></h3>
				<ul>
					<li id="level-1-loading"></li>
				</ul>
			</div><!-- / #level-1 -->
			<div id="level-2">
				<h3><?php echo Lang::txt('COM_RESOURCES'); ?></h3>
				<ul>
					<li id="level-2-loading"></li>
				</ul>
			</div><!-- / #level-2 -->
			<div id="level-3">
				<h3><?php echo Lang::txt('COM_RESOURCES_INFO'); ?></h3>
				<ul>
					<li><?php echo Lang::txt('COM_RESOURCES_TAGBROWSER_COL_EXPLANATION'); ?></li>
				</ul>
			</div><!-- / #level-3 -->

			<input type="hidden" name="id" id="id" value="" />
			<input type="hidden" name="pretype" id="pretype" value="<?php echo $this->escape($this->filters['type']); ?>" />
			<input type="hidden" name="preinput" id="preinput" value="<?php echo $this->escape($this->tag); ?>" />
			<input type="hidden" name="preinput2" id="preinput2" value="<?php echo $this->escape($this->tag2); ?>" />
			<div class="clear"></div>
		</div><!-- / #tagbrowser -->

		<p id="viewalltools"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&type=' . $this->filters['type']); ?>"><?php echo Lang::txt('COM_RESOURCES_VIEW_MORE'); ?></a></p>
		<div class="clear"></div>

		<?php
		if ($this->supportedtag)
		{
			include_once Component::path('com_tags') . DS . 'models' . DS . 'cloud.php';

			$tag = \Components\Tags\Models\Tag::oneByTag($this->supportedtag);

			if ($sl = $this->config->get('supportedlink'))
			{
				$link = $sl;
			}
			else
			{
				$link = Route::url('index.php?option=com_tags&tag=' . $tag->get('tag'));
			}
			?>
			<p class="supported">
				<?php echo Lang::txt('COM_RESOURCES_WHATS_THIS'); ?> <a href="<?php echo $link; ?>"><?php echo Lang::txt('COM_RESOURCES_ABOUT_TAG', $tag->get('raw_tag')); ?></a>
			</p>
			<?php
		}
		?>
	</section>
	<section class="below section">
		<div class="section-inner hz-layout-with-aside">
		<?php if ($this->results) { ?>
			<div class="subject">
				<h3><?php echo Lang::txt('COM_RESOURCES_TOP_RATED'); ?></h3>
				<?php
				$supported = array();

				if ($this->supportedtag)
				{
					include_once Component::path('com_resources') . DS . 'helpers' . DS . 'tags.php';

					$rt = new \Components\Resources\Helpers\Tags(0);
					$supported = $rt->getTagUsage($this->supportedtag, 'id');
				}

				$this->view('_list', 'browse')
					 ->set('lines', $this->results)
					 ->set('show_edit', $this->authorized)
					 ->set('supported', $supported)
					 ->display();
				?>
			</div><!-- / .subject -->
		<?php } ?>
			<aside class="aside">
				<p><?php echo Lang::txt('COM_RESOURCES_TOP_RATED_EXPLANATION'); ?></p>
			</aside><!-- / .aside -->
		</div>
	</section><!-- / .main section -->
</form>
