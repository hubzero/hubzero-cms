<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$i = 1;

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->
<div id="content-header-extra">
    <ul id="useroptions">
    	<li><a class="btn icon-browse" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo JText::_('COM_PUBLICATIONS_BROWSE') . ' ' . JText::_('COM_PUBLICATIONS_PUBLICATIONS'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->
<div class="clear"></div>
<?php if ($this->getError()) { ?>
<div class="status-msg">
<?php
	// Display error or success message
	if ($this->getError()) {
		echo ('<p class="witherror">' . $this->getError().'</p>');
	}
?>
</div>
<?php } ?>
<div class="clear block">&nbsp;</div>
<section class="section intropage">
	<div class="grid">
		<div class="col <?php echo ($this->contributable) ? 'span4' : 'span6';  ?>">
			<h3><?php echo JText::_('Recent Publications'); ?></h3>
			<?php if ($this->results && count($this->results) > 0) { ?>
				<ul class="mypubs">
					<?php foreach ($this->results as $row) {
						// Get version authors
						$pa = new PublicationAuthor( $this->database );
						$authors = $pa->getAuthors($row->version_id);
						$info = array();
						$info[] =  JHTML::_('date', $row->published_up, 'd M Y');
						$info[] = $row->cat_name;
						$info[] = JText::_('COM_PUBLICATIONS_CONTRIBUTORS').': '. $this->helper->showContributors( $authors, false, true );

						$pubthumb = $this->helper->getThumb($row->id, $row->version_id, $this->config, false, $row->cat_url);
					?>
					<li>
						<span class="pub-thumb"><img src="<?php echo $pubthumb; ?>" alt=""/></span>
						<span class="pub-details">
							<a href="<?php echo JRoute::_('index.php?option=com_publications'.a.'id='.$row->id); ?>" title="<?php echo stripslashes($row->abstract); ?>"><?php echo \Hubzero\Utility\String::truncate(stripslashes($row->title), 100); ?></a>
							<span class="block details"><?php echo implode(' <span>|</span> ', $info); ?></span>
						</span>
					</li>
					<?php }?>
				</ul>
			<?php } else {
				echo ('<p class="noresults">'.JText::_('COM_PUBLICATIONS_NO_RELEVANT_PUBS_FOUND').'</a></p>');
			} ?>
		</div>
		<div class="col <?php echo ($this->contributable) ? 'span4' : 'span6';  ?>">
			<h3><?php echo JText::_('COM_PUBLICATIONS_PUPULAR'); ?></h3>
			<?php if ($this->best && count($this->best) > 0) { ?>
				<ul class="mypubs">
					<?php foreach ($this->best as $row) {
						// Get version authors
						$pa = new PublicationAuthor( $this->database );
						$authors = $pa->getAuthors($row->version_id);
						$info = array();
						$info[] =  JHTML::_('date', $row->published_up, 'd M Y');
						$info[] = $row->cat_name;
						$info[] = JText::_('COM_PUBLICATIONS_CONTRIBUTORS').': '. $this->helper->showContributors( $authors, false, true );

						$pubthumb = $this->helper->getThumb($row->id, $row->version_id, $this->config, false, $row->cat_url);
					?>
					<li>
						<span class="pub-thumb"><img src="<?php echo $pubthumb; ?>" alt=""/></span>
						<span class="pub-details">
							<a href="<?php echo JRoute::_('index.php?option=com_publications'.a.'id='.$row->id); ?>" title="<?php echo stripslashes($row->abstract); ?>"><?php echo \Hubzero\Utility\String::truncate(stripslashes($row->title), 100); ?></a>
							<span class="block details"><?php echo implode(' <span>|</span> ', $info); ?></span>
						</span>
					</li>
					<?php }?>
				</ul>
			<?php } else {
				echo ('<p class="noresults">'.JText::_('COM_PUBLICATIONS_NO_RELEVANT_PUBS_FOUND').'</a></p>');
			} ?>
		</div>
		<?php if ($this->contributable) { ?>
		<div class="col span4 omega">
			<h3><?php echo JText::_('COM_PUBLICATIONS_WHO_CAN_SUBMIT'); ?></h3>
			<p><?php echo JText::_('COM_PUBLICATIONS_WHO_CAN_SUBMIT_ANYONE'); ?></p>
			<p><a href="<?php echo JRoute::_('index.php?option=com_publications&task=submit'); ?>" class="btn"><?php echo JText::_('COM_PUBLICATIONS_START_PUBLISHING'); ?> &raquo;</a></p>
		</div>
		<?php } ?>
	</div>
</section><!-- / .section -->
