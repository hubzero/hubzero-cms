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

<div id="content-header">
	<h2><?php echo $this->escape($this->title); ?></h2>
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
	<?php if ($this->config->get('access-create-thread')) { ?>
		<div class="container">
			<h3><?php echo JText::_('Start Your Own'); ?><span class="starter-point"></span></h3>
		<?php if (!$this->category->get('closed')) { ?>
			<p>
				<?php echo JText::_('Create your own discussion where you and other users can discuss related topics.'); ?>
			</p>
			<p>
				<a class="add" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>"><?php echo JText::_('Add Discussion'); ?></a>
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
		<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('Search'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo JText::_('Search posts'); ?></legend>
					<label for="entry-search-field"><?php echo JText::_('Enter keyword or phrase'); ?></label>
					<input type="text" name="q" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Enter keyword or phrase'); ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="categories" />
					<input type="hidden" name="task" value="search" />
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
								<a class="entry-title" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&section=' . $this->sections[$this->categories[$row->get('category_id')]->get('section_id')]->get('alias') . '&category=' . $this->categories[$row->get('category_id')]->get('alias') . '&thread=' . $row->get('thread') . '&q=' . $this->filters['search']); ?>">
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
					$pageNav->setAdditionalUrlParam('q', $this->filters['search']);
					echo $pageNav->getListFooter();
				?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</form>
	</div><!-- /.subject -->
</div><!-- /.main -->