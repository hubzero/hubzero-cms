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

$wikiconfig = array(
	'option'   => $this->option,
	'scope'    => 'forum',
	'pagename' => 'forum',
	'pageid'   => 0,
	'filepath' => '',
	'domain'   => 0
);
ximport('Hubzero_Wiki_Parser');
$p =& Hubzero_Wiki_Parser::getInstance();
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
						<td><span class="item-count"><?php echo $this->stats->categories; ?></span></td>
					</tr>
					<tr>
						<th><?php echo JText::_('Discussions'); ?></th>
						<td><span class="item-count"><?php echo $this->stats->threads; ?></span></td>
					</tr>
					<tr>
						<th><?php echo JText::_('Posts'); ?></th>
						<td><span class="item-count"><?php echo $this->stats->posts; ?></span></td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="container">
			<h3><?php echo JText::_('Last Post'); ?><span class="starter-point"></span></h3>
			<p>
<?php
			if (is_object($this->lastpost)) 
			{
				$lname = JText::_('Anonymous');
				$lastposter = JUser::getInstance($this->lastpost->created_by);
				if (is_object($lastposter)) 
				{
					$lname = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $lastposter->get('id')) . '">' . $this->escape(stripslashes($lastposter->get('name'))) . '</a>';
				}
				foreach ($this->sections as $section)
				{
					if ($section->categories) 
					{
						foreach ($section->categories as $row) 
						{
							if ($row->id == $this->lastpost->category_id)
							{
								$cat = $row->alias;
								$sec = $section->alias;
								break;
							}
						}
					}
				}
?>
				<a class="entry-date" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum&scope=' . $sec . '/' . $cat . '/' . ($this->lastpost->parent ? $this->lastpost->parent : $this->lastpost->id)); ?>">
					<span class="entry-date-at">@</span>
					<span class="time"><time datetime="<?php echo $this->lastpost->created; ?>"><?php echo JHTML::_('date', $this->lastpost->created, $timeFormat, $tz); ?></time></span> <span class="entry-date-on"><?php echo JText::_('PLG_GROUPS_FORUM_ON'); ?></span> 
					<span class="date"><time datetime="<?php echo $this->lastpost->created; ?>"><?php echo JHTML::_('date', $this->lastpost->created, $dateFormat, $tz); ?></time></span>
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
		<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum'); ?>" method="post">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('Search'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo JText::_('Search categories'); ?></legend>
					<label for="entry-search-field"><?php echo JText::_('Enter keyword or phrase'); ?></label>
					<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
					<input type="hidden" name="active" value="forum" />
					<input type="hidden" name="action" value="search" />
				</fieldset>
			</div><!-- / .container -->
		</form>
