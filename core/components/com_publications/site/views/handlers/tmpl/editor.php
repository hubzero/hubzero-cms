<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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
// no direct access
defined('_JEXEC') or die('Restricted access');

$url = Route::url($this->publication->link('edit'));

?>
<div id="abox-content" class="handler-wrap">
	<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_HANDLER') . ' - ' . $this->editor->configs->label; ?></h3>
	<?php
	// Display error  message
	if ($this->getError()) {
		echo ('<p class="error">' . $this->getError() . '</p>');
	} else { // No error
	?>
	<form id="<?php echo $this->ajax ? 'hubForm-ajax' : 'plg-form'; ?>" method="post" action="<?php echo $url; ?>">
	<div id="handler-status" class="handler-status">
		<?php echo $this->editor->drawStatus(); ?>
	</div>
	<div id="handler-content" class="handler-content">
		<?php echo $this->editor->drawEditor(); ?>
	</div>
	</form>
	<?php } ?>
</div>