<?php // no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::_('stylesheet', 'poll_bars.css', 'components/com_poll/assets/');
?>
<?php if ($this->params->get( 'show_page_title', 1)) : ?>
<div id="content-header">
	<h2><?php echo JText::_('Polls'); ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<p><a class="stats btn" href="<?php echo JRoute::_('index.php?option=com_poll&view=latest'); ?>"><?php echo JText::_('Take the latest poll'); ?></a></p>
</div><!-- / #content-header-extra -->
<?php endif; ?>

<form action="<?php echo JRoute::_('index.php?option=com_poll&view=poll'); ?>" method="post" name="poll" id="poll">
	<div class="main section contentpane<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<label for="id">
			<?php echo JText::_('Select Poll'); ?>
			<?php echo $this->lists['polls']; ?>
		</label>
	</div>
	<div class="below section">
		<div class="contentpane<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
<?php echo $this->loadTemplate('graph'); ?>
		</div>
	</div><!-- / .main section -->
</form>