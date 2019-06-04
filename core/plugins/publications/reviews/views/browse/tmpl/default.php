<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
		     ->display();
	}
}

