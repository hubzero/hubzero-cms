<?php
defined('_JEXEC') or die('Restricted access');
JToolBarHelper::title(JText::_('Joomdle'), 'generic.png');
//JToolBarHelper::preferences('com_joomdle', 480);
JToolBarHelper::save('save_config');
JToolBarHelper::apply('apply_config');
JToolBarHelper::cancel();
//JToolBarHelper::custom( 'save_config', 'save', 'save', 'Save', false, false );


//print_r ($this->system_info);
?>
<form action="index.php" method="post" id="adminForm" name="adminForm" autocomplete="off">

       <table class="adminlist">

             <tbody>
	     	<tr>
			<td>
			<?php require_once (dirname(__FILE__) . DS . 'config_general.php'); ?>
			<?php require_once (dirname(__FILE__) . DS . 'config_links.php'); ?>
			<?php require_once (dirname(__FILE__) . DS . 'config_detail.php'); ?>
			</td>
			<td valign='top'>
			<?php require_once (dirname(__FILE__) . DS . 'config_datasource.php'); ?>
			<?php require_once (dirname(__FILE__) . DS . 'config_customprofiles.php'); ?>
			<?php require_once (dirname(__FILE__) . DS . 'config_shop.php'); ?>
			</td>
		</tr>
             </tbody>
       </table>
 <input type="hidden" name="option" value="<?php echo JRequest::getVar( 'option' );?>"/>
       <input type="hidden" name="task" value=""/>
       <input type="hidden" name="boxchecked" value="0"/>
       <input type="hidden" name="hidemainmenu" value="0"/>

                <?php echo JHTML::_( 'form.token' ); ?>

</form>
