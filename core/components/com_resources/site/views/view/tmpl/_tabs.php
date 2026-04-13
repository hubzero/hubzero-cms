<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$base = Request::get('tab_base_url', null) ? Request::get('tab_base_url') : 'index.php?option=' . $this->option;
$base .= '&' . ($this->resource->alias ? 'alias=' . $this->resource->alias : 'id=' . $this->resource->id);

$active_key = Request::get('tab_active_key', null) ? Request::get('tab_active_key') : 'active';

?>
<?php
$tabItems = array();
foreach ($this->cats as $cat)
{
	$name = key($cat);

	if (!$name)
	{
		continue;
	}

	$active = false;

	$url = $base . '&' . $active_key . '=' . $name;
	if (strtolower($name) == $this->active)
	{
		Pathway::append($cat[$name], $url);

		if ($active != 'about')
		{
			Document::setTitle(Document::getTitle() . ': ' . $cat[$name]);
		}

		$active = true;
	}

	$tabItems[] = array('name' => $name, 'label' => $cat[$name], 'url' => $url, 'active' => $active);
}

if (count($tabItems) > 0) { ?>
<ul id="sub-menu" class="sub-menu">
	<?php foreach ($tabItems as $tab) { ?>
		<li id="sm-<?php echo $tab['name']; ?>"<?php echo $tab['active'] ? ' class="active"' : ''; ?>>
			<a class="tab" data-rel="<?php echo $tab['name']; ?>" href="<?php echo Route::url($tab['url']); ?>"><span><?php echo $tab['label']; ?></span><span class="sr-only">: <?php echo $this->escape(stripslashes($this->resource->title)); ?></span></a>
		</li>
	<?php } ?>
</ul>
<?php } ?>
