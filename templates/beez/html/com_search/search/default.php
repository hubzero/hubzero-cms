<?php // @version $Id: default.php 9837 2008-01-03 16:49:24Z tsai146 $
defined('_JEXEC') or die('Restricted access');
?>

<?php if($this->params->get('show_page_title')) : ?>
<h2 class="componentheading<?php echo $this->params->get('pageclass_sfx') ?>">
	<?php echo $this->escape($this->params->get('page_title')) ?>
</h2>
<?php endif; ?>

<div id="page">

<?php if (!$this->error) :
	echo $this->loadTemplate('results');
else :
	echo $this->loadTemplate('error');
endif; ?>

<?php echo $this->loadTemplate('form'); ?>
</div>