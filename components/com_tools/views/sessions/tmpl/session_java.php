<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->js('sessions.js')
     ->js('jquery.editable.min.js', 'system');

$base = rtrim(JURI::base(true), '/');

$cls = array();
if ($this->app->params->get('noResize', 0))
{
	$cls[] = 'no-resize';
}
if ($this->app->params->get('noPopout', 0))
{
	$cls[] = 'no-popout';
}
if ($this->app->params->get('noPopoutClose', 0))
{
	$cls[] = 'no-popout-close';
}
if ($this->app->params->get('noPopoutMaximize', 0))
{
	$cls[] = 'no-popout-maximize';
}
if ($this->app->params->get('noRefresh', 0))
{
	$cls[] = 'no-refresh';
}
if ($this->app->params->get('vncEncoding',0))
{
	$this->output->encoding = trim($this->app->params->get('vncEncoding',''),'"');
}
if ($this->app->params->get('vncShowControls',0))
{
	$this->output->show_controls = trim($this->app->params->get('vncShowControls',''),'"');
}
if ($this->app->params->get('vncShowLocalCursor',0))
{
	$this->output->show_local_cursor = trim($this->app->params->get('vncShowLocalCursor',''),'"');
}
if ($this->app->params->get('vncDebug',0))
{
	$this->output->debug = trim($this->app->params->get('vncDebug',''),'"');
}
?>

<applet id="theapp" class="thisapp<?php if (!empty($cls)) { echo ' ' . implode(' ', $cls); } ?>" code="VncViewer.class" archive="<?php echo $base; ?>/components/com_tools/scripts/VncViewer-20140116-01.jar" width="<?php echo $this->output->width; ?>" height="<?php echo $this->output->height; ?>" MAYSCRIPT>
	<param name="name" value="App Viewer" />
	<param name="PORT" value="<?php echo $this->output->port; ?>" />
	<param name="ENCPASSWORD" value="<?php echo $this->output->password; ?>" />
	<param name="CONNECT" value="<?php echo $this->output->connect; ?>" />
	<param name="View Only" value="<?php echo ($this->readOnly) ? 'Yes' : 'No'; ?>" />
	<param name="trustAllVncCerts" value="Yes" />
	<param name="Offer relogin" value="Yes" />
	<param name="DisableSSL" value="No" />
	<param name="permissions" value="all-permissions" />
	<?php if (!empty($this->output->debug)) { ?>
		<param name="Debug" value="<?php echo $this->output->debug; ?>" />
	<?php } ?>
	<param name="Show Controls" value="<?php echo $this->output->show_controls; ?>" />
	<?php if (!empty($this->output->show_local_cursor)) { ?>
		<param name="ShowLocalCursor" value="<?php echo $this->output->show_local_cursor; ?>" />
	<?php } ?>
	<param name="ENCODING" value="<?php echo $this->output->encoding; ?>" />
	<p class="error">
		<?php echo JText::_('COM_TOOLS_ERROR_JAVA_REQUIRED'); ?>
	</p>
</applet>

<script type="text/javascript">
	HUB.Mw.startAppletTimeout();
	HUB.Mw.connectingTool();
</script>
