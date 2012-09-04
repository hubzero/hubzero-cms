<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php if ($this->params->get( 'show_page_title', 1)) : ?>
<div id="content-header">
	<h2><?php echo $this->escape($this->params->get('page_title')); ?></h2>
</div><!-- / #content-header -->
<?php endif; ?>
<?php echo $this->loadTemplate($this->type); ?>

