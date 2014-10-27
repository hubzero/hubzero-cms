<?php
defined('_JEXEC') or die( 'Restricted access' );

$juser = JFactory::getUser();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum';

$this->css()
     ->js();

if ($this->config->get('access-manage-section')) {
?>
<ul id="page_options">
	<li>
		<a class="icon-config config btn" href="<?php echo JRoute::_($base . '/settings'); ?>">
			<?php echo JText::_('PLG_GROUPS_FORUM_SETTINGS'); ?>
		</a>
	</li>
</ul>
<?php } ?>

<section class="main section">
<?php if ($this->sections->total()) { ?>
	<div class="subject">
		<?php foreach ($this->notifications as $notification) { ?>
			<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } ?>

		<form action="<?php echo JRoute::_($base); ?>" method="post">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo JText::_('PLG_GROUPS_FORUM_SEARCH_LEGEND'); ?></legend>
					<label for="entry-search-field"><?php echo JText::_('PLG_GROUPS_FORUM_SEARCH_LABEL'); ?></label>
					<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('PLG_GROUPS_FORUM_SEARCH_PLACEHOLDER'); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
					<input type="hidden" name="active" value="forum" />
					<input type="hidden" name="action" value="search" />
				</fieldset>
			</div><!-- / .container -->
		</form>

	<?php
		$filters = array('access' => 0);
		if (!JFactory::getUser()->get('guest'))
		{
			$filters['access'] = array(0, 1, 3);
			if ($this->config->get('access-view-section'))
			{
				$filters['access'] = array(0, 1, 3, 4);
			}
		}

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
						<input type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_SAVE'); ?>" />

						<input type="hidden" name="fields[id]" value="<?php echo $section->get('id'); ?>" />
						<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->model->get('scope')); ?>" />
						<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->model->get('scope_id')); ?>" />
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
						<input type="hidden" name="action" value="savesection" />
						<input type="hidden" name="active" value="forum" />
						<?php echo JHTML::_('form.token'); ?>
					</form>
				<?php } else { ?>
					<?php echo $this->escape(stripslashes($section->get('title'))); ?>
				<?php } ?>
			<?php if ($this->config->get('access-edit-section') || $this->config->get('access-delete-section')) { ?>
				<?php if ($this->config->get('access-delete-section')) { ?>
					<a class="icon-delete delete" href="<?php echo JRoute::_($base . '&scope=' . $section->get('alias') . '/delete'); ?>" title="<?php echo JText::_('PLG_GROUPS_FORUM_DELETE'); ?>">
						<span><?php echo JText::_('PLG_GROUPS_FORUM_DELETE'); ?></span>
					</a>
				<?php } ?>
				<?php if ($this->config->get('access-edit-section') && $this->edit != $section->get('alias')) { ?>
					<a class="icon-edit edit" href="<?php echo JRoute::_($base . '&scope=' . $section->get('alias') . '/edit#s' . $section->get('id')); ?>" title="<?php echo JText::_('PLG_GROUPS_FORUM_EDIT'); ?>">
						<span><?php echo JText::_('PLG_GROUPS_FORUM_EDIT'); ?></span>
					</a>
				<?php } ?>
			<?php } ?>
				</caption>
			<?php if ($this->config->get('access-create-category')) { ?>
				<tfoot>
					<tr>
						<td<?php if ($section->categories()->total() > 0) { echo ' colspan="5"'; } ?>>
							<a class="icon-add add btn" href="<?php echo JRoute::_($base . '&scope=' . $section->get('alias') . '/new'); ?>">
								<span><?php echo JText::_('PLG_GROUPS_FORUM_NEW_CATEGORY'); ?></span>
							</a>
						</td>
					</tr>
				</tfoot>
			<?php } ?>
				<tbody>
			<?php if ($section->categories('list', $filters)->total() > 0) { ?>
				<?php foreach ($section->categories() as $row) { ?>
					<tr<?php if ($row->get('closed')) { echo ' class="closed"'; } ?>>
						<th scope="row">
							<span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
						</th>
						<td>
							<a class="entry-title" href="<?php echo JRoute::_($row->link()); ?>">
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
								<?php echo JText::_('PLG_GROUPS_FORUM_DISCUSSIONS'); ?>
							</span>
						</td>
						<td>
							<span><?php echo $row->count('posts'); ?></span>
							<span class="entry-details">
								<?php echo JText::_('PLG_GROUPS_FORUM_POSTS'); ?>
							</span>
						</td>
					<?php if ($this->config->get('access-edit-category') || $this->config->get('access-delete-category')) { ?>
						<td class="entry-options">
							<?php if ($row->get('created_by') == $juser->get('id') || $this->config->get('access-edit-category')) { ?>
								<a class="icon-edit edit" href="<?php echo JRoute::_($row->link('edit')); ?>" title="<?php echo JText::_('PLG_GROUPS_FORUM_EDIT'); ?>">
									<span><?php echo JText::_('PLG_GROUPS_FORUM_EDIT'); ?></span>
								</a>
							<?php } ?>
							<?php if ($this->config->get('access-delete-category')) { ?>
								<a class="icon-delete delete tooltips" title="<?php echo JText::_('PLG_GROUPS_FORUM_DELETE_CATEGORY'); ?>" href="<?php echo JRoute::_($row->link('delete')); ?>" title="<?php echo JText::_('PLG_GROUPS_FORUM_DELETE'); ?>">
									<span><?php echo JText::_('PLG_GROUPS_FORUM_DELETE'); ?></span>
								</a>
							<?php } ?>
						</td>
					<?php } ?>
					</tr>
				<?php } ?>
			<?php } else { ?>
					<tr>
						<td><?php echo JText::_('PLG_GROUPS_FORUM_NO_CATEGORIES'); ?></td>
					</tr>
			<?php } ?>
				</tbody>
			</table>
		</div>
	<?php
		}
	?>

	<?php if ($this->config->get('access-create-section')) { ?>
		<div class="container">
			<form method="post" action="<?php echo JRoute::_($base); ?>">
					<table class="entries categories">
						<caption>
							<label for="field-title">
								<?php echo JText::_('PLG_GROUPS_FORUM_NEW_SECTION'); ?>
								<input type="text" name="fields[title]" id="field-title" value="" />
							</label>
							<input type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_SAVE'); ?>" />
						</caption>
						<tbody>
							<tr>
								<td><?php echo JText::_('PLG_GROUPS_FORUM_NEW_SECTION_EXPLANATION'); ?></td>
							</tr>
						</tbody>
					</table>

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
					<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->model->get('scope')); ?>" />
					<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->model->get('scope_id')); ?>" />
					<input type="hidden" name="active" value="forum" />
					<input type="hidden" name="action" value="savesection" />

					<?php echo JHTML::_('form.token'); ?>
				</fieldset>
			</form>
		</div><!-- /.container -->
	<?php } ?>

	<?php if (JComponentHelper::getParams('com_groups')->get('email_comment_processing') && $this->config->get('access-view-section')) { ?>
		<form method="post" action="<?php echo JRoute::_($base); ?>" id="forum-options">
			<fieldset>
				<legend><?php echo JText::_('PLG_GROUPS_FORUM_EMAIL_SETTINGS'); ?></legend>

				<input type="hidden" name="action" value="savememberoptions" />
				<input type="hidden" name="memberoptionid" value="<?php echo $this->recvEmailOptionID; ?>" />
				<input type="hidden" name="postsaveredirect" value="<?php echo JRoute::_($base); ?>" />

				<label class="option" for="recvpostemail">
					<input type="checkbox" class="option" id="recvpostemail" value="1" name="recvpostemail"<?php if ($this->recvEmailOptionValue == 1) { echo ' checked="checked"'; } ?> />
					<?php echo JText::_('PLG_GROUPS_FORUM_EMAIL_POSTS'); ?>
				</label>
				<input class="option" type="submit" value="<?php echo JText::_('PLG_GROUPS_FORUM_SAVE'); ?>" />
			</fieldset>
		</form>
	<?php } ?>

	</div><!-- /.subject -->
	<aside class="aside">
		<div class="container">
			<h3><?php echo JText::_('PLG_GROUPS_FORUM_STATISTICS'); ?></h3>
			<table>
				<tbody>
					<tr>
						<th><?php echo JText::_('PLG_GROUPS_FORUM_CATEGORIES'); ?></th>
						<td><span class="item-count"><?php echo $this->model->count('categories'); ?></span></td>
					</tr>
					<tr>
						<th><?php echo JText::_('PLG_GROUPS_FORUM_DISCUSSIONS'); ?></th>
						<td><span class="item-count"><?php echo $this->model->count('threads'); ?></span></td>
					</tr>
					<tr>
						<th><?php echo JText::_('PLG_GROUPS_FORUM_POSTS'); ?></th>
						<td><span class="item-count"><?php echo $this->model->count('posts'); ?></span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="container">
			<h3><?php echo JText::_('PLG_GROUPS_FORUM_LAST_POST'); ?></h3>
			<p>
			<?php
			if ($this->model->lastActivity()->exists())
			{
				$post = $this->model->lastActivity();

				$lname = JText::_('PLG_GROUPS_FORUM_ANONYMOUS');
				if (!$post->get('anonymous'))
				{
					$lname = $this->escape(stripslashes($post->creator('name', $lname)));
					if ($post->creator('public'))
					{
						$lname = '<a href="' . JRoute::_($post->creator()->getLink()) . '">' . $lname . '</a>';
					}
				}
				foreach ($this->sections as $section)
				{
					if ($section->categories()->total() > 0)
					{
						foreach ($section->categories() as $row)
						{
							if ($row->get('id') == $post->get('category_id'))
							{
								$post->set('category', $row->get('alias'));
								$post->set('section', $section->get('alias'));
								break;
							}
						}
					}
				}
				?>
				<a class="entry-comment" href="<?php echo JRoute::_($post->link()); ?>">
					<?php echo $post->content('clean', 170); ?>
				</a>
				<span class="entry-author">
					<?php echo $lname; ?>
				</span>
				<span class="entry-date">
					<span class="entry-date-at"><?php echo JText::_('PLG_GROUPS_FORUM_AT'); ?></span>
					<span class="icon-time time"><time datetime="<?php echo $post->get('created'); ?>"><?php echo $post->created('time'); ?></time></span>
					<span class="entry-date-on"><?php echo JText::_('PLG_GROUPS_FORUM_ON'); ?></span>
					<span class="icon-date date"><time datetime="<?php echo $post->get('created'); ?>"><?php echo $post->created('date'); ?></time></span>
				</span>
			<?php } else { ?>
				<?php echo JText::_('PLG_GROUPS_FORUM_NONE'); ?>
			<?php } ?>
			</p>
		</div>
	</aside><!-- / .aside -->
