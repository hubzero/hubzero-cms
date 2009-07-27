<?php // @version $Id: default_error.php 10381 2008-06-01 03:35:53Z pasamio $
defined('_JEXEC') or die('Restricted access');
?>

<h2 class="error<?php $this->params->get( 'pageclass_sfx' ) ?>">
	<?php echo JText::_('Error') ?>
</h2>
<div class="error<?php echo $this->params->get( 'pageclass_sfx' ) ?>">
	<p><?php $this->escape($this->error); ?></p>
</div>
