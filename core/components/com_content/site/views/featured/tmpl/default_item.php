<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_HZEXEC_') or die();

// Create a shortcut for params.
$params = &$this->item->params;
$images = json_decode($this->item->images);
$canEdit = $this->item->params->get('access-edit');
?>

<?php if ($this->item->state == 0 || strtotime($this->item->publish_up) > strtotime(Date::of('now'))) : ?>
<div class="system-unpublished">
<?php endif; ?>
<?php if ($params->get('show_title')) : ?>
	<h2>
		<?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
			<a href="<?php echo Route::url(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)); ?>">
			<?php echo $this->escape($this->item->title); ?></a>
		<?php else : ?>
			<?php echo $this->escape($this->item->title); ?>
		<?php endif; ?>
	</h2>
<?php endif; ?>

<?php if ($params->get('show_print_icon') || $params->get('show_email_icon') || $canEdit) : ?>
	<ul class="actions">
		<?php if ($params->get('show_print_icon')) : ?>
		<li class="print-icon">
			<?php echo Html::icon('print_popup', $this->item, $params); ?>
		</li>
		<?php endif; ?>
		<?php if ($params->get('show_email_icon')) : ?>
		<li class="email-icon">
			<?php echo Html::icon('email', $this->item, $params); ?>
		</li>
		<?php endif; ?>

		<?php if ($canEdit) : ?>
		<li class="edit-icon">
			<?php echo Html::icon('edit', $this->item, $params); ?>
		</li>
		<?php endif; ?>
	</ul>
<?php endif; ?>

<?php if (!$params->get('show_intro')) : ?>
	<?php echo $this->item->event->afterDisplayTitle; ?>
<?php endif; ?>

<?php echo $this->item->event->beforeDisplayContent; ?>

<?php // to do not that elegant would be nice to group the params ?>

<?php if (($params->get('show_author')) or ($params->get('show_category')) or ($params->get('show_create_date')) or ($params->get('show_modify_date')) or ($params->get('show_publish_date')) or ($params->get('show_parent_category')) or ($params->get('show_hits'))) : ?>
 <dl class="article-info">
 <dt class="article-info-term"><?php  echo Lang::txt('COM_CONTENT_ARTICLE_INFO'); ?></dt>
<?php endif; ?>
<?php if ($params->get('show_parent_category') && $this->item->parent_id != 1) : ?>
		<dd class="parent-category-name">
			<?php $title = $this->escape($this->item->parent_title);
				$url = '<a href="' . Route::url(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)) . '">' . $title . '</a>'; ?>
			<?php if ($params->get('link_parent_category') and $this->item->parent_slug) : ?>
				<?php echo Lang::txt('COM_CONTENT_PARENT', $url); ?>
				<?php else : ?>
				<?php echo Lang::txt('COM_CONTENT_PARENT', $title); ?>
			<?php endif; ?>
		</dd>
<?php endif; ?>
<?php if ($params->get('show_category')) : ?>
		<dd class="category-name">
			<?php $title = $this->escape($this->item->category_title);
				$url = '<a href="'.Route::url(ContentHelperRoute::getCategoryRoute($this->item->catslug)).'">'.$title.'</a>';?>
			<?php if ($params->get('link_category') and $this->item->catslug) : ?>
				<?php echo Lang::txt('COM_CONTENT_CATEGORY', $url); ?>
				<?php else : ?>
				<?php echo Lang::txt('COM_CONTENT_CATEGORY', $title); ?>
			<?php endif; ?>
		</dd>
<?php endif; ?>
<?php if ($params->get('show_create_date')) : ?>
		<dd class="create">
		<?php echo Lang::txt('COM_CONTENT_CREATED_DATE_ON', Date::of($this->item->created)->toLocal(Lang::txt('DATE_FORMAT_LC2'))); ?>
		</dd>
<?php endif; ?>
<?php if ($params->get('show_modify_date')) : ?>
		<dd class="modified">
		<?php echo Lang::txt('COM_CONTENT_LAST_UPDATED', Date::of($this->item->modified)->toLocal(Lang::txt('DATE_FORMAT_LC2'))); ?>
		</dd>
