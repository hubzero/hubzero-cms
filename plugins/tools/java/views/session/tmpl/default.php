<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->js('jquery.editable.min.js', 'system')
     ->js('deployJava.js')
     ->js();

$base = rtrim(Request::base(true), '/');
?>
<p id="troubleshoot" class="help">
	If your application fails to appear within a minute, <a target="_blank" rel="external" href="http://www.java.com/en/download/testjava.jsp">troubleshoot this problem</a>.
</p>

<applet id="<?php echo $this->output->id ?>" class="<?php echo $this->output->class; ?>" code="<?php echo $this->output->code; ?>" archive="<?php echo $this->output->archive; ?>" width="<?php echo $this->output->width; ?>" height="<?php echo $this->output->height; ?>" MAYSCRIPT>
	<param name="name" value="<?php echo $this->output->name; ?>" />
	<?php if (!empty($this->output->host)) { ?>
		<param name="HOST" value="<?php echo $this->output->host; ?>" />
	<?php } ?>
	<param name="PORT" value="<?php echo $this->output->port; ?>" />
	<param name="ENCPASSWORD" value="<?php echo $this->output->encpassword; ?>" />
	<param name="CONNECT" value="<?php echo $this->output->connect; ?>" />
	<param name="View Only" value="<?php echo $this->output->view_only; ?>" />
	<param name="trustAllVncCerts" value="<?php echo $this->output->trust_all_vnc_certs; ?>" />
	<param name="Offer relogin" value="<?php echo $this->output->offer_relogin; ?>" />
	<param name="DisableSSL" value="<?php echo $this->output->disable_ssl; ?>" />
	<param name="permissions" value="<?php echo $this->output->permissions; ?>" />
	<param name="Show Controls" value="<?php echo $this->output->show_controls; ?>" />
	<param name="ENCODING" value="<?php echo $this->output->encoding; ?>" />
	<?php if (!empty($this->output->debug)) { ?>
		<param name="Debug" value="<?php echo $this->output->debug; ?>" />
	<?php } ?>
	<?php if (!empty($this->output->show_local_cursor)) { ?>
		<param name="ShowLocalCursor" value="<?php echo $this->output->show_local_cursor; ?>" />
	<?php } ?>
	<p class="error">
		In order to view an application, you must have Java installed and enabled. (<a href="<?php echo $base; ?>/kb/misc/java">How do I do this?</a>)
	</p>
</applet>

<script type="text/javascript">
	var JavaNotFound = 'It appears that Java is either not installed or not enabled.'<?php
		$options = array();
		if ($plugins = Event::trigger('tools.onToolSessionIdentify'))
		{
			$url = 'index.php?option=com_tools&app=' . $this->app->name . '&task=session&sess=' . $this->app->sess . '&viewer=';
			foreach ($plugins as $plugin)
			{
				if ($plugin->name == 'java')
				{
					continue;
				}

				$options[] = '<a href="' . Route::url($url . $plugin->name) . '">' . $plugin->title . '</a>';
			}
			if (count($options))
			{
				echo " + 'Consider trying an alternate viewer:<br /><br />" . implode('<br />', $options) . "'";
			}
		}
		?>;

	var JavaNotStarted = 'It appears that the Java environment did not start properly. Please make sure that you have Java installed and enabled for your web browser.'<?php
		if (count($options))
		{
			echo " + 'If you do not have Java or problems persist, consider trying an alternate viewer:<br /><br />" . implode('<br />', $options) . "'";
		}
		?>;

	var JavaBug = 'There is a problem caused by the specific version ' +
				'of Java you are using with this browser. You will ' +
				'likely not be able to view tools. There are three ' +
				'things you can try:<br /> ' +
				'1) Restart your browser and disable Javascript ' +
				'before starting a tool the ' +
				'first time and re-enable Javascript once the first ' +
				'tool is running.<br />' +
				'2) Switch to a different version of Java. ' +
				'Version 1.6.0 Update 02 (and earlier) will work ' +
				'but 1.6.0 Update 03 and 04 do not.<br>' +
				'3) Use a browser other than Firefox.<br>' +
				'(<a href="<?php echo Request::base(); ?>/kb/tools/unable_to_connect_error_in_firefox/">More information</a>)  ';

	if (!deployJava.versionCheck("1.6.0+")) {
		HUB.Mw.noJava();
	} else {
		HUB.Mw.startAppletTimeout();
		HUB.Mw.connectingTool();
	}
</script>
