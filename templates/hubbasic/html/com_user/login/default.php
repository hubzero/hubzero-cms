<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php if ($this->params->get( 'show_page_title', 1)) : ?>
<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<h2><?php echo $this->escape($this->params->get('page_title')); ?></h2>
</div>
<?php endif; ?>
<?php echo $this->loadTemplate($this->type); ?>