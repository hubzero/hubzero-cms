<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juser =& JFactory::getUser();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<?php if ($this->_msg) { ?>
	<p class="passed"><?php echo $this->_msg; ?></p>
<?php } ?>
	<h2>Accessing your dropbox folder</h2>
	<p>Your dropbox folder has been created. All members of your group can access this shared folder using any of the following methods:</p>
	<div class="option-synch">
		<h3>SynchroNEES Tool</h3>
		<p>This program was created by the NEES project to make it easy to access your group files and your NEEShub home directory from your desktop. <a rel="internal" href="/topics/synchronees">More information &rarr;</a></p>
	</div>
	<div class="option-workspace">
		<h3>Within a Workspace</h3>
		<p>Our <a href="/tools/workspace">workspace</a> tool gives you a Linux desktop that you can access anytime, anywhere with your web browser. Within the workspace, your group shared folder is just another directory. Access it as follows:</p>
		<p><code>% cd /data/groups/<?php echo $this->group->get('cn'); ?>/dropbox</code></p>
	</div>
	<div class="option-sftp">
		<h3>Secure File Transfer Protocol (SFTP)</h3>
		<p>Use any standard SFTP client to access your dropbox folder as follows:</p>
		<p><code>% sftp <?php echo $juser->get('username'); ?>@nees.org</code><br />
		<code>% cd /data/groups/<?php echo $this->group->get('cn'); ?>/dropbox</code></p>
		<p>The SFTP program is fairly standard on MacOSX and Linux machines. If you're running Windows, you'll have to download a special program to do this. One good choice is the <a rel="external" href="http://www.chiark.greenend.org.uk/~sgtatham/putty/download.html">psftp</a> utility.</p>
	</div>
	<div class="option-webdav">
		<h3>WebDAV</h3>
		<p>Configure your desktop to mount your NEEShub file storage as a remote drive via the WebDav protocol.</p>
		<dl>
			<dt>Windows</dt>
			<dd>
				Go to "My Network Places" and click on "Add a Network Place".  When prompted for the URL, enter the following:<br />
				<a href="https://nees.org/data/groups/<?php echo $this->group->get('cn'); ?>">https://nees.org/data/groups/<?php echo $this->group->get('cn'); ?></a><br />
				You'll be prompted for your NEEShub login name and password, and your group folder will be added as a new folder in the network places.
			</dd>
			<dt>Mac OS X</dt>
			<dd>
				Click on your desktop, then from the "Go" menu, choose "Connect to Server...".  When prompted for the URL, the the following:<br />
				<a href="https://nees.org/data/groups/<?php echo $this->group->get('cn'); ?>">https://nees.org/data/groups/<?php echo $this->group->get('cn'); ?></a><br />
				You'll be prompted for your NEEShub login name and password, then a Finder window will appear with your group data area.
			</dd>
			<dt>Linux</dt>
			<dd>
				Linux:  Gnome users typically use <a href="http://www.webdavsystem.com/server/access/gnome_nautilus">Nautilus</a>, while KDE users use the <a href="http://www.webdavsystem.com/server/access/konqueror">Konqueror web browser</a> to access their files.  When prompted for the URL, fill in:
				<a href="https://nees.org/data/groups/<?php echo $this->group->get('cn'); ?>">https://nees.org/data/groups/<?php echo $this->group->get('cn'); ?></a>
			</dd>
		</dl>
	</div>