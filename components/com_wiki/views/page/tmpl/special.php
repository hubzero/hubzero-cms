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
defined('_JEXEC') or die('Restricted access');

if (!$this->sub)
{
	$this->css();
}
$this->js();

$juser = JFactory::getUser();
?>
	<header id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
		<h2><?php echo $this->page->get('title'); ?></h2>
	</header><!-- /#content-header -->

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php if ($this->message) { ?>
	<p class="passed"><?php echo $this->message; ?></p>
<?php } ?>

<?php
	$this->view('submenu', 'page')
	     ->setBasePath($this->base_path)
	     ->set('option', $this->option)
	     ->set('controller', $this->controller)
	     ->set('page', $this->page)
	     ->set('task', $this->task)
	     ->set('sub', $this->sub)
	     ->display();
?>

	<section class="main section">
		<article>
		<?php
			$this->view(strtolower($this->layout), 'special')
			     ->setBasePath($this->base_path)
			     ->set('option', $this->option)
			     ->set('controller', $this->controller)
			     ->set('page', $this->page)
			     ->set('task', $this->task)
			     ->set('sub', $this->sub)
			     ->set('config', $this->config)
			     ->set('book', $this->book)
			     ->display()
		?>
		</article>
	</section><!-- / .main section -->