<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
		<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=reviews&action=addreview#commentform'))); ?>">
			<?php echo Lang::txt('PLG_RESOURCES_REVIEWS_WRITE_A_REVIEW'); ?>
		</a>
	<?php } else if (!$this->isAuthor) { ?>
		<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=reviews&action=addreview#commentform'); ?>">
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
?>

<div class="customfields">
	<?php
		// Parse for <nb:field> tags
		$type = $this->resource->type;

		$data = array();
		preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->resource->fulltxt, $matches, PREG_SET_ORDER);
		if (count($matches) > 0)
		{
			foreach ($matches as $match)
			{
				$data[$match[1]] = str_replace('="/site', '="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $match[2]);
			}
		}
		include_once Component::path('com_resources') . DS . 'models' . DS . 'elements.php';
		$elements = new \Components\Resources\Models\Elements($data, $this->resource->type->customFields);
		$schema = $elements->getSchema();
		$tab = Request::getCmd('active', 'reviews');  // The active tab (section)

		if (is_object($schema))
		{
			if (!isset($schema->fields) || !is_array($schema->fields))
			{
				$schema->fields = array();
			}
			foreach ($schema->fields as $field)
			{
				if (isset($data[$field->name]))
				{
					if ($elements->display($field->type, $data[$field->name]) && isset($field->display) && $field->display == $tab )
					{
						?>
						<h4><?php echo $field->label; ?></h4>
						<div class="resource-content">
						<?php echo $elements->display($field->type, $data[$field->name]); ?>
						</div>
						<?php
					}
				}
			}
		}
	?>
</div><!-- / .customfields -->
