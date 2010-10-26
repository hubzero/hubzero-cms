<?php 
/**
* @version		$Id: default.php 102 2009-06-21 19:20:52Z happynoodleboy $
* @package		JCE
* @copyright	Copyright (C) 2009 Ryan Demmer. All rights reserved.
* @license		GNU/GPL
* This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
	
JToolBarHelper::title( JText::_( 'JCE Configuration' ), 'config.png' );
JToolBarHelper::save();
JToolBarHelper::apply();
JToolBarHelper::cancel( 'cancel', JText::_( 'Close' ) );
jceToolbarHelper::help('config');
?>
<form action="index.php" method="post" name="adminForm">
    <div class="col width-50">
            <table class="admintable">
                <tr>
                	<td style="vertical-align:top;">
                	<fieldset class="adminform">
                		<legend><?php echo JText::_( 'Setup' ); ?></legend>
                		<?php if($output = $this->params->render('params', 'setup')) :
						echo $output;
						else :
						echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters')."</div>";
						endif;?>
                	</fieldset>
                	</td>
                </tr>
                <tr>
                	<td style="vertical-align:top;">
                    <fieldset class="adminform">
                        <legend><?php echo JText::_( 'Cleanup' ); ?></legend>
                        <?php if($output = $this->params->render('params', 'cleanup')) :
                        	echo $output;
                        else :
                        	echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters')."</div>";
                        endif;?>
                    </fieldset>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align:top;">
                    <fieldset class="adminform">
                        <legend><?php echo JText::_( 'Formatting' ); ?></legend>
                        <?php if($output = $this->params->render('params', 'format')) :
                        	echo $output;
                        else :
                        	echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters ')."</div>";
                        endif;?>
                    </fieldset>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align:top;">
                    <fieldset class="adminform">
                        <legend><?php echo JText::_( 'Advanced' ); ?></legend>
                        <?php if($output = $this->params->render('params', 'advanced')) :
                        	echo $output;
                        else :
                        	echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters ')."</div>";
                        endif;?>
                    </fieldset>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align:top;">
                    <fieldset class="adminform">
                        <legend><?php echo JText::_( 'Miscellaneous' ); ?></legend>
                        <?php if($output = $this->params->render('params', 'other')) :
                       		echo $output;
                        else :
                        	echo "<div  style=\"text-align: center; padding: 5px; \">".JText::_('No Parameters ')."</div>";
                        endif;?>
                    </fieldset>
                    </td>
                </tr>
            </table>
    </div>
    <input type="hidden" name="option" value="com_jce" />
    <input type="hidden" name="client" value="<?php echo $this->client; ?>" />
    <input type="hidden" name="type" value="config" />
    <input type="hidden" name="task" value="" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>