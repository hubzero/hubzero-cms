<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'helpers'.DS.'rsform.php');

// Initialize DB
$db = JFactory::getDBO();

// Sample Data
$db->setQuery("SELECT COUNT(FormId) FROM #__rsform_forms");
if (!$db->loadResult())
{
	$buffer = file_get_contents($this->parent->getPath('source').DS.'admin'.DS.'sample.rsform.sql');
	jimport('joomla.installer.helper');
	$queries = JInstallerHelper::splitSql($buffer);
	// Process each query in the $queries array (split out of sql file).
	foreach ($queries as $query)
	{
		$query = trim($query);
		if ($query != '' && $query{0} != '#') {
			$db->setQuery($query);
			if (!$db->query()) {
				JError::raiseWarning(1, 'JInstaller::install: '.JText::_('SQL Error')." ".$db->stderr(true));
			}
		}
	}
}

if (!RSFormProHelper::isJ16())
{
	// Get a new installer
	$plg_installer = new JInstaller();

	// Content - RSForm! Pro
	$plg_content_exists = false;
	$plg_content 	    = false;
	$plg_content_msg    = '';
	$published 		    = 1;

	// Search for the old mosrsform Plugin
	$db->setQuery("SELECT * FROM #__plugins WHERE folder='content' AND element='mosrsform'");
	$plugin = $db->loadObject();
	// Found it
	if (!empty($plugin))
	{
		$plugins = true;
		$plg_content_exists = true;
		
		$name = $plugin->name;
		if (strpos($name, ' (no longer supported, disabled)') === false)
			$name .= ' (no longer supported, disabled)';
		
		// Unpublish old plugin
		$db->setQuery("UPDATE #__plugins SET published=0, name='".$db->getEscaped($name)."' WHERE id='".$plugin->id."'");
		$db->query();
		$published = $plugin->published;
		
		$plg_content_msg .= 'The installer found the old &quot;mosrsform&quot; plugin. This plugin is no longer supported by this version of RSForm! Pro and has been disabled. You should uninstall it as soon as possible.<br />';
		
		// Install
		if ($plg_installer->install($this->parent->getPath('source').DS.'plugins'.DS.'plg_content'))
		{
			$db->setQuery("UPDATE #__plugins SET published='".$published."' WHERE `element`='rsform' AND `folder`='content'");
			$db->query();
			
			$plg_content = true;
		}
	}

	// System - RSForm! Pro Plugin
	$plg_rsform 	   = false;
	$plg_rsform_exists = false;
	$plg_rsform_msg    = '';
	$published 		   = 1;

	// Search for the old plugin
	$db->setQuery("SELECT * FROM #__plugins WHERE folder='system' AND element='rsform'");
	$plugin = $db->loadObject();
	// Found it
	if (!empty($plugin))
	{
		$plugins = true;
		$plg_rsform_exists = true;
		$published = $plugin->published;
		
		// Install
		if ($plg_installer->install($this->parent->getPath('source').DS.'plugins'.DS.'plg_rsform'))
		{
			$db->setQuery("UPDATE #__plugins SET published='".$published."' WHERE `element`='rsform' AND `folder`='system'");
			$db->query();
			
			$plg_rsform = true;
			$plg_rsform_msg .= 'The installer found the old plugin and updated it.';
		}
		else
		{
			$name = $plugin->name;
			if (strpos($name, ' (no longer supported, disabled)') === false)
				$name .= ' (no longer supported, disabled)';
				
			$db->setQuery("UPDATE #__plugins SET published='0', name='".$db->getEscaped($name)."' WHERE `element`='rsform' AND `folder`='system'");
			$db->query();
			
			$plg_rsform_msg .= 'The installer could not update the old plugin. Since the plugin is no longer supported by this version of RSForm! Pro, it has been disabled. You should check if /plugins/system is writable and try to update it again.';
		}
	}

	// System - RSForm! Pro - Google Analytics Plugin
	$plg_google 	   = false;
	$plg_google_exists = false;
	$plg_google_msg    = '';
	$published 		   = 1;

	// Search for the old plugin
	$db->setQuery("SELECT * FROM #__plugins WHERE folder='system' AND element='rsfpgoogle'");
	$plugin = $db->loadObject();
	// Found it
	if (!empty($plugin))
	{
		$plugins = true;
		$plg_google_exists = true;
		$published = $plugin->published;
		
		// Install
		if ($plg_installer->install($this->parent->getPath('source').DS.'plugins'.DS.'plg_google'))
		{
			$db->setQuery("UPDATE #__plugins SET published='".$published."' WHERE `element`='rsfpgoogle' AND `folder`='system'");
			$db->query();
			
			$plg_google = true;
			$plg_google_msg .= 'The installer found the old plugin and updated it.';
		}
		else
		{
			$name = $plugin->name;
			if (strpos($name, ' (no longer supported, disabled)') === false)
				$name .= ' (no longer supported, disabled)';
			
			$db->setQuery("UPDATE #__plugins SET published='0', name='".$db->getEscaped($name)."' WHERE `element`='rsfpgoogle' AND `folder`='system'");
			$db->query();
			
			$plg_google_msg .= 'The installer could not update the old plugin. Since the plugin is no longer supported by this version of RSForm! Pro, it has been disabled. You should check if /plugins/system is writable and try to update it again.';
		}
	}

	// System - RSForm! Pro - Mappings Plugin
	$plg_mappings 	     = false;
	$plg_mappings_exists = false;
	$plg_mappings_msg    = '';
	$published 		     = 1;

	// Search for the old plugin
	$db->setQuery("SELECT * FROM #__plugins WHERE folder='system' AND element='rsfpmappings'");
	$plugin = $db->loadObject();
	// Found it
	if (!empty($plugin))
	{
		$plugins = true;
		$plg_mappings_exists = true;
		$published = $plugin->published;
		
		// Install
		if ($plg_installer->install($this->parent->getPath('source').DS.'plugins'.DS.'plg_mappings'))
		{
			$db->setQuery("UPDATE #__plugins SET published='".$published."' WHERE `element`='rsfpmappings' AND `folder`='system'");
			$db->query();
			
			$plg_mappings = true;
			$plg_mappings_msg .= 'The installer found the old plugin and updated it.';
		}
		else
		{
			$name = $plugin->name;
			if (strpos($name, ' (no longer supported, disabled)') === false)
				$name .= ' (no longer supported, disabled)';
				
			$db->setQuery("UPDATE #__plugins SET published='0', name='".$db->getEscaped($name)."' WHERE `element`='rsfpmappings' AND `folder`='system'");
			$db->query();
			
			$plg_mappings_msg .= 'The installer could not update the old plugin. Since the plugin is no longer supported by this version of RSForm! Pro, it has been disabled. You should check if /plugins/system is writable and try to update it again.';
		}
	}

	// System - RSForm! Pro - Migration Plugin
	$plg_migration 	      = false;
	$plg_migration_exists = false;
	$plg_migration_msg    = '';
	$published 		      = 1;

	// Search for the old plugin
	$db->setQuery("SELECT * FROM #__plugins WHERE folder='system' AND element='rsfpmigration'");
	$plugin = $db->loadObject();
	// Found it
	if (!empty($plugin))
	{
		$plugins = true;
		$plg_migration_exists = true;
		$published = $plugin->published;
		
		// Install
		if ($plg_installer->install($this->parent->getPath('source').DS.'plugins'.DS.'plg_migration'))
		{
			$db->setQuery("UPDATE #__plugins SET published='".$published."' WHERE `element`='rsfpmigration' AND `folder`='system'");
			$db->query();
			
			$plg_migration = true;
			$plg_migration_msg .= 'The installer found the old plugin and updated it.';
		}
		else
		{
			$name = $plugin->name;
			if (strpos($name, ' (no longer supported, disabled)') === false)
				$name .= ' (no longer supported, disabled)';
			
			$db->setQuery("UPDATE #__plugins SET published='0', name='".$db->getEscaped($name)."' WHERE `element`='rsfpmigration' AND `folder`='system'");
			$db->query();
			
			$plg_migration_msg .= 'The installer could not update the old plugin. Since the plugin is no longer supported by this version of RSForm! Pro, it has been disabled. You should check if /plugins/system is writable and try to update it again.';
		}
	}

	// System - RSForm! Pro - PayPal Plugin
	$plg_paypal 	   = false;
	$plg_paypal_exists = false;
	$plg_paypal_msg    = '';
	$published 		   = 1;

	// Search for the old plugin
	$db->setQuery("SELECT * FROM #__plugins WHERE folder='system' AND element='rsfppaypal'");
	$plugin = $db->loadObject();
	// Found it
	if (!empty($plugin))
	{
		$plugins = true;
		$plg_paypal_exists = true;
		$published = $plugin->published;
		
		// Install
		if ($plg_installer->install($this->parent->getPath('source').DS.'plugins'.DS.'plg_paypal'))
		{
			$db->setQuery("UPDATE #__plugins SET published='".$published."' WHERE `element`='rsfppaypal' AND `folder`='system'");
			$db->query();
			
			$plg_paypal = true;
			$plg_paypal_msg .= 'The installer found the old plugin and updated it.';
		}
		else
		{
			$name = $plugin->name;
			if (strpos($name, ' (no longer supported, disabled)') === false)
				$name .= ' (no longer supported, disabled)';
			
			$db->setQuery("UPDATE #__plugins SET published='0', name='".$db->getEscaped($name)."' WHERE `element`='rsfppaypal' AND `folder`='system'");
			$db->query();
			
			$plg_paypal_msg .= 'The installer could not update the old plugin. Since the plugin is no longer supported by this version of RSForm! Pro, it has been disabled. You should check if /plugins/system is writable and try to update it again.';
		}
	}

	// System - RSForm! Pro - reCAPTCHA Plugin
	$plg_recaptcha 	   = false;
	$plg_recaptcha_exists = false;
	$plg_recaptcha_msg    = '';
	$published 		   = 1;

	// Search for the old plugin
	$db->setQuery("SELECT * FROM #__plugins WHERE folder='system' AND element='rsfprecaptcha'");
	$plugin = $db->loadObject();
	// Found it
	if (!empty($plugin))
	{
		$plugins = true;
		$plg_recaptcha_exists = true;
		$published = $plugin->published;
		
		// Install
		if ($plg_installer->install($this->parent->getPath('source').DS.'plugins'.DS.'plg_recaptcha'))
		{
			$db->setQuery("UPDATE #__plugins SET published='".$published."' WHERE `element`='rsfprecaptcha' AND `folder`='system'");
			$db->query();
			
			$plg_recaptcha = true;
			$plg_recaptcha_msg .= 'The installer found the old plugin and updated it.';
		}
		else
		{
			$name = $plugin->name;
			if (strpos($name, ' (no longer supported, disabled)') === false)
				$name .= ' (no longer supported, disabled)';
			
			$db->setQuery("UPDATE #__plugins SET published='0', name='".$db->getEscaped($name)."' WHERE `element`='rsfprecaptcha' AND `folder`='system'");
			$db->query();
			
			$plg_recaptcha_msg .= 'The installer could not update the old plugin. Since the plugin is no longer supported by this version of RSForm! Pro, it has been disabled. You should check if /plugins/system is writable and try to update it again.';
		}
	}

	// System - RSForm! Pro - RSEvents! Plugin
	$plg_rsevents 	   = false;
	$plg_rsevents_exists = false;
	$plg_rsevents_msg    = '';
	$published 		   = 1;

	// Search for the old plugin
	$db->setQuery("SELECT * FROM #__plugins WHERE folder='system' AND element='rsfprsevents'");
	$plugin = $db->loadObject();
	// Found it
	if (!empty($plugin))
	{
		$plugins = true;
		$plg_rsevents_exists = true;
		$published = $plugin->published;
		
		// Install
		if ($plg_installer->install($this->parent->getPath('source').DS.'plugins'.DS.'plg_rsevents'))
		{
			$db->setQuery("UPDATE #__plugins SET published='".$published."' WHERE `element`='rsfprsevents' AND `folder`='system'");
			$db->query();
			
			$plg_rsevents = true;
			$plg_rsevents_msg .= 'The installer found the old plugin and updated it.';
		}
		else
		{
			$name = $plugin->name;
			if (strpos($name, ' (no longer supported, disabled)') === false)
				$name .= ' (no longer supported, disabled)';
				
			$db->setQuery("UPDATE #__plugins SET published='0', name='".$db->getEscaped($name)."' WHERE `element`='rsfprsevents' AND `folder`='system'");
			$db->query();
			
			$plg_rsevents_msg .= 'The installer could not update the old plugin. Since the plugin is no longer supported by this version of RSForm! Pro, it has been disabled. You should check if /plugins/system is writable and try to update it again.';
		}
	}

	// System - RSForm! Pro - RSMail! Plugin
	$plg_rsmail 	   = false;
	$plg_rsmail_exists = false;
	$plg_rsmail_msg    = '';
	$published 		   = 1;

	// Search for the old plugin
	$db->setQuery("SELECT * FROM #__plugins WHERE folder='system' AND element='rsmail_rsformpro_subscription'");
	$plugin = $db->loadObject();
	// Found it
	if (!empty($plugin))
	{
		$plugins = true;
		$plg_rsmail_exists = true;
		$published = $plugin->published;
		
		// Install
		if ($plg_installer->install($this->parent->getPath('source').DS.'plugins'.DS.'plg_rsmail'))
		{
			$db->setQuery("UPDATE #__plugins SET published='".$published."' WHERE `element`='rsmail_rsformpro_subscription' AND `folder`='system'");
			$db->query();
			
			$plg_rsmail = true;
			$plg_rsmail_msg .= 'The installer found the old plugin and updated it.';
		}
		else
		{
			$name = $plugin->name;
			if (strpos($name, ' (no longer supported, disabled)') === false)
				$name .= ' (no longer supported, disabled)';
			
			$db->setQuery("UPDATE #__plugins SET published='0', name='".$db->getEscaped($name)."' WHERE `element`='rsmail_rsformpro_subscription' AND `folder`='system'");
			$db->query();
			
			$plg_rsmail_msg .= 'The installer could not update the old plugin. Since the plugin is no longer supported by this version of RSForm! Pro, it has been disabled. You should check if /plugins/system is writable and try to update it again.';
		}
	}

	// Module - RSForm! Pro
	$mod_rsform 	   = false;
	$mod_rsform_exists = false;
	$mod_rsform_msg    = '';
	$published 		   = 1;

	// Search for the old module
	$db->setQuery("SELECT * FROM #__modules WHERE module='mod_rsform'");
	$module = $db->loadObject();
	// Found it
	if (!empty($module))
	{
		$modules = true;
		$mod_rsform_exists = true;
		$published = $module->published;
		
		// Install
		if ($plg_installer->install($this->parent->getPath('source').DS.'modules'.DS.'mod_rsform'))
		{
			$db->setQuery("UPDATE #__modules SET published='".$published."' WHERE `module`='mod_rsform'");
			$db->query();
			
			$mod_rsform = true;
			$mod_rsform_msg .= 'The installer found the old module and updated it.';
		}
		else
		{
			$name = $module->name;
			if (strpos($name, ' (no longer supported, disabled)') === false)
				$name .= ' (no longer supported, disabled)';
			
			$db->setQuery("UPDATE #__modules SET published='0', name='".$db->getEscaped($name)."' WHERE `module`='mod_rsform'");
			$db->query();
			
			$mod_rsform_msg .= 'The installer could not update the old module. Since the module is no longer supported by this version of RSForm! Pro, it has been disabled. You should check if /modules is writable and try to update it again.';
		}
	}

	// Module - RSForm! Pro
	$mod_rsform_list 	    = false;
	$mod_rsform_list_exists = false;
	$mod_rsform_list_msg    = '';
	$published 		   		= 1;

	// Search for the old module
	$db->setQuery("SELECT * FROM #__modules WHERE module='mod_rsform_list'");
	$module = $db->loadObject();
	// Found it
	if (!empty($module))
	{
		$modules = true;
		$mod_rsform_list_exists = true;
		$published = $module->published;
		
		// Install
		if ($plg_installer->install($this->parent->getPath('source').DS.'modules'.DS.'mod_rsform_list'))
		{
			$db->setQuery("UPDATE #__modules SET published='".$published."' WHERE `module`='mod_rsform_list'");
			$db->query();
			
			$mod_rsform_list = true;
			$mod_rsform_list_msg .= 'The installer found the old module and updated it.';
		}
		else
		{
			$name = $module->name;
			if (strpos($name, ' (no longer supported, disabled)') === false)
				$name .= ' (no longer supported, disabled)';
			
			$db->setQuery("UPDATE #__modules SET published='0', name='".$db->getEscaped($name)."' WHERE `module`='mod_rsform_list'");
			$db->query();
			
			$mod_rsform_list_msg .= 'The installer could not update the old module. Since the module is no longer supported by this version of RSForm! Pro, it has been disabled. You should check if /modules is writable and try to update it again.';
		}
	}
}

