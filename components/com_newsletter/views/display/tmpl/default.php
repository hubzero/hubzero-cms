<?php
/**
 * @package		HUBzero CMS
 * @author		Christopher Smoak <csmoak@purdue.edu>
 * @copyright	Copyright 2005-2010 by Purdue Research Foundation, West Lafayette, IN 47906
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
?>
<?php if(JRequest::getVar("tmpl", "") == "") : ?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>
<div class="main section">
	<div class="aside">
		<h4>Past Newsletters</h4>
		<ul>
			<?php foreach($this->campaigns as $c) : ?>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_newsletter&id='.$c->id); ?>">
						<?php echo $c->name; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div><!-- /.aside -->
	<div class="subject newsletter">
		<?php endif; ?>
		<?php
		//ini_set('display_errors', 1);
		//error_reporting(E_ALL);
			$file =  JPATH_ROOT . DS . 'templates' . DS . 'newsletter';
			//print_r($file);
			$handle = fopen($file, "w");
			fwrite($handle, $this->newsletter);
			fclose($handle);
			//die();
		?>
			<?php echo $this->newsletter; ?>
		<?php if(JRequest::getVar("tmpl", "") == "") : ?>
	</div><!-- /.subject -->
</div><!-- /.main .section -->
<?php endif; ?>	