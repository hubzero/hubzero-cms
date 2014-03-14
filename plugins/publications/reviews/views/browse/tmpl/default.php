<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

$database = JFactory::getDBO();
$juser = JFactory::getUser();

if (!isset($this->reviews) || !$this->reviews) 
{
	$this->reviews = array();
}

foreach ($this->reviews as $k => $review)
{
	$this->reviews[$k] = new PublicationsModelReview($review);
}
$this->reviews = new \Hubzero\Base\ItemList($this->reviews);
?>
<h3 class="section-header">
	<?php echo JText::_('PLG_PUBLICATION_REVIEWS'); ?>
</h3>
<p class="section-options">
	<?php if ($juser->get('guest')) { ?>
			<a href="<?php echo JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&active=reviews&action=addreview#reviewform'))); ?>" class="icon-add add btn">
				<?php echo JText::_('PLG_PUBLICATION_REVIEWS_WRITE_A_REVIEW'); ?>
			</a>
	<?php } else { ?>
			<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&active=reviews&action=addreview#reviewform'); ?>" class="icon-add add btn">
				<?php echo JText::_('PLG_PUBLICATION_REVIEWS_WRITE_A_REVIEW'); ?>
			</a>
	<?php } ?>
</p>

<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>

<?php
if ($this->reviews->total() > 0)
{
	$view = new \Hubzero\Plugin\View(
		array(
			'folder'  => 'publications',
			'element' => 'reviews',
			'name'    => 'browse',
			'layout'  => '_list'
		)
	);
	$view->parent      = 0;
	$view->cls         = 'odd';
	$view->depth       = 0;
	$view->option      = $this->option;
	$view->publication = $this->publication;
	$view->comments    = $this->reviews;
	$view->config      = $this->config;
	$view->base        = 'index.php?option=' . $this->option . '&id=' . $this->publication->id . '&active=reviews';
	$view->display();
}
else
{
	echo '<p class="noresults">'.JText::_('PLG_PUBLICATION_REVIEWS_NO_REVIEWS_FOUND').'</p>'."\n";
}

// Display the review form if needed
if (!$juser->get('guest')) 
{
	$myreview = $this->h->myreview;
	if (is_object($myreview)) 
	{
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'publications',
				'element' => 'reviews',
				'name'    => 'review'
			)
		);
		$view->option      = $this->option;
		$view->review      = $this->h->myreview;
		$view->banking     = $this->banking;
		$view->infolink    = $this->infolink;
		$view->publication = $this->publication;
		$view->juser       = $juser;
		$view->display();
	}
}

