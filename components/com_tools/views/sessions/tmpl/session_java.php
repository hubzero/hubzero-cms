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

\Hubzero\Document\Assets::addComponentScript('com_tools', 'assets/js/sessions');
\Hubzero\Document\Assets::addSystemScript('jquery.editable.min');

$base = rtrim(JURI::base(true), '/');

?>
			<p id="troubleshoot" class="help">If your application fails to appear within a minute, <a target="_blank" href="http://www.java.com/en/download/testjava.jsp">troubleshoot this problem.</a></p>

			<applet id="<?php echo $this->output->id ?>" class="<?php echo $this->output->class; ?>" code="<?php echo $this->output->code; ?>" archive="<?php echo $this->output->archive; ?>" width="<?php echo $this->output->width; ?>" height="<?php echo $this->output->height; ?>" MAYSCRIPT>
				<param name="name" value="<?php echo $this->output->name; ?>" />
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
<?php 				if (!empty($this->output->debug)) { ?>
				<param name="Debug" value="<?php echo $this->output->debug; ?>" />
<?php 				} ?>
<?php 				if (!empty($this->output->show_local_cursor)) { ?>
				<param name="ShowLocalCursor" value="<?php echo $this->output->show_local_cursor; ?>" />
<?php 				} ?>
				<p class="error">
					In order to view an application, you must have Java installed and enabled. (<a href="<?php echo $base; ?>/kb/misc/java">How do I do this?</a>)
				</p>
			</applet>

			<script type="text/javascript">
			HUB.Mw.startAppletTimeout();
			HUB.Mw.connectingTool();
			</script>
