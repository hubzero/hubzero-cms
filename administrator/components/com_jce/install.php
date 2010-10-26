<?php
/**
 * @version		$Id: install.php 115 2009-06-23 11:31:41Z happynoodleboy $
 * @package		JCE Admin Component
 * @copyright	Copyright (C) 2006 - 2009 Ryan Demmmer. All rights reserved.
 * @license		GNU/GPL
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
defined('_JEXEC') or die ('Restricted access');
/**
 * Installer function
 * @return
 */
function com_install()
{
    global $mainframe;
    $db = & JFactory::getDBO();

    jimport('joomla.filesystem.folder');
    jimport('joomla.filesystem.file');

    $path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jce';

    // Remove legacy file
    if (file_exists($path.DS.'admin.jce.php')) {
        @JFile::delete($path.DS.'admin.jce.php');
    }
    // Load updater class
    require_once ($path.DS.'updater.php');

    $updater = & JCEUpdater::getInstance();
    // Install Plugins data
    $updater->installPlugins(true);
    // Install Groups data
    $updater->installGroups(true);
    // Install editor plugin

    jimport('joomla.installer.installer');
    $installer = & JInstaller::getInstance();

    $source 	= $installer->getPath('source');

    $packages 	= $source.DS.'packages';
    // Get editor and plugin packages
	if(is_dir($packages)) {
		$editor 	= JFolder::files($packages, 'plg_jce_15\d+?\.zip', false, true);
   	 	$plugins 	= JFolder::files($packages, 'jce_\w+_15\d+?\.zip', false, true);
	}

    $language = & JFactory::getLanguage();
    $language->load('com_jce', JPATH_ADMINISTRATOR);

    $img_path	 = JURI::root().'/administrator/components/com_jce/img/';
    $out 	 	 = '<table class="adminlist" style="width:50%;">';
    $out 		.= '<tr><th class="title" style="width:65%">'.JText::_('Extension').'</th><th class="title" style="width:30%">'.JText::_('Version').'</th><th class="title" style="width:5%">&nbsp;</th></tr>';

    $editor_img 	= 'delete.png';
    $editor_result 	= JText::_('Error');
    $plugin_out 	= '';

    if (! empty($editor)) {
        if (is_file($editor[0])) {
            $config = & JFactory::getConfig();
            $tmp = $config->getValue('config.tmp_path').DS.uniqid('install_').DS.basename($editor[0], '.zip');

            if (!JArchive::extract($editor[0], $tmp)) {
                $mainframe->enqueueMessage(JText::_('EDITOR EXTRACT ERROR'), 'error');
            } else {
                $query = 'SELECT id, params'
                .' FROM #__components'
                .' WHERE link = '.$db->Quote('option=com_jce')
                ;

                $db->setQuery($query);
                $component = $db->loadObject();

                $params = explode("\n", $component->params);

                $installer = & JInstaller::getInstance();
				
				$c_manifest 	= & $installer->getManifest();
				$c_root 		= & $c_manifest->document;
				$version 		= & $c_root->getElementByPath('version');
				
				$component_version = $version->data();

                // Store Component values
                $component_paths = array (
                	'source'=>$installer->getPath('source'),
                	'manifest'=>$installer->getPath('manifest'),
               		'extension_site'=>$installer->getPath('extension_site'),
                	'extension_administrator'=>$installer->getPath('extension_administrator')
                );

                $component_vars = array (
                	'name'=>$installer->get('name'),
                	'result'=>$installer->get('result'),
                	'message'=>$installer->message
                );
                $editor_version = preg_replace('/\D+/', '', basename($editor[0]));

                // Add JTable include path
                JTable::addIncludePath(JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table');
                // Editor Plugin installed proceed with plugins
                if ($installer->install($tmp)) {
                    $manifest 	= & $installer->getManifest();
                    $root 		= & $manifest->document;
                    $name 		= & $root->getElementByPath('name');
					$version 	= & $root->getElementByPath('version');
					$editor_version = $version->data();

                    $language->load('plg_editors_jce', JPATH_ADMINISTRATOR);

                    $editor_img 	= 'tick.png';
                    $editor_result 	= JText::_('Success');
					
					$query = 'UPDATE #__plugins'
					. ' SET name = '.$db->Quote($name->data())
					. ' WHERE folder = '.$db->Quote('editors')
					. ' AND element = '.$db->Quote('jce')
					;
					$db->setQuery($query);
					$db->query();

                    $params[] = 'package=1';

                    // Include installer class
                    if (! empty($plugins)) {
                        require_once ($path.DS.'installer'.DS.'installer.php');
                        $jce_installer = & JCEInstaller::getInstance();

                        $plugin_out = '<tr><th class="title" style="width:65%">'.JText::_('Plugin').'</th><th class="title" style="width:30%">'.JText::_('Version').'</th><th class="title" style="width:5%">&nbsp;</th></tr>';

                        foreach ($plugins as $plugin) {
                            // Create unique tmp dir name
                            $tmp = $config->getValue('config.tmp_path').DS.uniqid('install_').DS.basename($plugin, '.zip');
                            // Extract to tmp dir
                            if (JArchive::extract($plugin, $tmp)) {
                                // Install plugin
                                if (!$jce_installer->install($tmp)) {
                                    $plugin_img 	= 'delete.png';
                                    $plugin_result 	= JText::_('Error');
                                    $plugin_name 	= basename($plugin);
                                } else {
                                    $manifest 	= & $jce_installer->getManifest();
                                    $root 		= & $manifest->document;
                                    $name 		= & $root->getElementByPath('name');

                                    $language->load('com_jce_'.trim($root->attributes('plugin')));

                                    $plugin_img 	= 'tick.png';
                                    $plugin_result 	= JText::_('Success');
                                    $plugin_name 	= $name->data();
                                }
                                $plugin_out .= '<tr><td>'.$plugin_name.'</td><td>'.preg_replace('/\D+/', '', basename($plugin)).'</td><td style="text-align:center;">'.JHTML::image($img_path.$plugin_img, $plugin_result).'</td></tr>';
                                if ($jce_installer->get('extension.message')) {
                                    $plugin_out .= '<tr><td colspan="3">'.JText::_($jce_installer->get('extension.message')).'</td></tr>';
                                }
                                // Cleanup
                                if (is_dir($tmp)) {
                                    @JFolder::delete($tmp);
                                }
                            }
                        }
                    }
                } else {
                    $editor_img = 'delete.png';
                    $editor_result = JText::_('Error');
                    $params[] = 'package=0';
                }
                $editor_message = JText::_($installer->message);
                // Return Component Paths
                foreach ($component_paths as $k=>$v) {
                    $installer->setPath($k, $v);
                }
                // Return Component Vars
                foreach ($component_vars as $k=>$v) {
                    $installer->set($k, $v);
                }
                if (is_dir($tmp)) {
                    @JFolder::delete($tmp);
                }

                $manifest 	= & $installer->getManifest();
                $root 		= & $manifest->document;
                $version 	= & $root->getElementByPath('version');

                $out .= '<tr><td>'.JText::_('JCE ADMIN TITLE').'</td><td>'.$component_version.'</td><td class="title" style="text-align:center;">'.JHTML::image($img_path.'tick.png', JText::_('Success')).'</td></tr>';
                $out .= '<tr><td colspan="3">'.JText::_($installer->message).'</td></tr>';
                $out .= '<tr><td>'.JText::_('JCE EDITOR TITLE').'</td><td>'.$editor_version.'</td><td class="title" style="text-align:center;">'.JHTML::image($img_path.$editor_img, $editor_result).'</td></tr>';
                $out .= '<tr><td colspan="3">'.$editor_message.'</td></tr>';
                $out .= $plugin_out;
                $out .= '</table>';

                $installer->set('message', JText::_('JCE INSTALL SUMMARY'));
                $installer->set('extension.message', $out);

                $row = & JTable::getInstance('component');
                $row->load($component->id);
                $row->params = implode("\n", $params);
                $row->store();
            }
        }
    }
	if (is_dir($packages)) {
    	// Delete packages folder
    	@JFolder::delete($packages);
	}
}
/**
 * Uninstall function
 * @return
 */
function com_uninstall()
{
    require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jce'.DS.'updater.php');

    $updater = & JCEUpdater::getInstance();
    $updater->cleanupDB();

    $params = & JComponentHelper::getParams('com_jce');
    if ($params->get('package')) {
        $updater->removeEditor();
    }
}
?>
