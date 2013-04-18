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

$unit         = $this->course->offering()->unit($this->scope_id);
$publish_up   = (!is_null($unit->get('publish_up')) && $unit->get('publish_up') != '0000-00-00')? $unit->get('publish_up') : '';
$publish_down = (!is_null($unit->get('publish_down')) && $unit->get('publish_down') != '0000-00-00')? $unit->get('publish_down') : '';
?>

<div class="main section <?php echo $this->scope; ?>-edit">
	<form action="/api/courses/unit/save" class="unit-details-form">
		<input type="text" value="<?php echo $publish_up; ?>"   name="publish_up"   id="publish_up"   class="datepicker" placeholder="Start publishing on" />
		<input type="text" value="<?php echo $publish_down; ?>" name="publish_down" id="publish_down" class="datepicker" placeholder="Stop publishing on" />

		<input type="hidden" name="id" value="<?php echo $this->scope_id; ?>" />
		<input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
		<input type="hidden" name="offering" value="<?= $this->course->offering()->get('alias') ?>" />

		<input type="submit" name="submit" value="Save" />
	</form>
</div>