<?php
if (count($this->sections) > 0) {
	foreach ($this->sections as $section)
	{
		if ($section->id == 0 && !$section->categories[0]->posts) 
		{
			continue;
		}
?>
		<div class="container">
			<table class="entries categories">
				<caption>
<?php if ($this->config->get('access-edit-section') && $this->edit == $section->alias && $section->id) { ?>
					<a name="s<?php echo $section->id; ?>"></a>
					<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum'); ?>" method="post">
					<input type="text" name="fields[title]" value="<?php echo $this->escape(stripslashes($section->title)); ?>" />
					<input type="submit" value="<?php echo JText::_('Save'); ?>" />
					<input type="hidden" name="fields[id]" value="<?php echo $section->id; ?>" />
					<input type="hidden" name="fields[scope]" value="group" />
					<input type="hidden" name="fields[scope_id]" value="<?php echo $this->group->get('gidNumber'); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
					<input type="hidden" name="action" value="savesection" />
					<input type="hidden" name="active" value="forum" />
					</form>
<?php } else { ?>
					<?php echo $this->escape(stripslashes($section->title)); ?>
<?php } ?>
			<?php if (($this->config->get('access-edit-section') || $this->config->get('access-delete-section')) && $section->id) { ?>
				<?php if ($this->config->get('access-delete-section')) { ?>
					<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum&scope=' . $section->alias . '/delete'); ?>" title="<?php echo JText::_('Delete'); ?>">
						<span><?php echo JText::_('Delete'); ?></span>
					</a>
				<?php } ?>
				<?php if ($this->config->get('access-edit-section') && $this->edit != $section->alias && $section->id) { ?>
					<a class="edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum&scope=' . $section->alias . '/edit#s' . $section->id); ?>" title="<?php echo JText::_('Edit'); ?>">
						<span><?php echo JText::_('Edit'); ?></span>
					</a>
				<?php } ?>
			<?php } ?>
				</caption>
			<?php if ($this->config->get('access-create-category')) { ?>
				<tfoot>
					<tr>
						<td<?php if ($section->categories) { echo ' colspan="5"'; } ?>>
							<a class="add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum&scope=' . $section->alias . '/new'); ?>">
								<span><?php echo JText::_('Add Category'); ?></span>
							</a>
						</td>
					</tr>
				</tfoot>
			<?php } ?>
				<tbody>
<?php 
if ($section->categories) { 
		foreach ($section->categories as $row) 
		{ 
?>
					<tr<?php if ($row->closed) { echo ' class="closed"'; } ?>>
						<th scope="row">
							<span class="entry-id"><?php echo $this->escape($row->id); ?></span>
						</th>
						<td>
							<a class="entry-title" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum&scope=' . $section->alias . '/' . $row->alias); ?>">
								<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
							</a>
							<span class="entry-details">
								<span class="entry-description">
									<?php echo $p->parse(stripslashes($row->description), $wikiconfig, false); ?>
								</span>
							</span>
						</td>
						<td>
							<span><?php echo $row->threads; ?></span>
							<span class="entry-details">
								<?php echo JText::_('Discussions'); ?>
							</span>
						</td>
						<td>
							<span><?php echo $row->posts; ?></span>
							<span class="entry-details">
								<?php echo JText::_('Posts'); ?>
							</span>
						</td>
<?php 			if ($this->config->get('access-edit-category') || $this->config->get('access-delete-category')) { ?>
						<td class="entry-options">
							<?php if (($row->created_by == $juser->get('id') || $this->config->get('access-edit-category')) && $section->id) { ?>
								<a class="edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum&scope=' . $section->alias . '/' . $row->alias . '/edit'); ?>" title="<?php echo JText::_('Edit'); ?>">
									<span><?php echo JText::_('Edit'); ?></span>
								</a>
							<?php } ?>
							<?php if ($this->config->get('access-delete-category') && $section->id) { ?>
								<a class="delete tooltips" title="<?php echo JText::_('PLG_GROUPS_FORUM_DELETE_CATEGORY'); ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum&scope=' . $section->alias . '/' . $row->alias . '/delete'); ?>" title="<?php echo JText::_('Delete'); ?>">
									<span><?php echo JText::_('Delete'); ?></span>
								</a>
							<?php } ?>
						</td>
<?php 			} ?>
					</tr>
<?php 
		}
	} else { 
?>
					<tr>
						<td><?php echo JText::_('There are no categories.'); ?></td>
					</tr>
<?php } ?>
				</tbody>
			</table>
		</div>
<?php
	}
} else {
?>
	<p><?php echo JText::_('This forum is currently empty.'); ?></p>
<?php
}
?>

<?php if ($this->config->get('access-create-section')) { ?>
		<div class="container">
			<form method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum'); ?>">
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
					<input type="hidden" name="fields[scope]" value="group" />
					<input type="hidden" name="fields[scope_id]" value="<?php echo $this->group->get('gidNumber'); ?>" />
					<input type="hidden" name="active" value="forum" />
					<input type="hidden" name="action" value="savesection" />
				</fieldset>
			</form>
		</div><!-- /.container -->
<?php } ?>

<?php 
$params =& JComponentHelper::getParams('com_groups');
if ($params->get('email_comment_processing') && $this->config->get('access-create-section'))
{ ?>
			<form method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=memberoptions'); ?>" id="forum-options">
				<fieldset>
					<legend><?php echo JText::_('Email Settings'); ?></legend>
					
					<input type="hidden" name="action" value="savememberoptions" />
					<input type="hidden" name="memberoptionid" value="<?php echo $this->recvEmailOptionID; ?>" />
					<input type="hidden" name="postsaveredirect" value="<?php echo JRoute::_('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum') ?>" />

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