if (RSFormProHelper::isJ16())
{
	$library_installer = new JInstaller();
	$library_installer->install($this->parent->getPath('source').DS.'libraries');
}
?>
<style type="text/css">
.green { color: #009E28; }
.red { color: #B8002E; }
.greenbg { background: #B8FFC9 !important; }
.redbg { background: #FFB8C9 !important; }

#rsform_changelog
{
	list-style-type: none;
	padding: 0;
}

#rsform_changelog li
{
	background: url(components/com_rsform/assets/images/legacy/tick.png) no-repeat center left;
	padding-left: 24px;
}

#rsform_links
{
	list-style-type: none;
	padding: 0;
}
</style>

<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
			<th width="30%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'RSForm! Pro '.JText::_('Component'); ?></td>
			<td><strong class="green"><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
		<?php if (!empty($plugins)) { ?>
		<tr>
			<th><?php echo JText::_('Plugin'); ?></th>
			<th><?php echo JText::_('Group'); ?></th>
			<th></th>
		</tr>
		<?php if ($plg_content_exists) { ?>
		<tr class="row0">
			<td class="key">Content - RSForm! Pro Plugin</td>
			<td class="key">content</td>
			<td>
			<?php if ($plg_content) { ?>
			<strong class="green"><?php echo JText::_('Installed'); ?></strong>
			<?php } else { ?>
			<strong class="red"><?php echo JText::_('Not Installed'); ?></strong>
			<?php } ?>
			<?php echo $plg_content_msg; ?>
			</td>
		</tr>
		<?php } ?>
		<?php if ($plg_rsform_exists) { ?>
		<tr class="row1">
			<td class="key">System - RSForm! Pro Plugin</td>
			<td class="key">system</td>
			<td>
			<?php if ($plg_rsform) { ?>
			<strong class="green"><?php echo JText::_('Installed'); ?></strong>
			<?php } else { ?>
			<strong class="red"><?php echo JText::_('Not Installed'); ?></strong>
			<?php } ?>
			<?php echo $plg_rsform_msg; ?>
			</td>
		</tr>
		<?php } ?>
		<?php if ($plg_google_exists) { ?>
		<tr class="row0">
			<td class="key">System - RSForm! Pro - Google Analytics</td>
			<td class="key">system</td>
			<td>
			<?php if ($plg_google) { ?>
			<strong class="green"><?php echo JText::_('Installed'); ?></strong>
			<?php } else { ?>
			<strong class="red"><?php echo JText::_('Not Installed'); ?></strong>
			<?php } ?>
			<?php echo $plg_google_msg; ?>
			</td>
		</tr>
		<?php } ?>
		<?php if ($plg_mappings_exists) { ?>
		<tr class="row1">
			<td class="key">System - RSForm! Pro - Mappings</td>
			<td class="key">system</td>
			<td>
			<?php if ($plg_mappings) { ?>
			<strong class="green"><?php echo JText::_('Installed'); ?></strong>
			<?php } else { ?>
			<strong class="red"><?php echo JText::_('Not Installed'); ?></strong>
			<?php } ?>
			<?php echo $plg_mappings_msg; ?>
			</td>
		</tr>
		<?php } ?>
		<?php if ($plg_migration_exists) { ?>
		<tr class="row0">
			<td class="key">System - RSForm! Pro - Migration</td>
			<td class="key">system</td>
			<td>
			<?php if ($plg_migration) { ?>
			<strong class="green"><?php echo JText::_('Installed'); ?></strong>
			<?php } else { ?>
			<strong class="red"><?php echo JText::_('Not Installed'); ?></strong>
			<?php } ?>
			<?php echo $plg_migration_msg; ?>
			</td>
		</tr>
		<?php } ?>
		<?php if ($plg_paypal_exists) { ?>
		<tr class="row1">
			<td class="key">System - RSForm! Pro - PayPal</td>
			<td class="key">system</td>
			<td>
			<?php if ($plg_paypal) { ?>
			<strong class="green"><?php echo JText::_('Installed'); ?></strong>
			<?php } else { ?>
			<strong class="red"><?php echo JText::_('Not Installed'); ?></strong>
			<?php } ?>
			<?php echo $plg_paypal_msg; ?>
			</td>
		</tr>
		<?php } ?>
		<?php if ($plg_recaptcha_exists) { ?>
		<tr class="row0">
			<td class="key">System - RSForm! Pro - reCAPTCHA</td>
			<td class="key">system</td>
			<td>
			<?php if ($plg_recaptcha) { ?>
			<strong class="green"><?php echo JText::_('Installed'); ?></strong>
			<?php } else { ?>
			<strong class="red"><?php echo JText::_('Not Installed'); ?></strong>
			<?php } ?>
			<?php echo $plg_recaptcha_msg; ?>
			</td>
		</tr>
		<?php } ?>
		<?php if ($plg_rsevents_exists) { ?>
		<tr class="row1">
			<td class="key">System - RSForm! Pro - RSEvents!</td>
			<td class="key">system</td>
			<td>
			<?php if ($plg_rsevents) { ?>
			<strong class="green"><?php echo JText::_('Installed'); ?></strong>
			<?php } else { ?>
			<strong class="red"><?php echo JText::_('Not Installed'); ?></strong>
			<?php } ?>
			<?php echo $plg_rsevents_msg; ?>
			</td>
		</tr>
		<?php } ?>
		<?php if ($plg_rsmail_exists) { ?>
		<tr class="row0">
			<td class="key">System - RSForm! Pro - RSMail!</td>
			<td class="key">system</td>
			<td>
			<?php if ($plg_rsmail) { ?>
			<strong class="green"><?php echo JText::_('Installed'); ?></strong>
			<?php } else { ?>
			<strong class="red"><?php echo JText::_('Not Installed'); ?></strong>
			<?php } ?>
			<?php echo $plg_rsmail_msg; ?>
			</td>
		</tr>
		<?php } ?>
		<?php } ?>
		<?php if (!empty($modules)) { ?>
		<tr>
			<th colspan="3"><?php echo JText::_('Module'); ?></th>
		</tr>
		<?php if ($mod_rsform_exists) { ?>
		<tr class="row0">
			<td class="key" colspan="2">Module - RSForm! Pro</td>
			<td>
			<?php if ($mod_rsform) { ?>
			<strong class="green"><?php echo JText::_('Installed'); ?></strong>
			<?php } else { ?>
			<strong class="red"><?php echo JText::_('Not Installed'); ?></strong>
			<?php } ?>
			<?php echo $mod_rsform_msg; ?>
			</td>
		</tr>
		<?php } ?>
		<?php if ($mod_rsform_list_exists) { ?>
		<tr class="row1">
			<td class="key" colspan="2">Module - RSForm! Pro Frontend List</td>
			<td>
			<?php if ($mod_rsform_list) { ?>
			<strong class="green"><?php echo JText::_('Installed'); ?></strong>
			<?php } else { ?>
			<strong class="red"><?php echo JText::_('Not Installed'); ?></strong>
			<?php } ?>
			<?php echo $mod_rsform_list_msg; ?>
			</td>
		</tr>
		<?php } ?>
		<?php } ?>
	</tbody>
</table>
<br/>
<?php
$your_php = phpversion();
$correct_php = version_compare($your_php, '4.0');

$db->setQuery("SELECT VERSION()");
$your_sql = $db->loadResult();
$correct_sql = version_compare($your_sql, '4.2');
?>
<table class="adminlist">
	<thead>
		<tr>
			<th width="30%" nowrap="nowrap"><?php echo JText::_('Software'); ?></th>
			<th width="30%" nowrap="nowrap"><?php echo JText::_('Your Version'); ?></th>
			<th width="30%" nowrap="nowrap"><?php echo JText::_('Minimum'); ?></th>
			<th width="30%" nowrap="nowrap"><?php echo JText::_('Recommended'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="4"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key">PHP</td>
			<td class="<?php echo $correct_php >= 0 ? 'greenbg' : 'redbg'; ?>"><strong class="<?php echo $correct_php >= 0 ? 'green' : 'red'; ?>"><?php echo $your_php; ?></strong> <img src="images/<?php echo $correct_php >= 0 ? 'tick' : 'publish_x'; ?>.png" alt="" /></td>
			<td><strong>4.x</strong></td>
			<td><strong>5.x</strong></td>
		</tr>
		<tr class="row1">
			<td class="key">MySQL</td>
			<td class="<?php echo $correct_sql >= 0 ? 'greenbg' : 'redbg'; ?>"><strong class="<?php echo $correct_sql >= 0 ? 'green' : 'red'; ?>"><?php echo $your_sql; ?></strong> <img src="images/<?php echo $correct_sql >= 0 ? 'tick' : 'publish_x'; ?>.png" alt="" /></td>
			<td><strong>4.2</strong></td>
			<td><strong>5.x</strong></td>
		</tr>
	</tbody>
</table>
<table>
	<tr>
		<td width="1%"><img src="components/com_rsform/assets/images/box.png" alt="RSForm! Pro Box" /></td>
		<td align="left">
		<div id="rsform_message">
		<p>Thank you for choosing RSForm! Pro.</p>
		<p>New in this version:</p>
		<ul id="rsform_changelog">
			<li><img src="components/com_rsform/assets/images/native16.png" alt="1.6 Native" /> Joomla! 1.6 Compatibility</li>
			<li>A new &quot;Submissions&quot; view you can add to your menu</li>
			<li>Salesforce custom fields</li>
			<li>Themes support</li>
			<li>Pages validate through AJAX</li>
		</ul>
		<a href="http://www.rsjoomla.com/customer-support/documentations/22-general-overview-of-the-component/23-rsformpro-changelog.html" target="_blank">Full Changelog</a>
		<ul id="rsform_links">
			<li>
				<div class="button2-left">
					<div class="next">
						<a href="index.php?option=com_rsform">Start using RSForm! Pro</a>
					</div>
				</div>
			</li>
			<li>
				<div class="button2-left">
					<div class="readmore">
						<a href="index.php?option=com_rsform&amp;task=goto.support" target="_blank">Read the RSForm! Pro User Guide</a>
					</div>
				</div>
			</li>
			<li>
				<div class="button2-left">
					<div class="blank">
						<a href="http://www.rsjoomla.com/customer-support/tickets.html" target="_blank">Get Support!</a>
					</div>
				</div>
			</li>
		</ul>
		</div>
		</td>
	</tr>
	
</table><br/>

<?php
$jconfig = JFactory::getConfig();
$dbprefix = $jconfig->getValue('config.dbprefix');

// Disable error reporting
$db->setQuery("REPLACE INTO `#__rsform_config` (`ConfigId`, `SettingName`, `SettingValue`) VALUES(2, 'global.debug.mode', '0')");
$db->query();

$db->setQuery("UPDATE `#__rsform_component_type_fields` SET `FieldType` = 'textarea' WHERE `ComponentTypeFieldId` = 10 LIMIT 1");
$db->query();

$db->setQuery("SHOW TABLES");
$tables = $db->loadResultArray();
if (in_array($dbprefix.'RSFORM_CONFIG', $tables))
{
	$wrong_tables = array('#__RSFORM_COMPONENTS', '#__RSFORM_COMPONENT_TYPES', '#__RSFORM_COMPONENT_TYPE_FIELDS', '#__RSFORM_CONFIG', '#__RSFORM_FORMS', '#__RSFORM_MAPPINGS', '#__RSFORM_PROPERTIES', '#__RSFORM_SUBMISSIONS',	'#__RSFORM_SUBMISSION_VALUES');
	$good_tables = array('#__rsform_components', '#__rsform_component_types', '#__rsform_component_type_fields', '#__rsform_config', '#__rsform_forms', '#__rsform_mappings', '#__rsform_properties', '#__rsform_submissions', '#__rsform_submission_values');
	foreach ($wrong_tables as $i => $wrong_table)
	{
		$db->setQuery("RENAME TABLE `".$wrong_tables[$i]."` TO `".$good_tables[$i]."`");
		$db->query();
	}
	// Replace uppercase tables if there are any scripts
	foreach ($wrong_tables as $i => $wrong_table)
	{
		$db->setQuery("UPDATE `".$good_tables[4]."` SET `ScriptProcess`=REPLACE(`ScriptProcess`,'".$wrong_tables[$i]."','".$good_tables[$i]."'), `ScriptDisplay`=REPLACE(`ScriptDisplay`,'".$wrong_tables[$i]."','".$good_tables[$i]."')");
		$db->query();
		$db->setQuery("UPDATE `".$good_tables[6]."` SET `PropertyValue`=REPLACE(`PropertyValue`,'".$wrong_tables[$i]."','".$good_tables[$i]."')");
		$db->query();
	}
}

$db->setQuery("DESCRIBE #__rsform_forms");
$form_properties = $db->loadAssocList();
$exists_email_attach = 0;
$exists_email_attach_file = 0;
$exists_process2 = 0;
$exists_user_cc = 0;
$exists_user_bcc = 0;
$exists_user_reply = 0;
$exists_admin_cc = 0;
$exists_admin_bcc = 0;
$exists_admin_reply = 0;
foreach ($form_properties as $prop)
{
	if($prop['Field'] == 'UserEmailAttach') $exists_email_attach = 1;
	if($prop['Field'] == 'UserEmailAttachFile') $exists_email_attach_file = 1;
	if($prop['Field'] == 'ScriptProcess2') $exists_process2 = 1;
	if($prop['Field'] == 'UserEmailCC') $exists_user_cc = 1;
	if($prop['Field'] == 'UserEmailBCC') $exists_user_bcc = 1;
	if($prop['Field'] == 'UserEmailReplyTo') $exists_user_reply = 1;
	if($prop['Field'] == 'AdminEmailCC') $exists_admin_cc = 1;
	if($prop['Field'] == 'AdminEmailBCC') $exists_admin_bcc = 1;
	if($prop['Field'] == 'AdminEmailReplyTo') $exists_admin_reply = 1;
}
if(!$exists_email_attach) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `UserEmailAttach` TINYINT NOT NULL AFTER `UserEmailMode`"); $db->query(); }
if(!$exists_email_attach_file) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `UserEmailAttachFile` VARCHAR (255) NOT NULL AFTER `UserEmailAttach`"); $db->query(); }
if(!$exists_process2) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `ScriptProcess2` TEXT NOT NULL AFTER `ScriptProcess`"); $db->query(); }
if(!$exists_user_cc) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `UserEmailCC` VARCHAR (255) NOT NULL AFTER `UserEmailTo`"); $db->query(); }
if(!$exists_user_bcc) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `UserEmailBCC` VARCHAR (255) NOT NULL AFTER `UserEmailCC`"); $db->query(); }
if(!$exists_user_reply) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `UserEmailReplyTo` VARCHAR (255) NOT NULL AFTER `UserEmailBCC`"); $db->query(); }
if(!$exists_admin_cc) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `AdminEmailCC` VARCHAR (255) NOT NULL AFTER `AdminEmailTo`"); $db->query(); }
if(!$exists_admin_bcc) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `AdminEmailBCC` VARCHAR (255) NOT NULL AFTER `AdminEmailCC`"); $db->query(); }
if(!$exists_admin_reply) { $db->setQuery("ALTER TABLE #__rsform_forms ADD `AdminEmailReplyTo` VARCHAR (255) NOT NULL AFTER `AdminEmailBCC`"); $db->query(); }

$db->setQuery("SELECT * FROM #__rsform_component_type_fields WHERE `ComponentTypeId` = 2 AND `FieldName`='WYSIWYG'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields SET `ComponentTypeId`=2, `FieldName`='WYSIWYG', `FieldType`='select', `FieldValues`='NO\r\nYES', `Ordering` = 11");
	$db->query();
	$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE `ComponentTypeId`=2");
	$components = $db->loadAssocList();
	foreach ($components as $comp)
	{
		$db->setQuery("INSERT INTO #__rsform_properties SET `ComponentId`='".$comp['ComponentId']."', `PropertyName`='WYSIWYG', `PropertyValue`='NO'");
		$db->query();
	}
}

$db->setQuery("SELECT * FROM #__rsform_component_type_fields WHERE `ComponentTypeId` = 8 AND `FieldName`='SIZE'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields SET `ComponentTypeId`=8, `FieldName`='SIZE', `FieldType`='textbox', `FieldValues`='15', `Ordering` = 12");
	$db->query();
	$components = $db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE `ComponentTypeId`=8");
	$db->loadAssocList();
	foreach ($components as $comp)
	{
		$db->setQuery("INSERT INTO #__rsform_properties SET `ComponentId`='".$comp['ComponentId']."', `PropertyName`='SIZE', `PropertyValue`='15'");
		$db->query();
	}
}

$db->setQuery("DESCRIBE #__rsform_submission_values");
$sqlinfo = $db->loadAssocList();
$form_id = 0;
foreach ($sqlinfo as $sql)
	if($sql['Field'] == 'FormId') $form_id = 1;

if(!$form_id)
{
	$db->setQuery("ALTER TABLE #__rsform_submission_values ADD `FormId` INT NOT NULL AFTER `SubmissionValueId`");
	$db->query();
	$db->setQuery("UPDATE #__rsform_submission_values sv, #__rsform_submissions s SET sv.FormId=s.FormId WHERE sv.SubmissionId = s.SubmissionId");
	$db->query();
}

$index_ctid = 0;
$index_fid = 0;
$db->setQuery("DESCRIBE #__rsform_components");
$sqlinfo = $db->loadAssocList();
foreach ($sqlinfo as $sql)
{
	if ($sql['Field'] == 'ComponentTypeId' && $sql['Key'] == 'MUL') $index_ctid = 1;
	if ($sql['Field'] == 'FormId' && $sql['Key'] == 'MUL') $index_fid = 1;
}
if (!$index_ctid)
{
	$db->setQuery("ALTER TABLE #__rsform_components ADD INDEX (`ComponentTypeId`)");
	$db->query();
}
if (!$index_fid)
{
	$db->setQuery("ALTER TABLE #__rsform_components ADD INDEX (`FormId`)");
	$db->query();
}
$index_ctid = 0;
$db->setQuery("DESCRIBE #__rsform_component_type_fields");
$sqlinfo = $db->loadAssocList();
foreach ($sqlinfo as $sql)
	if ($sql['Field'] == 'ComponentTypeId' && $sql['Key'] == 'MUL')	$index_ctid = 1;
if (!$index_ctid)
{
	$db->setQuery("ALTER TABLE #__rsform_component_type_fields ADD INDEX (`ComponentTypeId`)");
	$db->query();
}

$index_cid = 0;
$db->setQuery("DESCRIBE #__rsform_properties");
$sqlinfo = $db->loadAssocList();
foreach ($sqlinfo as $sql)
	if ($sql['Field'] == 'ComponentId' && $sql['Key'] == 'MUL') $index_cid = 1;
if (!$index_cid)
{
	$db->setQuery("ALTER TABLE #__rsform_properties ADD INDEX (`ComponentId`)");
	$db->query();
}
$index_fid = 0;
$db->setQuery("DESCRIBE #__rsform_submissions");
$sqlinfo = $db->loadAssocList();
foreach ($sqlinfo as $sql)
	if ($sql['Field'] == 'FormId' && $sql['Key'] == 'MUL') $index_fid = 1;
if (!$index_fid)
{
	$db->setQuery("ALTER TABLE #__rsform_submissions ADD INDEX (`FormId`)");
	$db->query();
}

$index_fid = 0;
$index_sid = 0;
$db->setQuery("DESCRIBE #__rsform_submission_values");
$sqlinfo = $db->loadAssocList();
foreach ($sqlinfo as $sql)
{
	if ($sql['Field'] == 'FormId' && $sql['Key'] == 'MUL') $index_fid = 1;
	if ($sql['Field'] == 'SubmissionId' && $sql['Key'] == 'MUL') $index_sid = 1;
}
if (!$index_fid)
{
	$db->setQuery("ALTER TABLE #__rsform_submission_values ADD INDEX (`FormId`)"); 
	$db->query();
}
if (!$index_sid)
{
	$db->setQuery("ALTER TABLE #__rsform_submission_values ADD INDEX (`SubmissionId`)");
	$db->query();
}
$index_cid = 0;
$db->setQuery("DESCRIBE #__rsform_mappings"); $sqlinfo = $db->loadAssocList();
foreach ($sqlinfo as $sql)
	if ($sql['Field'] == 'FormId' && $sql['Key'] == 'MUL') $index_cid = 1;
if (!$index_cid)
{
	$db->setQuery("ALTER TABLE #__rsform_mappings ADD INDEX (`ComponentId`)");
	$db->query();
}

$db->setQuery("ALTER TABLE #__rsform_component_type_fields CHANGE `FieldType` `FieldType` ENUM( 'hidden', 'hiddenparam', 'textbox', 'textarea', 'select' ) NOT NULL DEFAULT 'hidden'");
$db->query();

$db->setQuery("SELECT * FROM #__rsform_config WHERE `SettingName`='global.iis'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO #__rsform_config SET `SettingName`='global.iis', `SettingValue`='1'");
	$db->query();
}
$db->setQuery("SELECT * FROM #__rsform_config WHERE `SettingName`='global.editor'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO #__rsform_config SET `SettingName`='global.editor', `SettingValue`='1'");
	$db->query();
}

$db->setQuery("SELECT * FROM #__rsform_component_type_fields WHERE `ComponentTypeId` = 8 AND `FieldName`='IMAGETYPE'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields SET `ComponentTypeId`=8, `FieldName`='IMAGETYPE', `FieldType`='select', `FieldValues`='FREETYPE\r\nNOFREETYPE\r\nINVISIBLE', `Ordering` = 3");
	$db->query();
	$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE `ComponentTypeId`=8");
	$components = $db->loadAssocList();
	foreach ($components as $comp)
	{
		$db->setQuery("INSERT INTO #__rsform_properties SET `ComponentId`='".$comp['ComponentId']."', `PropertyName`='IMAGETYPE', `PropertyValue`='FREETYPE'");
		$db->query();
	}
}

