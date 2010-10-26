<?php
/**
 * @version		$Id$
 * @package		Joomla Content Editor (JCE)
 * @copyright	Copyright (C) 2005 - 2009 Ryan Demmer. All rights reserved.
 * @license		GNU/GPL
 * This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.mootools');
JHTML::script('popup.js', 'components/com_jce/js/');
JHTML::stylesheet('popup.css', 'components/com_jce/css/');
?>
<script type="text/javascript">
	window.addEvent('load', function(){
		jcePopupWindow.init(<?php echo $this->features['width'];?>, <?php echo $this->features['height'];?>, <?php echo $this->features['click'];?>);
	});
</script>
<style type="text/css">
	/* Reset template style sheet */
	body{margin:0;padding:0;}div{margin:0;padding:0;}img{margin:0;padding:0;}
</style>
<div id="jce_popup">
    <?php if( $this->features['mode'] ){?>
    <div class="contentheading"><?php echo $this->features['title'];?></div>
    <?php }?>
    <?php if( $this->features['mode'] && $this->features['print'] ){?>
    <div class="buttonheading"><a href="javascript:;" onClick="window.print(); return false"><img src="<?php echo JURI::root(); ?>images/M_images/printButton.png" width="16" height="16" alt="<?php echo JText::_('Print');?>" title="<?php echo JText::_('Print');?>" /></a></div>
    <?php }?>
    <div><img src="<?php echo $this->features['img'];?>" width="<?php echo $this->features['width'];?>" height="<?php echo $this->features['height'];?>" alt="<?php echo $this->features['alt'];?>" onclick="window.close();" /></div>
</div>