<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
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

// No direct access
defined('_HZEXEC_') or die();

// Build url
$route = $this->model->isProvisioned()
	? 'index.php?option=com_publications&task=submit'
	: 'index.php?option=com_projects&alias=' . $this->model->get('alias');

$attached = isset($this->attachments) && count($this->attachments) > 0 ? $this->attachments[0] : '';
$path 	  = $attached ? $attached->path : '';
$serial   = $attached ? $attached->object_id : '';

?>
<div id="import-link">
	<label>
	<input type="text" name="url" size="40" id="parse-url" placeholder="http:// OR doi:" value="<?php echo $path; ?>" />
	</label>
</div>