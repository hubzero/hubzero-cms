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

$this->css()
	->js();

?>
<div id="project-wrap">
	<section class="main section">
		<?php
			$this->view('_header')
			     ->set('model', $this->model)
			     ->set('showPic', 1)
			     ->set('showPrivacy', 0)
			     ->set('goBack', 0)
			     ->set('showUnderline', 1)
			     ->set('option', $this->option)
			     ->display();
		?>
		<p class="warning"><?php echo Lang::txt('COM_PROJECTS_PROJECT_PENDING_APPROVAL'); ?></p>
	</section><!-- / .main section -->
</div>