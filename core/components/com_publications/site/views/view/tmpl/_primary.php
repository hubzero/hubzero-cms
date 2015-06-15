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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   GNU General Public License, version 2 (GPLv2)
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if ($this->disabled) { ?>
	<p id="primary-document">
		<span class="btn disabled <?php echo $this->class; ?>"><?php echo $this->msg; ?></span>
	</p>
<?php } else { ?>
	<p id="primary-document">
		<a class="btn btn-primary<?php echo ($this->class)  ? ' ' . $this->class : ''; ?>" <?php
				echo ($this->href)   ? ' href="' . $this->href . '"' : '';
				echo ($this->title)  ? ' title="' . $this->escape($this->title) . '"' : '';
				echo ($this->action) ? ' ' . $this->action : '';
			?>><?php echo $this->msg; ?></a>
	</p>
<?php } ?>

<?php if ($this->pop) { ?>
	<div id="primary-document_pop">
		<div><?php echo $this->pop; ?></div>
	</div>
<?php } ?>