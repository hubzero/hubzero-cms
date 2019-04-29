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
?>
<h3 class="section-header">
	<?php echo Lang::txt('PLG_RESOURCES_REVIEWS'); ?>
</h3>

<p class="section-options">
	<?php if (User::isGuest()) { ?>
		<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=reviews&action=addreview#reviewform'))); ?>">
			<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_WRITE_A_REVIEW'); ?>
		</a>
	<?php } else if (!$this->isAuthor) { ?>
		<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=reviews&action=addreview#reviewform'); ?>">
			<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_WRITE_A_REVIEW'); ?>
		</a>
	<?php } ?>
</p>

<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>

<?php
if ($this->reviews->count() > 0)
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
	echo '<div class="results-none"><p>' . Lang::txt('PLG_RESOURCES_REVIEWS_NO_REVIEWS_FOUND') . '</p></div>' . "\n";
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
		     ->display();
	}
}