$db->setQuery("SELECT * FROM #__rsform_component_type_fields WHERE `ComponentTypeId` = 1 AND `FieldName`='VALIDATIONEXTRA'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields SET `ComponentTypeId`=1, `FieldName`='VALIDATIONEXTRA', `FieldType`='textbox', `FieldValues`='', `Ordering` = 6");
	$db->query();
	$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE `ComponentTypeId`=1");
	$components = $db->loadAssocList();
	foreach ($components as $comp)
	{
		$db->setQuery("INSERT INTO #__rsform_properties SET `ComponentId`='".$comp['ComponentId']."', `PropertyName`='VALIDATIONEXTRA', `PropertyValue`=''");
		$db->query();
	}
}
$db->setQuery("SELECT * FROM #__rsform_component_type_fields WHERE `ComponentTypeId` = 2 AND `FieldName`='VALIDATIONEXTRA'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields SET `ComponentTypeId`=2, `FieldName`='VALIDATIONEXTRA', `FieldType`='textbox', `FieldValues`='', `Ordering` = 6");
	$db->query();
	$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE `ComponentTypeId`=2");
	$components = $db->loadAssocList();
	foreach ($components as $comp)
	{
		$db->setQuery("INSERT INTO #__rsform_properties SET `ComponentId`='".$comp['ComponentId']."', `PropertyName`='VALIDATIONEXTRA', `PropertyValue`=''");
		$db->query();
	}
}
$db->setQuery("SELECT * FROM #__rsform_component_type_fields WHERE `ComponentTypeId` = 14 AND `FieldName`='VALIDATIONRULE'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields SET `ComponentTypeId`=14, `FieldName`='VALIDATIONRULE', `FieldType`='select', `FieldValues`='//<code>\r\nreturn RSgetValidationRules();\r\n//</code>', `Ordering` = 9");
	$db->query();
	$db->setQuery("SELECT ComponentId FROM #__rsform_components WHERE `ComponentTypeId`=14");
	$components = $db->loadAssocList();
	foreach ($components as $comp)
	{
		$db->setQuery("INSERT INTO #__rsform_properties SET `ComponentId`='".$comp['ComponentId']."', `PropertyName`='VALIDATIONRULE', `PropertyValue`=''");
		$db->query();
	}
}

