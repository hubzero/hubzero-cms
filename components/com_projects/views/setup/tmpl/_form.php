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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

?>
<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="alias" value="<?php echo $this->project->alias; ?>" />
<input type="hidden" name="pid" id="pid" value="<?php echo $this->project->id; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="setup" id="insetup" value="<?php echo $this->project->state == 1 ? 0 : 1; ?>" />
<input type="hidden" name="active" value="<?php echo $this->section; ?>" />
<input type="hidden" name="step" id="step" value="<?php echo $this->step; ?>" />
<input type="hidden" name="gid" value="<?php echo $this->gid; ?>" />
