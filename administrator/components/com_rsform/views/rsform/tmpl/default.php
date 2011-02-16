<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
?>
<form action="index.php" method="post" name="adminForm">
    <table width="100%">
   		<tr>
      		<td width="50%" valign="top">
				<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
				<td valign="top">
					<table class="adminlist">
						<tr>
							<td>
								<div id="cpanel">
								<h3><?php echo JText::_('RSFP_MANAGE_FORMS'); ?></h3>
								<div style="float: left">
									<div class="icon hasTip" title="<?php echo JText::_('RSFP_MANAGE_FORMS'); ?>">
										<a href="index.php?option=com_rsform&amp;task=forms.manage">
											<?php echo JHTML::_('image', 'administrator/components/com_rsform/assets/images/forms.png', JText::_('RSFP_MANAGE_FORMS')); ?>
											<span><?php echo JText::_('RSFP_MANAGE_FORMS'); ?></span>
										</a>
									</div>
								</div>
								<div style="float: left">
									<div class="icon hasTip" title="<?php echo JText::_('RSFP_MANAGE_SUBMISSIONS'); ?>">
										<a href="index.php?option=com_rsform&amp;task=submissions.manage">
											<?php echo JHTML::_('image', 'administrator/components/com_rsform/assets/images/viewdata.png', JText::_('RSFP_MANAGE_SUBMISSIONS')); ?>
											<span><?php echo JText::_('RSFP_MANAGE_SUBMISSIONS'); ?></span>
										</a>
									</div>
								</div>
								<div style="float: left">
									<div class="icon hasTip" title="<?php echo JText::_('RSFP_BACKUP_RESTORE'); ?>">
										<a href="index.php?option=com_rsform&amp;task=backup.restore">
											<?php echo JHTML::_('image', 'administrator/components/com_rsform/assets/images/backup.png', JText::_('RSFP_BACKUP_RESTORE')); ?>
											<span><?php echo JText::_('RSFP_BACKUP_RESTORE'); ?></span>
										</a>
									</div>
								</div>
								<span class="clr"></span>
								<h3><?php echo JText::_('RSFP_CONFIGURATION'); ?></h3>
								<div style="float: left">
									<div class="icon hasTip" title="<?php echo JText::_('RSFP_CONFIGURATION'); ?>">
										<a href="index.php?option=com_rsform&amp;task=configuration.edit">
											<?php echo JHTML::_('image', 'administrator/components/com_rsform/assets/images/config.png', JText::_('RSFP_CONFIGURATION')); ?>
											<span><?php echo JText::_('RSFP_CONFIGURATION'); ?></span>
										</a>
									</div>
								</div>
								<div style="float: left">
									<div class="icon hasTip" title="<?php echo JText::_('RSFP_UPDATES'); ?>">
										<a href="index.php?option=com_rsform&amp;task=updates.manage">
											<?php echo JHTML::_('image', 'administrator/components/com_rsform/assets/images/restore.png', JText::_('RSFP_UPDATES')); ?>
											<span><?php echo JText::_('RSFP_UPDATES'); ?></span>
										</a>
									</div>
								</div>
								<div style="float: left">
									<div class="icon hasTip" title="<?php echo JText::_('RSFP_SUPPORT'); ?>">
										<a href="index.php?option=com_rsform&amp;task=goto.support">
											<?php echo JHTML::_('image', 'administrator/components/com_rsform/assets/images/support.png', JText::_('RSFP_SUPPORT')); ?>
											<span><?php echo JText::_('RSFP_SUPPORT'); ?></span>
										</a>
									</div>
								</div>
								<div style="float: left">
									<div class="icon hasTip" title="<?php echo JText::_('RSFP_PLUGINS'); ?>">
										<a href="index.php?option=com_rsform&amp;task=goto.plugins">
											<?php echo JHTML::_('image', 'administrator/components/com_rsform/assets/images/samples.png', JText::_('RSFP_PLUGINS')); ?>
											<span><?php echo JText::_('RSFP_PLUGINS'); ?></span>
										</a>
									</div>
								</div>
								</div>
							</td>
						</tr>
					</table>
				</td>
				</tr>
				</table>
			</td>
		    <td width="50%" valign="top" align="center">
			    <table border="1" width="100%" class="thisform">
					<tr class="thisform">
			            <th class="cpanel" colspan="2"><?php echo _RSFORM_PRODUCT; ?>  <?php echo _RSFORM_VERSION; ?> rev <?php echo _RSFORM_REVISION; ?></th>
			         </tr>
			         <tr class="thisform"><td bgcolor="#FFFFFF" colspan="2"><br />
			      <div style="width:100%" align="center">
			      <img src="components/com_rsform/assets/images/rsform-pro.jpg" align="middle" alt="RSForm! Pro logo"/>
			      <br /><br /></div>
			      </td></tr>
			         <tr class="thisform">
			            <td width="120" bgcolor="#FFFFFF"><?php echo JText::_('VERSION');?></td>
			            <td bgcolor="#FFFFFF"><?php echo _RSFORM_VERSION;?></td>
			         </tr>
			         <tr class="thisform">
			            <td bgcolor="#FFFFFF"><?php echo JText::_('COPYRIGHT'); ?></td>
			            <td bgcolor="#FFFFFF"><?php echo _RSFORM_COPYRIGHT;?></td>
			         </tr>
			         <tr class="thisform">
			            <td bgcolor="#FFFFFF"><?php echo JText::_('LICENSE'); ?></td>
			            <td bgcolor="#FFFFFF"><?php echo _RSFORM_LICENSE;?></td>
			         </tr>
			         <tr class="thisform">
			            <td valign="top" bgcolor="#FFFFFF"><?php echo JText::_('AUTHOR');?></td>
			            <td bgcolor="#FFFFFF">
			            <?php echo _RSFORM_AUTHOR;?>
						</td>
			         </tr>
			         <tr class="<?php echo $this->code == '' ? 'thisformError' : 'thisformOk'; ?>">
						<td valign="top">
							<?php echo JText::_('RSFP_CODE_DESC');?>
						</td>
						<td>
							<?php if ($this->code=='') { ?>
								<input type="text" name="code" value="" />
							<?php } else { ?>
								<?php echo $this->code; ?>
							<?php } ?>
						</td>
			         </tr>
			         <tr class="<?php echo $this->code == '' ? 'thisformError' : 'thisformOk'; ?>">
						<td valign="top">&nbsp;</td>
						<td>
							<?php if ($this->code!='') { ?>
								<input type="submit" name="modify_register" value="<?php echo JText::_('RSFP_MODIFY_CODE');?>" /><br/>
							<?php } else { ?>
								<input type="button" name="register" value="<?php echo JText::_('RSFP_UPDATE_CODE');?>" onclick="submitbutton('saveRegistration');"/>
							<?php } ?>
						</td>
			         </tr>
			      </table>
				  <p align="center"><a href="http://www.rsjoomla.com/joomla-components/joomla-security.html" target="_blank"><img src="components/com_rsform/assets/images/rsfirewall-approved.gif" align="middle" alt="RSFirewall! Approved"/></a></p>
		      </td>
		   </tr>
		</table>
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="configuration.edit" />
</form>