$db->setQuery("SHOW COLUMNS FROM #__rsform_forms WHERE `Field`='MetaTitle'");
$db->query();
if ($db->getNumRows() == 0)
{
	$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `MetaTitle` TINYINT( 1 ) NOT NULL");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `MetaDesc` TEXT NOT NULL");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `MetaKeywords` TEXT NOT NULL");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `Required` VARCHAR( 255 ) NOT NULL DEFAULT '(*)'");
	$db->query();
	$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `ErrorMessage` TEXT NOT NULL");
	$db->query();
}

$db->setQuery("SELECT FormId FROM #__rsform_forms WHERE FormId='1' AND FormName='RSformPro example' AND ErrorMessage=''");
if ($db->loadResult())
{
	$db->setQuery("UPDATE #__rsform_forms SET MetaTitle=0, MetaDesc='This is the meta description of your form. You can use it for SEO purposes.', MetaKeywords='rsform, contact, form, joomla', Required='(*)', ErrorMessage='<p class=\"formRed\">Please complete all required fields!</p>' WHERE FormId='1' LIMIT 1");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM `#__rsform_forms` WHERE `Field`='FormLayout'");
$result = $db->loadObject();
if (strtolower($result->Type == 'text'))
{
	$db->setQuery("ALTER TABLE `#__rsform_forms` CHANGE `FormLayout` `FormLayout` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
	$db->query();
}

$db->setQuery("SELECT ComponentTypeFieldId FROM #__rsform_component_type_fields WHERE `ComponentTypeId` = 9 AND `FieldName`='PREFIX'");
if (!$db->loadResult())
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields SET ComponentTypeId='9', FieldName='PREFIX', FieldType='textarea', FieldValues='', Ordering='6'");
	$db->query();
}

$db->setQuery("SELECT ComponentTypeFieldId FROM #__rsform_component_type_fields WHERE `ComponentTypeId` = 13 AND `FieldName`='PREVBUTTON'");
if (!$db->loadResult())
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields SET ComponentTypeId='13', FieldName='PREVBUTTON', FieldType='textbox', FieldValues='//<code>\r\nreturn JText::_(''PREV'');\r\n//</code>', Ordering='8'");
	$db->query();
}

