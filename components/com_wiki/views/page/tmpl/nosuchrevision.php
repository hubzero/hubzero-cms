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

$params =& new JParameter( $this->page->params );
$mode = $params->get( 'mode', 'wiki' );

if ($this->sub) {
	$hid = 'sub-content-header';
} else {
	$hid = 'content-header';
}
?>
<div id="<?php echo $hid; ?>" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- /#content-header -->

<?php
echo WikiHtml::subMenu( $this->sub, $this->option, $this->page->pagename, $this->page->scope, $this->page->state, $this->task, $params, $this->authorized );
?>

<div class="main section">
	<p class="warning">A page could not be found matching the version number <?php echo $this->version; ?>.</p>
	<div class="clear"></div>
</div><!-- / .main section -->