<?php 
defined('_JEXEC') or die( 'Restricted access' );

$dateFormat = '%d %b, %Y';
$timeFormat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
        $dateFormat = 'd M, Y';
        $timeFormat = 'h:i a';
        $tz = true;
}

$juser = JFactory::getUser();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum';
?>
<div class="main section">
<?php foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
<?php } ?>
	<div class="aside">
		<div class="container">
			<h3><?php echo JText::_('Statistics'); ?><span class="starter-point"></span></h3>
			<table summary="<?php echo JText::_('Statistics'); ?>">
				<tbody>
					<tr>
						<th><?php echo JText::_('Categories'); ?></th>
						<td><span class="item-count"><?php echo $this->model->count('categories'); ?></span></td>
					</tr>
					<tr>
						<th><?php echo JText::_('Discussions'); ?></th>
						<td><span class="item-count"><?php echo $this->model->count('threads'); ?></span></td>
					</tr>
					<tr>
						<th><?php echo JText::_('Posts'); ?></th>
						<td><span class="item-count"><?php echo $this->model->count('posts'); ?></span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="container">
			<h3><?php echo JText::_('Last Post'); ?><span class="starter-point"></span></h3>
			<p>
			<?php
			if ($this->model->lastActivity()->exists()) 
			{
				$post = $this->model->lastActivity();

				$lname = JText::_('Anonymous');
				if (!$post->get('anonymous')) 
				{
					$lname = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $post->creator('id')) . '">' . $this->escape(stripslashes($post->creator('name'))) . '</a>';
				}
				foreach ($this->sections as $section)
				{
					if ($section->categories()->total() > 0) 
					{
						foreach ($section->categories() as $row) 
						{
							if ($row->get('id') == $post->get('category_id'))
							{
								$cat = $row->get('alias');
								$sec = $section->get('alias');
								break;
							}
						}
					}
				}
				?>
				<a class="entry-date" href="<?php echo JRoute::_($base . '&scope=' . $sec . '/' . $cat . '/' . $post->get('thread')); ?>">
					<span class="entry-date-at">@</span>
					<span class="time"><time datetime="<?php echo $post->get('created'); ?>"><?php echo JHTML::_('date', $post->get('created'), $timeFormat, $tz); ?></time></span> <span class="entry-date-on"><?php echo JText::_('on'); ?></span> 
					<span class="date"><time datetime="<?php echo $post->get('created'); ?>"><?php echo JHTML::_('date', $post->get('created'), $dateFormat, $tz); ?></time></span>
				</a>
				<span class="entry-author">
					<?php echo JText::_('by'); ?>
					<?php echo $lname; ?>
				</span>
			<?php } else { ?>
				<?php echo JText::_('none'); ?>
			<?php } ?>
			</p>
		</div>
	</div><!-- / .aside -->

	<div class="subject">
		<form action="<?php echo JRoute::_($base); ?>" method="post">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('Search'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo JText::_('Search categories'); ?></legend>
					<label for="entry-search-field"><?php echo JText::_('Enter keyword or phrase'); ?></label>
					<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Enter keyword or phrase'); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
					<input type="hidden" name="active" value="forum" />
					<input type="hidden" name="action" value="search" />
				</fieldset>
			</div><!-- / .container -->
		</form>
<?php
	foreach ($this->sections as $section)
	{
		if (!$section->exists()) 
		{
			continue;
		}
?>
		<div class="container">
			<table class="entries categories">
				<caption>
				<?php if ($this->config->get('access-edit-section') && $this->edit == $section->get('alias')) { ?>
					<a name="s<?php echo $section->get('id'); ?>"></a>
					<form action="<?php echo JRoute::_($base); ?>" method="post">
						<input type="text" name="fields[title]" value="<?php echo $this->escape(stripslashes($section->get('title'))); ?>" />
						<input type="submit" value="<?php echo JText::_('Save'); ?>" />
						<input type="hidden" name="fields[id]" value="<?php echo $section->get('id'); ?>" />
						<input type="hidden" name="fields[scope]" value="<?php echo $this->model->get('scope'); ?>" />
						<input type="hidden" name="fields[scope_id]" value="<?php echo $this->model->get('scope_id'); ?>" />
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
						<input type="hidden" name="action" value="savesection" />
						<input type="hidden" name="active" value="forum" />
						<?php echo JHTML::_('form.token'); ?>
					</form>
				<?php } else { ?>
					<?php echo $this->escape(stripslashes($section->get('title'))); ?>
				<?php } ?>
			<?php if ($this->config->get('access-edit-section') || $this->config->get('access-delete-section')) { ?>
				<?php if ($this->config->get('access-delete-section')) { ?>
					<a class="icon-delete delete" href="<?php echo JRoute::_($base . '&scope=' . $section->get('alias') . '/delete'); ?>" title="<?php echo JText::_('Delete'); ?>">
						<span><?php echo JText::_('Delete'); ?></span>
					</a>
				<?php } ?>
				<?php if ($this->config->get('access-edit-section') && $this->edit != $section->get('alias')) { ?>
					<a class="icon-edit edit" href="<?php echo JRoute::_($base . '&scope=' . $section->get('alias') . '/edit#s' . $section->get('id')); ?>" title="<?php echo JText::_('Edit'); ?>">
						<span><?php echo JText::_('Edit'); ?></span>
					</a>
				<?php } ?>
			<?php } ?>
				</caption>
			<?php if ($this->config->get('access-create-category')) { ?>
				<tfoot>
					<tr>
						<td<?php if ($section->categories()->total() > 0) { echo ' colspan="5"'; } ?>>
							<a class="icon-add add btn" href="<?php echo JRoute::_($base . '&scope=' . $section->get('alias') . '/new'); ?>">
								<span><?php echo JText::_('Add Category'); ?></span>
							</a>
						</td>
					</tr>
				</tfoot>
			<?php } ?>
				<tbody>
			<?php if ($section->categories()->total() > 0) { ?>
				<?php foreach ($section->categories() as $row) { ?>
					<tr<?php if ($row->get('closed')) { echo ' class="closed"'; } ?>>
						<th scope="row">
							<span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
						</th>
						<td>
							<a class="entry-title" href="<?php echo JRoute::_($base . '&scope=' . $section->get('alias') . '/' . $row->get('alias')); ?>">
								<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
							</a>
							<span class="entry-details">
								<span class="entry-description">
									<?php echo $this->escape(stripslashes($row->get('description'))); ?>
								</span>
							</span>
						</td>
						<td>
							<span><?php echo $row->count('threads'); ?></span>
							<span class="entry-details">
								<?php echo JText::_('Discussions'); ?>
							</span>
						</td>
						<td>
							<span><?php echo $row->count('posts'); ?></span>
							<span class="entry-details">
								<?php echo JText::_('Posts'); ?>
							</span>
						</td>
					<?php if ($this->config->get('access-edit-category') || $this->config->get('access-delete-category')) { ?>
						<td class="entry-options">
							<?php if ($row->get('created_by') == $juser->get('id') || $this->config->get('access-edit-category')) { ?>
								<a class="icon-edit edit" href="<?php echo JRoute::_($base . '&scope=' . $section->get('alias') . '/' . $row->get('alias') . '/edit'); ?>" title="<?php echo JText::_('Edit'); ?>">
									<span><?php echo JText::_('Edit'); ?></span>
								</a>
							<?php } ?>
							<?php if ($this->config->get('access-delete-category')) { ?>
								<a class="icon-delete delete tooltips" title="<?php echo JText::_('PLG_GROUPS_FORUM_DELETE_CATEGORY'); ?>" href="<?php echo JRoute::_($base . '&scope=' . $section->get('alias') . '/' . $row->get('alias') . '/delete'); ?>" title="<?php echo JText::_('Delete'); ?>">
									<span><?php echo JText::_('Delete'); ?></span>
								</a>
							<?php } ?>
						</td>
					<?php } ?>
					</tr>
				<?php } ?>
			<?php } else { ?>
					<tr>
						<td><?php echo JText::_('There are no categories.'); ?></td>
					</tr>
			<?php } ?>
				</tbody>
			</table>
		</div>
<?php
	}
/*} else {
?>
	<p><?php echo JText::_('This forum is currently empty.'); ?></p>
<?php
}*/
?>

	<?php if ($this->config->get('access-create-section')) { ?>
		<div class="container">
			<form method="post" action="<?php echo JRoute::_($base); ?>">
					<table class="entries categories">
						<caption>
							<label for="field-title">
								<?php echo JText::_('New Section'); ?>
								<input type="text" name="fields[title]" id="field-title" value="" />
							</label>
							<input type="submit" value="<?php echo JText::_('Create'); ?>" />
						</caption>
						<tbody>
							<tr>
								<td><?php echo JText::_('Use sections to group related categories.'); ?></td>
							</tr>
						</tbody>
					</table>

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
					<input type="hidden" name="fields[scope]" value="<?php echo $this->model->get('scope'); ?>" />
					<input type="hidden" name="fields[scope_id]" value="<?php echo $this->model->get('scope_id'); ?>" />
					<input type="hidden" name="active" value="forum" />
					<input type="hidden" name="task" value="savesection" />

					<?php echo JHTML::_('form.token'); ?>
				</fieldset>
			</form>
		</div><!-- /.container -->
	<?php } ?>

	<?php if (JComponentHelper::getParams('com_groups')->get('email_comment_processing')) { ?>
		<form method="post" action="<?php echo JRoute::_($base); ?>" id="forum-options">
			<fieldset>
				<legend><?php echo JText::_('Email Settings'); ?></legend>

				<input type="hidden" name="action" value="savememberoptions" />
				<input type="hidden" name="memberoptionid" value="<?php echo $this->recvEmailOptionID; ?>" />
				<input type="hidden" name="postsaveredirect" value="<?php echo JRoute::_($base); ?>" />

				<label class="option" for="recvpostemail">
					<input type="checkbox" class="option" id="recvpostemail" value="1" name="recvpostemail"<?php if ($this->recvEmailOptionValue == 1) { echo ' checked="checked"'; } ?> />
					<?php echo JText::_('Email forum posts'); ?>
				</label>
				<input class="option" type="submit" value="<?php echo JText::_('Save'); ?>" />
			</fieldset>
		</form>
	<?php } ?>

	</div><!-- /.subject -->
</div><!-- /.main -->