$db->setQuery("SELECT ComponentTypeFieldId FROM #__rsform_component_type_fields WHERE `ComponentTypeId` = 41 AND `FieldName`='NAME'");
if (!$db->loadResult())
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields (ComponentTypeFieldId, ComponentTypeId, FieldName, FieldType, FieldValues, Ordering)".
				  " VALUES ('', 41, 'NAME', 'textbox', '', 1),".
				  " ('', 41, 'COMPONENTTYPE', 'hidden', '41', 5),".
				  " ('', 41, 'NEXTBUTTON', 'textbox', '//<code>\r\nreturn JText::_(''NEXT'');\r\n//</code>', 2),".
				  " ('', 41, 'PREVBUTTON', 'textbox', '//<code>\r\nreturn JText::_(''PREV'');\r\n//</code>', 3),".
				  " ('', 41, 'ADDITIONALATTRIBUTES', 'textarea', '', 4)");
	$db->query();
}

$db->setQuery("SHOW COLUMNS FROM `#__rsform_forms` WHERE `Field`='CSS'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `CSS` TEXT NOT NULL AFTER `FormLayoutAutogenerate` ,".
				  " ADD `JS` TEXT NOT NULL AFTER `CSS` ,".
				  " ADD `ShowThankyou` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `ReturnUrl` ,".
				  " ADD `UserEmailScript` TEXT NOT NULL AFTER `ScriptDisplay` ,".
				  " ADD `AdminEmailScript` TEXT NOT NULL AFTER `UserEmailScript` ,".
				  " ADD `MultipleSeparator` VARCHAR( 64 ) NOT NULL AFTER `ErrorMessage` ,".
				  " ADD `TextareaNewLines` TINYINT( 1 ) NOT NULL AFTER `MultipleSeparator`");
	$db->query();
}

