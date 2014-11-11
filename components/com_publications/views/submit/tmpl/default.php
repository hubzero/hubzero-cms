<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$this->css()
     ->css('jquery.fancybox.css', 'system')
     ->js();
?>
<header id="content-header"><h2><?php echo $this->title; ?></h2></header><!-- / #content-header -->

<?php if ($this->pid && $this->project && $this->project->created_by_user == $this->uid) { ?>
	<p class="contrib-options">
		<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_NEED_A_PROJECT'); ?>
		<a href="<?php echo JRoute::_('index.php?option=com_projects&alias=' . $this->project->alias) . '/?action=activate'; ?>">
		<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_LEARN_MORE'); ?> &raquo;</a>
	</p>
<?php } ?>

<div class="status-msg" id="status-msg">
	<?php
	// Display error or success message
	if ($this->getError()) {
		echo ('<p class="witherror">' . $this->getError().'</p>');
	}
	else if ($this->msg) {
		echo ('<p>' . $this->msg . '</p>');
	} ?>
</div>
<section id="contrib-section" class="section">
	<?php echo $this->content; ?>
</section><!-- / .section -->