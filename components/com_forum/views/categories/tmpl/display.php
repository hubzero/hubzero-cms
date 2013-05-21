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

$base = 'index.php?option=' . $this->option . '&section=' . $this->filters['section'] . '&category=' . $this->filters['category'];
?>

<div id="content-header">
	<h2><?php echo JText::_('COM_FORUM'); ?></h2>
</div>
<div id="content-header-extra">
	<p><a class="categories btn" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>"><?php echo JText::_('All categories'); ?></a></p>
</div>
<div class="clear"></div>

<?php foreach ($this->notifications as $notification) { ?>
<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
<?php } ?>

<div class="main section">
	<div class="aside">
		<div class="container">
			<h3><?php echo JText::_('Last Post'); ?><span class="starter-point"></span></h3>
			<p>
			<?php
			$last = $this->category->lastActivity();
			if ($last->exists()) 
			{
				$lname = JText::_('Anonymous');
				if (!$last->get('anonymous')) 
				{
					$lname = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $last->creator('id')) . '">' . $this->escape(stripslashes($last->creator('name'))) . '</a>';
				}
			?>
				<a href="<?php echo JRoute::_($base . '&thread=' . $last->get('thread')); ?>" class="entry-date">
					<span class="entry-date-at">@</span>
					<span class="time"><time datetime="<?php echo $last->created(); ?>"><?php echo $last->created('time'); ?></time></span> <span class="entry-date-on"><?php echo JText::_('COM_FORUM_ON'); ?></span> 
					<span class="date"><time datetime="<?php echo $last->created(); ?>"><?php echo $last->created('date'); ?></time></span>
				</a>
				<span class="entry-author">
					<?php echo JText::_('by'); ?>
					<?php echo $lname; ?>
				</span>
			<?php } else { ?>
				<?php echo JText::_('none'); ?>
			<?php } ?>
			</p>
		</div><!-- / .container -->
	<?php if ($this->config->get('access-create-thread')) { ?>
		<div class="container">
			<h3><?php echo JText::_('Start Your Own'); ?><span class="starter-point"></span></h3>
		<?php if (!$this->category->get('closed')) { ?>
			<p>
				<?php echo JText::_('Create your own discussion where you and other users can discuss related topics.'); ?>
			</p>
			<p>
				<a class="add btn" href="<?php echo JRoute::_($base . '&task=new'); ?>"><?php echo JText::_('Add Discussion'); ?></a>
			</p>
		<?php } else { ?>
			<p class="warning">
				<?php echo JText::_('This category is closed and no new discussions may be created.'); ?>
			</p>
		<?php } ?>
		</div><!-- / .container -->
	<?php } ?>
	</div><!-- / .aside -->

	<div class="subject">
		<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('Search'); ?>" />
				<fieldset class="entry-search">
					<legend><span><?php echo JText::_('Search posts'); ?></span></legend>
					
					<label for="entry-search-field"><?php echo JText::_('Enter keyword or phrase'); ?></label>
					<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Enter keyword or phrase'); ?>" />
					
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="categories" />
					<input type="hidden" name="task" value="search" />
				</fieldset>
			</div><!-- / .container -->
			
			<div class="container">
				<ul class="entries-menu order-options">
					<li>
						<a<?php echo ($this->filters['sortby'] == 'created') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($base . '&sortby=created'); ?>" title="<?php echo JText::_('Sort by created date'); ?>">
							<?php echo JText::_('&darr; Created'); ?>
						</a>
					</li>
					<li>
						<a<?php echo ($this->filters['sortby'] == 'activity') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($base . '&sortby=activity'); ?>" title="<?php echo JText::_('Sort by activity'); ?>">
							<?php echo JText::_('&darr; Activity'); ?>
						</a>
					</li>
					<li>
						<a<?php echo ($this->filters['sortby'] == 'replies') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($base . '&sortby=replies'); ?>" title="<?php echo JText::_('Sort by number of posts'); ?>">
							<?php echo JText::_('&darr; # Posts'); ?>
						</a>
					</li>
					<li>
						<a<?php echo ($this->filters['sortby'] == 'title') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($base . '&sortby=title'); ?>" title="<?php echo JText::_('Sort by title'); ?>">
							<?php echo JText::_('&darr; Title'); ?>
						</a>
					</li>
				</ul>

				<table class="entries">
					<caption>
						<?php
						if ($this->filters['search']) {
							if ($this->category->get('title')) {
								echo JText::sprintf('Search for "%s" in "%s"', $this->escape($this->filters['search']), $this->escape(stripslashes($this->category->get('title'))));
							} else {
								echo JText::sprintf('Search for "%s"', $this->escape($this->filters['search']));
							}
						} else {
							echo JText::sprintf('Discussions in "%s"', $this->escape(stripslashes($this->category->get('title'))));
						}
						?>
					</caption>
					<tbody>
				<?php
				if ($this->category->threads('list', $this->filters)->total() > 0) {
					foreach ($this->category->threads() as $row) 
					{
						$name = JText::_('Anonymous');
						if (!$row->get('anonymous'))
						{
							$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $row->creator('id')) . '">' . $this->escape(stripslashes($row->creator('name'))) . '</a>';
						}
						?>
						<tr<?php if ($row->get('sticky')) { echo ' class="sticky"'; } ?>>
							<th>
								<span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
							</th>
							<td>
								<a class="entry-title" href="<?php echo JRoute::_($base . '&thread=' . $row->get('id')); ?>">
									<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
								</a>
								<span class="entry-details">
									<span class="entry-date">
										<time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time>
									</span>
									<?php echo JText::_('by'); ?>
									<span class="entry-author">
										<?php echo $name; ?>
									</span>
								</span>
							</td>
							<td>
								<span><?php echo ($row->posts('count')); ?></span>
								<span class="entry-details">
									<?php echo JText::_('Comments'); ?>
								</span>
							</td>
							<td>
								<span><?php echo JText::_('Last Post:'); ?></span>
								<span class="entry-details">
							<?php 
								$lastpost = $row->lastActivity();
								if ($lastpost->exists()) 
								{
										$lname = JText::_('Anonymous');
										if (!$lastpost->get('anonymous')) 
										{
											$lname = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $lastpost->creator('id')) . '">' . $this->escape(stripslashes($lastpost->creator('name'))) . '</a>';
										}
									?>
									<span class="entry-date">
										<time datetime="<?php echo $lastpost->created(); ?>"><?php echo $lastpost->created('date'); ?></time>
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
						<?php if ($this->config->get('access-manage-thread') || $this->config->get('access-edit-thread') || $this->config->get('access-delete-thread')) { ?>
							<td class="entry-options">
								<?php if ($this->config->get('access-manage-thread') || ($this->config->get('access-edit-thread') && $row->get('created_by') == $juser->get('id'))) { ?>
									<a class="edit" href="<?php echo JRoute::_($base . '&thread=' . $row->get('id') . '&task=edit'); ?>">
										<?php echo JText::_('COM_FORUM_EDIT'); ?>
									</a>
								<?php } ?>
								<?php if ($this->config->get('access-manage-thread') || ($this->config->get('access-delete-thread') && $row->get('created_by') == $juser->get('id'))) { ?>
									<a class="delete" href="<?php echo JRoute::_($base . '&thread=' . $row->get('id') . '&task=delete'); ?>">
										<?php echo JText::_('COM_FORUM_DELETE'); ?>
									</a>
								<?php } ?>
							</td>
						<?php } ?>
						</tr>
					<?php } ?>
				<?php } else { ?>
						<tr>
							<td><?php echo JText::_('There are currently no discussions.'); ?></td>
						</tr>
				<?php } ?>
					</tbody>
				</table>

				<?php 
				jimport('joomla.html.pagination');
				$pageNav = new JPagination(
					$this->category->threads('count', $this->filters), 
					$this->filters['start'], 
					$this->filters['limit']
				);
				$pageNav->setAdditionalUrlParam('section', $this->filters['section']);
				$pageNav->setAdditionalUrlParam('category', $this->filters['category']);
				$pageNav->setAdditionalUrlParam('q', $this->filters['search']);
				echo $pageNav->getListFooter();
				?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</form>
	</div><!-- /.subject -->
	<div class="clear"></div>
</div><!-- /.main -->