<?php endif; ?>
<?php if ($params->get('show_publish_date')) : ?>
		<dd class="published">
			<?php echo Lang::txt('COM_CONTENT_PUBLISHED_DATE_ON', Date::of($this->item->publish_up)->toLocal(Lang::txt('DATE_FORMAT_LC2'))); ?>
		</dd>
<?php endif; ?>
<?php if ($params->get('show_author') && !empty($this->item->author )) : ?>
	<dd class="createdby">
		<?php $author =  $this->item->author; ?>
		<?php $author = ($this->item->created_by_alias ? $this->item->created_by_alias : $author);?>

			<?php if (!empty($this->item->contactid ) &&  $params->get('link_author') == true):?>
				<?php  echo Lang::txt('COM_CONTENT_WRITTEN_BY', '<a href="' . Route::url('index.php?option=com_contact&view=contact&id='.$this->item->contactid) . '">' . $author . '</a>'); ?>
			<?php else :?>
				<?php echo Lang::txt('COM_CONTENT_WRITTEN_BY', $author); ?>
			<?php endif; ?>
	</dd>
<?php endif; ?>
<?php if ($params->get('show_hits')) : ?>
		<dd class="hits">
		<?php echo Lang::txt('COM_CONTENT_ARTICLE_HITS', $this->item->hits); ?>
		</dd>
<?php endif; ?>
<?php if (($params->get('show_author')) or ($params->get('show_category')) or ($params->get('show_create_date')) or ($params->get('show_modify_date')) or ($params->get('show_publish_date')) or ($params->get('show_parent_category')) or ($params->get('show_hits'))) : ?>
 </dl>
<?php endif; ?>

<?php  if (isset($images->image_intro) and !empty($images->image_intro)) : ?>
	<?php $imgfloat = (empty($images->float_intro)) ? $params->get('float_intro') : $images->float_intro; ?>

	<div class="img-intro-<?php echo htmlspecialchars($imgfloat); ?>">
	<img
		<?php if ($images->image_intro_caption):
			echo 'class="caption"'.' title="' .htmlspecialchars($images->image_intro_caption) .'"';
		endif; ?>
		src="<?php echo htmlspecialchars($images->image_intro); ?>" alt="<?php echo htmlspecialchars($images->image_intro_alt); ?>"/>
	</div>
<?php endif; ?>

<?php echo $this->item->introtext; ?>

<?php if ($params->get('show_readmore') && $this->item->readmore) :
	if ($params->get('access-view')) :
		$link = Route::url(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
	else :
		$menu = \App::get('menu');
		$active = $menu->getActive();
		$itemId = $active->id;
		$link1 = Route::url('index.php?option=com_users&view=login&Itemid=' . $itemId);
		$returnURL = Route::url(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
		$link = new JURI($link1);
		$link->setVar('return', base64_encode(urlencode($returnURL)));
	endif;
?>
			<p class="readmore">
				<a href="<?php echo $link; ?>">
					<?php if (!$params->get('access-view')) :
						echo Lang::txt('COM_CONTENT_REGISTER_TO_READ_MORE');
					elseif ($readmore = $this->item->alternative_readmore) :
						echo $readmore;
						if ($params->get('show_readmore_title', 0) != 0) :
							echo \Hubzero\Utility\String::truncate($this->item->title, $params->get('readmore_limit'));
						endif;
					elseif ($params->get('show_readmore_title', 0) == 0) :
						echo Lang::txt('COM_CONTENT_READ_MORE_TITLE');
					else :
						echo Lang::txt('COM_CONTENT_READ_MORE');
						echo \Hubzero\Utility\String::truncate($this->item->title, $params->get('readmore_limit'));
					endif; ?>
				</a>
			</p>
<?php endif; ?>

<?php if ($this->item->state == 0 || strtotime($this->item->publish_up) > strtotime(Date::of('now'))) : ?>
</div>
<?php endif; ?>

<div class="item-separator"></div>
<?php echo $this->item->event->afterDisplayContent; ?>