$db->setQuery("UPDATE #__rsform_component_type_fields SET FieldValues='//<code>\r\nreturn JPATH_SITE.DS.''components''.DS.''com_rsform''.DS.''uploads''.DS;\r\n//</code>' WHERE FieldName='DESTINATION' AND ComponentTypeId=9 AND FieldValues LIKE '%RSadapter%'");
$db->query();

// R32

$db->setQuery("SHOW COLUMNS FROM `#__rsform_forms` WHERE `Field`='CSSClass'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `CSSClass` VARCHAR( 255 ) NOT NULL AFTER `TextareaNewLines` ,".
				  " ADD `CSSId` VARCHAR( 255 ) NOT NULL DEFAULT 'userForm' AFTER `CSSClass` ,".
				  " ADD `CSSName` VARCHAR( 255 ) NOT NULL AFTER `CSSId` ,".
				  " ADD `CSSAction` TEXT NOT NULL AFTER `CSSName` ,".
				  " ADD `CSSAdditionalAttributes` TEXT NOT NULL AFTER `CSSAction`,".
				  " ADD `AjaxValidation` TINYINT( 1 ) NOT NULL AFTER `CSSAdditionalAttributes`");
	$db->query();
}

// R33
$db->setQuery("SHOW COLUMNS FROM `#__rsform_forms` WHERE `Field`='UserEmailConfirmation'");
if ($db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsform_forms` DROP `UserEmailConfirmation`");
	$db->query();
}
$db->setQuery("SHOW COLUMNS FROM `#__rsform_forms` WHERE `Field`='ThemeParams'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `ThemeParams` TEXT NOT NULL");
	$db->query();
}

