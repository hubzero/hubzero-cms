<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<?php if($this->pid && $this->project && $this->project->created_by_user == $this->uid) { ?>
<p class="contrib-options">
	<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_NEED_A_PROJECT'); ?> 
	<a href="<?php echo JRoute::_('index.php?option=com_projects' . a . 'alias='.$this->project->alias).'/?action=activate'; ?>">
	<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LEARN_MORE'); ?> &raquo;</a>
</p>	
<?php } ?>
<div class="clear"></div>
<div id="contrib-section" class="section">
	<div class="status-msg" id="status-msg">
	<?php 
		// Display error or success message
		if ($this->getError()) { 
			echo ('<p class="witherror">' . $this->getError().'</p>');
		}
		else if($this->msg) {
			echo ('<p>' . $this->msg . '</p>');
		} ?>
	</div>
	<?php echo $this->content; ?>
</div><!-- / .section -->