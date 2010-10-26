<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
	JToolBarHelper::title( JText::_( 'JCE Administration' ), 'cpanel.png' );
	jceToolbarHelper::help( 'cpanel' );	
	
	JHTML::stylesheet('icons.css', 'administrator/components/com_jce/css/');
	
	$updater =& JCEUpdater::getInstance();	
?>
<table class="admintable">
    <tr>
        <td width="55%" valign="top" colspan="2">
		<div id="cpanel"><?php
			$link = 'index.php?option=com_jce&amp;type=config';
			JCEHelper::quickiconButton( $link, 'icon-48-config.png', JText::_( 'Configuration' ) );
	
			$link = 'index.php?option=com_jce&amp;type=plugin';
			JCEHelper::quickiconButton( $link, 'icon-48-plugin.png', JText::_( 'Plugins' ) );
			
			$link = 'index.php?option=com_jce&amp;type=group';
			JCEHelper::quickiconButton( $link, 'icon-48-user.png', JText::_( 'Groups' ) );
	
			$link = 'index.php?option=com_jce&amp;type=install';
			JCEHelper::quickiconButton( $link, 'icon-48-install.png', JText::_( 'Install' ) );
		?></div>
        <div class="clr"></div>
        </td>
    </tr>
	<tr>
    	<td>
        	<table class="admintable">
            	<tr>
                    <td class="key">
                        <?php echo JText::_( 'Forum' );?>
                    </td>
                    <td>
                        <a href="http://www.joomlacontenteditor.net/forum" target="_new">www.joomlacontenteditor.com/forum</a>
                    </td>
                </tr>
                <tr>
                    <td class="key">
                        <?php echo JText::_( 'Tutorials' );?>
                    </td>
                    <td>
                        <a href="http://www.joomlacontenteditor.net/support/tutorials" target="_new">www.joomlacontenteditor.com/tutorials</a>
                    </td>
                </tr>
                <tr>
                    <td class="key">
                        <?php echo JText::_( 'Documentation' );?>
                    </td>
                    <td>
                        <a href="http://www.joomlacontenteditor.net/support/documentation" target="_new">www.joomlacontenteditor.com/documentation</a>
                    </td>
                </tr>
                <tr>
                    <td class="key">
                        <?php echo JText::_( 'FAQ' );?>
                    </td>
                    <td>
                        <a href="http://www.joomlacontenteditor.net/support/faq" target="_new">www.joomlacontenteditor.com/faq</a>
                    </td>
                </tr>
                <tr>
                    <td class="key">
                        <?php echo JText::_( 'License' );?>
                    </td>
                    <td>GNU/GPL</td>
                </tr>
                 <tr>
                    <td class="key">
                        <?php echo JText::_( 'Component Version' );?>
                    </td>
                    <td>
                        <?php echo $this->com_info['version'];?>
                    </td>
                </tr>
                <tr>
                    <td class="key">
                        <?php echo JText::_( 'Plugin Version' );?>
                    </td>
                    <td>
                        <?php echo $this->plg_info['version'];?>
                    </td>
                </tr>
                <tr>
                    <td class="key">
                        <?php echo JText::_( 'JCE Tables' );?>
                    </td>
                    <td>
                        <ul id="table_status">
						<?php if( $updater->purgeCheck() ){?>
                        	<li class="ok"><?php echo JText::_('OK');?> - 
                        	<a href="index.php?option=com_jce&amp;task=purge" title="<?php echo JText::_('Remove');?>" />[<?php echo JText::_('Remove');?>]</a></li>
                    	<?php }else{
                            if( !$updater->checkTable( 'plugins' ) ){?>
								<li class="error"><?php echo JText::_('DB PLUGINS ERROR');?></li>
							<?php }
							if( !$updater->checkTable( 'groups' ) ){?>
								<li class="error"><?php echo JText::_('DB GROUPS ERROR');?></li>
							<?php }
						}?>
                        </ul>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>