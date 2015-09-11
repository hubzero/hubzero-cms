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

require_once( PATH_CORE . DS . 'components' . DS
	.'com_projects' . DS . 'tables' . DS . 'publicstamp.php');

$database 	= App::get('db');
$objSt 		= new \Components\Projects\Tables\Stamp( $database );

// Get listed public files
$items = $objSt->getPubList($this->model->get('id'), 'files');

if ($items) {
?>
<div class="public-list-header">
	<h3><?php echo ucfirst(Lang::txt('COM_PROJECTS_PUBLIC')); ?> <?php echo Lang::txt('COM_PROJECTS_FILES'); ?></h3>
</div>
<div class="public-list-wrap">
	<ul>
		<?php foreach ($items as $item)
		{
			$ref = json_decode($item->reference);
			$file = new \Components\Projects\Models\File($e);
		?>
		<li><a href="<?php echo Route::url($this->model->link('stamp') . '&s=' . $item->stamp); ?>"><?php echo $file::drawIcon($file->get('ext')); ?> <?php echo basename($ref->file); ?></li>
		<?php
		} ?>
	</ul>
</div>
<?php } ?>
