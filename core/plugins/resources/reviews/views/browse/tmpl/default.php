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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

if (!isset($this->reviews) || !$this->reviews)
{
	$this->reviews = array();
}

foreach ($this->reviews as $k => $review)
{
	$this->reviews[$k] = new ResourcesModelReview($review);
}
$this->reviews = new \Hubzero\Base\ItemList($this->reviews);
?>
<h3 class="section-header">
	<?php echo Lang::txt('PLG_RESOURCES_REVIEWS'); ?>
</h3>

<p class="section-options">
	<?php if (User::isGuest()) { ?>
		<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=reviews&action=addreview#reviewform'))); ?>">
			<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_WRITE_A_REVIEW'); ?>
		</a>
	<?php } else { ?>
		<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=reviews&action=addreview#reviewform'); ?>">
			<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_WRITE_A_REVIEW'); ?>
		</a>
	<?php } ?>
</p>

<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>

<?php
if ($this->reviews->total() > 0)
{
	$this->view('_list')
	     ->set('parent', 0)
	     ->set('cls', 'odd')
	     ->set('depth', 0)
	     ->set('option', $this->option)
	     ->set('resource', $this->resource)
	     ->set('comments', $this->reviews)
	     ->set('config', $this->config)
	     ->set('base', 'index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=reviews')
	     ->display();
}
else
{
	echo '<p>' . Lang::txt('PLG_RESOURCES_REVIEWS_NO_REVIEWS_FOUND') . '</p>' . "\n";
}

// Display the review form if needed
if (!User::isGuest())
{
	if (isset($this->h->myreview) && is_object($this->h->myreview))
	{
		$this->view('default', 'review')
		     ->set('option', $this->option)
		     ->set('review', $this->h->myreview)
		     ->set('banking', $this->banking)
		     ->set('infolink', $this->infolink)
		     ->set('resource', $this->resource)
		     ->set('user', User::getRoot())
		     ->display();
	}
}
