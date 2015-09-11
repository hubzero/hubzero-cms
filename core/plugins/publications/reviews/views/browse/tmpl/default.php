<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

$database = App::get('db');

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
	<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS'); ?>
</h3>
<p class="section-options">
	<?php if (User::isGuest()) { ?>
			<a href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->publication->link('reviews') . '&action=addreview#reviewform'))); ?>" class="icon-add add btn">
				<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_WRITE_A_REVIEW'); ?>
			</a>
	<?php } else { ?>
			<a href="<?php echo Route::url($this->publication->link('reviews') . '&action=addreview#reviewform'); ?>" class="icon-add add btn">
				<?php echo Lang::txt('PLG_PUBLICATIONS_REVIEWS_WRITE_A_REVIEW'); ?>
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
	     ->set('publication', $this->publication)
	     ->set('comments', $this->reviews)
	     ->set('config', $this->config)
	     ->set('base', $this->publication->link('reviews'))
	     ->display();
}
else
{
	echo '<p class="noresults">'.Lang::txt('PLG_PUBLICATIONS_REVIEWS_NO_REVIEWS_FOUND').'</p>'."\n";
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
		     ->set('publication', $this->publication)
		     ->set('juser', User::getRoot())
		     ->display();
	}
}

