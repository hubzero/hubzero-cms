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

$mode = $this->page->params->get('mode', 'wiki');

$orauthor = JText::_('Unknown');
$oruser =& JUser::getInstance($this->or->created_by);
if (is_object($oruser)) {
	$orauthor = $oruser->get('name');
}

$drauthor = JText::_('Unknown');
$druser =& JUser::getInstance($this->dr->created_by);
if (is_object($druser)) {
	$drauthor = $druser->get('name');
}
?>
	<div id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
		<h2><?php echo $this->escape($this->title); ?></h2>
<?php
if (!$mode || ($mode && $mode != 'static')) 
{
	$view = new JView(array(
		'base_path' => $this->base_path, 
		'name'      => 'page',
		'layout'    => 'authors'
	));
	$view->option   = $this->option;
	$view->page     = $this->page;
	$view->task     = $this->task;
	$view->config   = $this->config;
	$view->revision = $this->revision;
	$view->display();
}
?>
	</div><!-- /#content-header -->

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php if ($this->message) { ?>
	<p class="passed"><?php echo $this->message; ?></p>
<?php } ?>

<?php
	$view = new JView(array(
		'base_path' => $this->base_path, 
		'name'      => 'page',
		'layout'    => 'submenu'
	));
	$view->option = $this->option;
	$view->controller = $this->controller;
	$view->page   = $this->page;
	$view->task   = $this->task;
	$view->config = $this->config;
	$view->sub    = $this->sub;
	$view->display();
?>

<div class="section">
	<div class="two columns first">
		<dl class="diff-versions">
			<dt><?php echo JText::_('COM_WIKI_VERSION') . ' ' . $this->or->version; ?><dt>
			<dd><time datetime="<?php echo $this->or->created; ?>"><?php echo $this->or->created; ?></time> by <?php echo $this->escape($orauthor); ?><dd>
			
			<dt><?php echo JText::_('COM_WIKI_VERSION') . ' ' . $this->dr->version; ?><dt>
			<dd><time datetime="<?php echo $this->dr->created; ?>"><?php echo $this->dr->created; ?></time> by <?php echo $this->escape($drauthor); ?><dd>
		</dl>
	</div><!-- / .aside -->
	<div class="two columns second">
		<p class="diff-deletedline"><del class="diffchange">Deletions</del> or items before changed</p>
		<p class="diff-addedline"><ins class="diffchange">Additions</ins> or items after changed</p>
	</div><!-- / .subject -->
</div><!-- / .section -->
<div class="clear"></div>

<div class="main section">
	<?php echo $this->content; ?>
</div><!-- / .main section -->
<div class="clear"></div>
