<?php
defined('_JEXEC') or die('Restricted access');

$juser = JFactory::getUser();

$base = $this->offering->alias() . '&active=forum';
?>
<section class="main section">
	<!-- <div class="subject"> -->
		<?php foreach ($this->notifications as $notification) { ?>
			<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } ?>

		<form action="<?php echo JRoute::_($base); ?>" method="post">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('Search'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo JText::_('Search for posts'); ?></legend>

					<label for="entry-search-field"><?php echo JText::_('Enter keyword or phrase'); ?></label>
					<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" />

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
					<input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
					<input type="hidden" name="active" value="forum" />
					<input type="hidden" name="action" value="search" />
				</fieldset>
			</div><!-- / .container -->

			<div class="container">
				<table class="entries">
					<caption>
						<?php echo JText::sprintf('Search for "%s"', $this->escape($this->filters['search'])); ?>
					</caption>
					<tbody>
				<?php
				if ($this->rows)
				{
					foreach ($this->rows as $row)
					{
						$name = JText::_('Anonymous');
						if (!$row->anonymous)
						{
							$creator = JUser::getInstance($row->created_by);
							if (is_object($creator))
							{
								$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $creator->get('id')) . '">' . $this->escape(stripslashes($creator->get('name'))) . '</a>';
							}
						}

						if ($row->parent)
						{
							$p = new ForumTablePost(JFactory::getDBO());
							$thread = $p->getThread($row->parent);
						}
						else
						{
							$thread = $row;
						}
					?>
						<tr<?php if ($row->sticky) { echo ' class="sticky"'; } ?>>
							<th>
								<span class="entry-id"><?php echo $this->escape($row->id); ?></span>
							</th>
							<td>
								<a class="entry-title" href="<?php echo JRoute::_($base . '&unit=' . $this->categories[$row->category_id]->alias . '&b=' . $thread->id . '#c' . $row->id); ?>">
									<span><?php echo $this->escape(stripslashes($row->title)); ?> ...</span>
								</a>
								<span class="entry-details">
									<span class="entry-date">
										<time datetime="<?php echo $row->created; ?>"><?php echo JHTML::_('date', $row->created, JText::_('DATE_FORMAt_HZ1')); ?></time>
									</span>
									<?php echo JText::_('by'); ?>
									<span class="entry-author">
										<?php echo $name; ?>
									</span>
								</span>
							</td>
							<!-- <td>
								<span><?php echo JText::_('Section'); ?></span>
								<span class="entry-details">
									<?php echo $this->escape($this->sections[$this->categories[$row->category_id]->section_id]->title); ?>
								</span>
							</td> -->
							<td>
								<span><?php echo JText::_('Category'); ?></span>
								<span class="entry-details">
									<?php echo $this->escape($this->categories[$row->category_id]->title); ?>
								</span>
							</td>
							<td>
								<span><?php echo JText::_('Thread'); ?></span>
								<span class="entry-details">
									<?php echo $this->escape(stripslashes($thread->title)); ?>
								</span>
							</td>
						</tr>
				<?php
					}
				} else { ?>
						<tr>
							<td><?php echo JText::_('No discussions found.'); ?></td>
						</tr>
				<?php } ?>
					</tbody>
				</table>
				<?php
				if ($this->pageNav)
				{
					$this->pageNav->setAdditionalUrlParam('gid', $this->course->get('alias'));
					$this->pageNav->setAdditionalUrlParam('offering', $this->offering->get('alias'));
					$this->pageNav->setAdditionalUrlParam('active', 'forum');
					$this->pageNav->setAdditionalUrlParam('action', 'search');
					$this->pageNav->setAdditionalUrlParam('q', $this->filters['search']);
					echo $this->pageNav->getListFooter();
				}
				?>
			</div><!-- / .container -->
		</form>
	<?php /*</div><!-- /.subject -->
	<aside class="aside">
	<?php if ($this->config->get('access-create-thread')) { ?>
		<div class="container">
			<h3><?php echo JText::_('Start Your Own'); ?><span class="starter-point"></span></h3>
		<?php if (!$this->category->closed) { ?>
			<p>
				<?php echo JText::_('Create your own discussion where you and other users can discuss related topics.'); ?>
			</p>
			<p class="add">
				<a href="<?php echo JRoute::_($base . '&action=add'); ?>"><?php echo JText::_('Add Discussion'); ?></a>
			</p>
		<?php } else { ?>
			<p class="warning">
				<?php echo JText::_('This category is closed and no new discussions may be created.'); ?>
			</p>
		<?php } ?>
		</div>
	<?php } ?>
	</aside><!-- / .aside -->*/ ?>
</section><!-- /.main -->