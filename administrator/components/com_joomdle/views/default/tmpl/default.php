<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pane');

JToolBarHelper::title(JText::_('Joomdle'), 'generic.png');
//JToolBarHelper::preferences('com_joomdle', 480);
//JToolBarHelper::save('save');

//print_r ($this->system_info);
?>
<form action="index.php" method="post" id="adminForm" name="adminForm" autocomplete="off">

       <table class="adminlist">

             <tbody>
	     	<tr>
			<td width="55%" valign="top">
			<div id="cpanel">
			<?php
			$link = 'index.php?option=com_joomdle&amp;view=config';
			$this->showButton( $link, 'config.png', JText::_( 'CJ CONFIGURATION' ) );
			$link = 'index.php?option=com_joomdle&amp;view=users';
			$this->showButton( $link, 'users.png', JText::_( 'CJ USERS' ) );
			$link = 'index.php?option=com_joomdle&amp;view=mappings';
			$this->showButton( $link, 'mappings.png', JText::_( 'CJ DATA MAPPINGS' ) );
			$link = 'index.php?option=com_joomdle&amp;view=customprofiletypes';
			$this->showButton( $link, 'profiletypes.png', JText::_( 'CJ CUSTOM PROFILETYPES' ) );
			echo '<div style="clear: both;" />';
			$link = 'index.php?option=com_joomdle&amp;view=shop';
			$this->showButton( $link, 'vmart.png', JText::_( 'CJ SHOP INTEGRATION' ) );
			$link = 'index.php?option=com_joomdle&amp;view=check';
			$this->showButton( $link, 'info.png', JText::_( 'CJ SYSTEM CHECK' ) );

			?>
			</div>
			</td>
			<td width="45%" valign="top">
			<div style="width: 100%">
<?php
			$pane           =& JPane::getInstance('sliders');
                        echo $pane->startPane("content-pane");

			$title = JText::_("CJ ABOUT");
			echo $pane->startPanel( $title, 'joomdle-panel-about' );
			$renderer = 'renderAbout';
			echo $this->$renderer();
			echo $pane->endPanel();
?>

			</div>
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