$db->setQuery("SELECT `ComponentTypeFieldId` FROM #__rsform_component_type_fields WHERE `ComponentTypeId`='41' AND `FieldName`='VALIDATENEXTPAGE'");
if (!$db->loadResult())
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields SET `ComponentTypeId`='41', `FieldName`='VALIDATENEXTPAGE', `FieldType`='select', `FieldValues`='NO\r\nYES', `Ordering`='5'");
	$db->query();
}

// R34
$db->setQuery("SHOW COLUMNS FROM `#__rsform_forms` WHERE `Field`='ShowContinue'");
if (!$db->loadResult())
{
	$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `ShowContinue` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `Thankyou`");
	$db->query();
}
$db->setQuery("SELECT `ComponentTypeFieldId` FROM #__rsform_component_type_fields WHERE `ComponentTypeId`='6' AND `FieldName`='MINDATE'");
if (!$db->loadResult())
{
	$db->setQuery("INSERT INTO #__rsform_component_type_fields (ComponentTypeFieldId, ComponentTypeId, FieldName, FieldType, FieldValues, Ordering)".
				  " VALUES (NULL, '6', 'MINDATE', 'textbox', '', '5'),".
				  " (NULL, '6', 'MAXDATE', 'textbox', '', '5'),".
				  " (NULL, '6', 'DEFAULTVALUE', 'textarea', '', '2')");
	$db->query();
}
?>

<div align="left" width="100%"><b>RSForm! Pro <?php echo _RSFORM_VERSION;?> Rev <?php echo _RSFORM_REVISION; ?> Installed</b></div>