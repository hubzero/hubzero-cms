<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('applications')
     ->css()
     ->js();

// get active var
$active = Request::getCmd('active');
?>

<header id="content-header">
	<h2><?php echo $this->escape($this->application->get('name')); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="btn icon-browse" href="<?php echo Route::url('index.php?option=com_developer&controller=applications'); ?>">
				<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATIONS_ALL'); ?>
			</a>
		</p>
	</div>
</header>

<?php
	echo $this->view('_menu')
			  ->set('active', $active)
			  ->set('application', $this->application)
			  ->display();
?>

<section class="main section">
	<div class="section-inner">
		<?php
		echo $this->view($active)
				  ->set('application', $this->application)
				  ->display();

		echo $this->view('_sidebar')
				  ->set('active', $active)
				  ->display();
		?>
	</div>
</section>