<?php } else { ?>
	<div class="instructions">
		<?php if ($this->config->get('access-create-section')) { ?>
			<p class="notification"><?php echo JText::sprintf('PLG_GROUPS_FORUM_EMPTY_MODERATOR', JRoute::_($base. '&action=populate')); ?></p>

			<div class="container">
				<form method="post" action="<?php echo JRoute::_($base); ?>">
					<fieldset class="entry-section">
						<legend><?php echo JText::_('PLG_GROUPS_FORUM_NEW_SECTION'); ?></legend>

						<span class="input-wrap">
							<label for="field-title"><span><?php echo JText::_('PLG_GROUPS_FORUM_FIELD_TITLE'); ?></span></label>
							<span class="input-cell">
								<input type="text" name="fields[title]" id="field-title" value="" placeholder="<?php echo JText::_('PLG_GROUPS_FORUM_ENTER_TITLE'); ?>" />
							</span>
							<span class="input-cell">
								<input type="submit" class="btn" value="<?php echo JText::_('PLG_GROUPS_FORUM_CREATE'); ?>" />
							</span>
						</span>

						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
						<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->model->get('scope')); ?>" />
						<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->model->get('scope_id')); ?>" />
						<input type="hidden" name="active" value="forum" />
						<input type="hidden" name="action" value="savesection" />

						<input type="hidden" name="fields[id]" value="" />
						<input type="hidden" name="fields[access]" value="0" />

						<?php echo JHTML::_('form.token'); ?>
					</fieldset>
				</form>
			</div><!-- / .container -->
		<?php } else { ?>
			<p class="notification"><?php echo JText::_('PLG_GROUPS_FORUM_EMPTY_NOT_MODERATOR'); ?></p>
		<?php } ?>
	</div>
<?php } ?>
</section><!-- /.main -->
