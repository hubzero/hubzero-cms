<?php // no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::_('stylesheet', 'poll_bars.css', 'components/com_poll/assets/');
?>
<?php if ($this->params->get( 'show_page_title', 1)) : ?>
<header id="content-header">
	<h2><?php echo JText::_('COM_POLL'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-stats btn" href="<?php echo JRoute::_('index.php?option=com_poll&view=latest'); ?>">
				<?php echo JText::_('COM_POLL_TAKE_LATEST_POLL'); ?>
			</a>
		</p>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->
<?php endif; ?>

<form action="<?php echo JRoute::_('index.php?option=com_poll&view=poll'); ?>" method="post" name="poll" id="poll">
	<section class="main section contentpane<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<label for="id">
			<?php echo JText::_('COM_POLL_SELECT'); ?>
			<?php echo $this->lists['polls']; ?>
		</label>
	</section>
	<section class="below section">
		<div class="contentpane<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
			<?php echo $this->loadTemplate('graph'); ?>
		</div>
	</section><!-- / .main section -->
</form>