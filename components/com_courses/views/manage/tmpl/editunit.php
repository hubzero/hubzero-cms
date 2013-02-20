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

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias');

$unit       = $this->course->offering->unit($this->scope_id);
$start_date = (!is_null($unit->get('start_date')) && $unit->get('start_date') != '0000-00-00')? $unit->get('start_date') : '';
$end_date   = (!is_null($unit->get('end_date')) && $unit->get('end_date') != '0000-00-00')? $unit->get('end_date') : '';
?>

<div class="main section <?php echo $this->scope; ?>-edit">
	<form action="/api/courses/unitsave" class="unit-details-form">
		<input type="text" value="<?php echo $start_date; ?>" name="start_date" id="start_date" class="datepicker" placeholder="Start publishing on" />
		<input type="text" value="<?php echo $end_date; ?>"   name="end_date"   id="end_date"   class="datepicker" placeholder="Stop publishing on" />

		<input type="hidden" name="id" value="<?php echo $this->scope_id; ?>" />
		<input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
		<input type="hidden" name="offering" value="<?= $this->course->offering()->get('alias') ?>" />

		<input type="submit" name="submit" value="Save" />
	</form>
</div>