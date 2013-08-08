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

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum';
?>
<ul id="page_options">
	<li>
		<a class="icon-folder categories btn" href="<?php echo JRoute::_($base); ?>"><?php echo JText::_('All categories'); ?></a>
	</li>
</ul>

<div class="main section">
	<?php foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
	<?php } ?>

		<form action="<?php echo JRoute::_($base); ?>" method="post">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('Search'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo JText::_('Search for articles'); ?></legend>
					<label for="entry-search-field"><?php echo JText::_('Enter keyword or phrase'); ?></label>
					<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Enter keyword or phrase'); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
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
			if ($this->thread->posts('list', $this->filters)->total() > 0) {
				foreach ($this->thread->posts() as $row) 
				{
					$title = $this->escape(stripslashes($row->get('title')));
					$title = preg_replace('#' . $this->filters['search'] . '#i', "<span class=\"highlight\">\\0</span>", $title);

					$name = JText::_('Anonymous');
					if (!$row->get('anonymous'))
					{
						$name = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $row->creator('id')) . '">' . $this->escape(stripslashes($row->creator('name'))) . '</a>';
					}
					$cls = array();
					if ($row->get('closed')) 
					{
						$cls[] = 'closed';
					}
					if ($row->get('sticky')) 
					{
						$cls[] = 'sticky';
					}
					?>
						<tr<?php if (count($cls) > 0) { echo ' class="' . implode(' ', $cls) . '"'; } ?>>
							<th>
								<span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
							</th>
							<td>
								<a class="entry-title" href="<?php echo JRoute::_($base . '&scope=' . $this->sections[$this->categories[$row->get('category_id')]->get('section_id')]->get('alias') . '/' . $this->categories[$row->get('category_id')]->get('alias') . '/' . $row->get('thread')); ?>">
									<span><?php echo $title; ?></span>
								</a>
								<span class="entry-details">
									<span class="entry-date">
										<?php echo $row->created('date'); ?>
									</span>
									<?php echo JText::_('by'); ?>
									<span class="entry-author">
										<?php echo $name; ?>
									</span>
								</span>
							</td>
							<td>
								<span><?php echo JText::_('Section'); ?></span>
								<span class="entry-details">
									<?php echo $this->escape($this->sections[$this->categories[$row->get('category_id')]->get('section_id')]->get('title')); ?>
								</span>
							</td>
							<td>
								<span><?php echo JText::_('Category'); ?></span>
								<span class="entry-details">
									<?php echo $this->escape($this->categories[$row->get('category_id')]->get('title')); ?>
								</span>
							</td>
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
						$this->thread->posts('count', $this->filters), 
						$this->filters['start'], 
						$this->filters['limit']
					);
					$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
					$pageNav->setAdditionalUrlParam('active', 'forum');
					//$pageNav->setAdditionalUrlParam('scope', $this->filters['section'] . '/' . $this->filters['category']);
					$pageNav->setAdditionalUrlParam('search', $this->filters['search']);
				?>
				<div class="clear"></div>
			</div><!-- / .container -->
		</form>

</div><!-- /.main -->