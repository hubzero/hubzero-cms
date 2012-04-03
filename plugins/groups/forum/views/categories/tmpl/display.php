<?php 
defined('_JEXEC') or die('Restricted access');

$juser =& JFactory::getUser();

$dateFormat = '%d %b, %Y';
$timeFormat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$timeFormat = 'h:m a';
	$tz = true;
}
?>
<div id="content-header-extra">
	<p><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum'); ?>"><?php echo JText::_('&larr; All categories'); ?></a></p>
</div>
<div class="main section">
<?php foreach ($this->notifications as $notification) { ?>
	<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
<?php } ?>
	<div class="aside">
<?php if ($this->config->get('access-create-thread')) { ?>
		<div class="container">
			<h3><?php echo JText::_('Start Your Own'); ?><span class="starter-point"></span></h3>
<?php if (!$this->category->closed) { ?>
			<p>
				<?php echo JText::_('Create your own discussion where you and other users can discuss related topics.'); ?>
			</p>
			<p class="add">
				<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->filters['category'] . '/new'); ?>"><?php echo JText::_('Add Discussion'); ?></a>
			</p>
<?php } else { ?>
			<p class="warning">
				<?php echo JText::_('This category is closed and no new discussions may be created.'); ?>
			</p>
<?php } ?>
		</div>
<?php } ?>
	</div><!-- / .aside -->

	<div class="subject">
		<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum'); ?>" method="post">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('Search'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo JText::_('Search for articles'); ?></legend>				
					<label for="entry-search-field"><?php echo JText::_('Enter keyword or phrase'); ?></label>
					<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
					<input type="hidden" name="active" value="forum" />
					<input type="hidden" name="action" value="search" />
				</fieldset>
			</div><!-- / .container -->
			
			<div class="container">
				<table class="entries">
					<caption>
<?php
			if ($this->filters['search']) {
				if ($this->category->id) {
					echo JText::sprintf('Search for "%s" in "%s"', $this->escape($this->filters['search']), $this->escape(stripslashes($this->category->title)));
				} else {
					echo JText::sprintf('Search for "%s"', $this->escape($this->filters['search']));
				}
			} else {
				if ($this->category->id) {
					echo JText::sprintf('Discussions in "%s"', $this->escape(stripslashes($this->category->title)));
				} else {
					echo JText::_('Discussions');
				}
			}
?>
<?php /*if (!$this->category->closed && $this->config->get('access-create-thread')) { ?>
						<a class="add" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->filters['category'] . '/new'); ?>"><?php echo JText::_('Add Discussion'); ?></a>
<?php }*/ ?>
					</caption>
					<tbody>
<?php
			if ($this->rows) {
				foreach ($this->rows as $row) 
				{
					$name = JText::_('Anonymous');
					if (!$row->anonymous)
					{
						$creator =& JUser::getInstance($row->created_by);
						if (is_object($creator)) 
						{
							$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $creator->get('id')) . '">' . $this->escape(stripslashes($creator->get('name'))) . '</a>';
						}
					}
?>
						<tr<?php if ($row->sticky) { echo ' class="sticky"'; } ?>>
							<th>
								<span class="entry-id"><?php echo $this->escape($row->id); ?></span>
							</th>
							<td>
								<a class="entry-title" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->filters['category'] . '/' . $row->id); ?>">
									<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
								</a>
								<span class="entry-details">
									<span class="entry-date">
										<?php echo JHTML::_('date', $row->created, $dateFormat, $tz); ?>
									</span>
									<?php echo JText::_('by'); ?>
									<span class="entry-author">
										<?php echo $name; ?>
									</span>
								</span>
							</td>
							<td>
								<span><?php echo ($row->replies + 1); ?></span>
								<span class="entry-details">
									<?php echo JText::_('Comments'); ?>
								</span>
							</td>
							<td>
								<span><?php echo JText::_('Last Post:'); ?></span>
								<span class="entry-details">
<?php 
									/*$lastpost = null;
									if ($row->last_activity != '0000-00-00 00:00:00')
									{*/
										$lastpost = $this->forum->getLastPost($row->id);
									//}
									if (is_object($lastpost)) 
									{
										$lname = JText::_('Anonymous');
										$lastposter =& JUser::getInstance($lastpost->created_by);
										if (is_object($lastposter)) 
										{
											$lname = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $lastposter->get('id')) . '">' . $this->escape(stripslashes($lastposter->get('name'))) . '</a>';
										}
?>
									<span class="entry-date">
										<?php echo JHTML::_('date', $lastpost->created, $dateFormat, $tz); ?>
									</span>
									<?php echo JText::_('by'); ?>
									<span class="entry-author">
										<?php echo $lname; ?>
									</span>
							<?php } else { ?>
									<?php echo JText::_('none'); ?>
							<?php } ?>
								</span>
							</td>
						<?php if ($this->config->get('access-delete-thread') || $this->config->get('access-edit-thread')) { ?>
							<td class="entry-options">
								<?php if ($row->created_by == $juser->get('id') || $this->config->get('access-edit-thread')) { ?>
									<a class="edit" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->filters['category'] . '/' . $row->id . '/edit'); ?>">
										<?php echo JText::_('PLG_GROUPS_FORUM_EDIT'); ?>
									</a>
								<?php } ?>
								<?php if ($this->config->get('access-delete-thread')) { ?>
									<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=forum&scope=' . $this->filters['section'] . '/' . $this->filters['category'] . '/' . $row->id . '/delete'); ?>">
										<?php echo JText::_('PLG_GROUPS_FORUM_DELETE'); ?>
									</a>
								<?php } ?>
							</td>
						<?php } ?>
						</tr>
<?php 
				}
			} else { ?>
						<tr>
							<td><?php echo JText::_('There are currently no discussions.'); ?></td>
						</tr>
<?php 		} ?>
					</tbody>
				</table>
<?php 
			if ($this->pageNav) {
				// @FIXME: Nick's Fix Based on Resources View
				$pf = $this->pageNav->getListFooter();
				//var_dump($pf);
				$nm = str_replace('com_', '', $this->option);
				//$pf = str_replace($nm.'/?',$nm.'/'.$this->group->get('cn').'/'.$this->_element.'/?',$pf);
				echo $pf;
				//echo $this->pageNav->getListFooter();
				// @FIXME: End Nick's Fix
			}
?>
				<div class="clear"></div>
			</div><!-- / .container -->
		</form>
	</div><!-- /.subject -->
</div><!-- /.main -->
