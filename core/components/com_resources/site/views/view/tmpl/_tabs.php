<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$base = Request::get('tab_base_url', null) ? Request::get('tab_base_url') : 'index.php?option=' . $this->option;
$base .= '&' . ($this->resource->alias ? 'alias=' . $this->resource->alias : 'id=' . $this->resource->id);

$active_key = Request::get('tab_active_key', null) ? Request::get('tab_active_key') : 'active';

?>
<ul id="sub-menu" class="sub-menu">
	<?php
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
		?>
		<li id="sm-<?php echo $name; ?>"<?php echo $active ? ' class="active"' : ''; ?>>
			<a class="tab" data-rel="<?php echo $name; ?>" href="<?php echo Route::url($url); ?>"><span><?php echo $cat[$name]; ?></span></a>
		</li>
		<?php
	}
	?>
</ul>
