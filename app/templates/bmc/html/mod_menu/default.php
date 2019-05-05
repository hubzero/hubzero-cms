<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 * @purpose  	 The override adds the alias data attribute to the navigation items to be used with the tempalte's lookup code to map the current page to the main navigation subnav
 */

// No direct access.
defined('_HZEXEC_') or die;

// Note. It is important to remove spaces between elements.
?>

<ul class="menu<?php echo $class_sfx; ?>"<?php
	$tag = '';
	if ($params->get('tag_id') != null)
	{
		$tag = $params->get('tag_id').'';
		echo ' id="' . $tag . '"';
	}
?>>
	<?php
	foreach ($list as $i => &$item) :
		$class = 'item-' . $item->id;
		if ($item->id == $active_id)
		{
			$class .= ' current';
		}

		if (in_array($item->id, $path))
		{
			$class .= ' active';
		}
		elseif ($item->type == 'alias')
		{
			$aliasToId = $item->params->get('aliasoptions');
			if (count($path) > 0 && $aliasToId == $path[count($path)-1])
			{
				$class .= ' active';
			}
			elseif (in_array($aliasToId, $path))
			{
				$class .= ' alias-parent-active';
			}
		}

		if ($item->deeper)
		{
			$class .= ' deeper';
		}

		if ($item->parent)
		{
			$class .= ' parent';
		}

		if (!empty($class))
		{
			$class = ' class="' . trim($class) . '"';
		}

		// Add data attribute to identify the parent easily by the alias
		$dataAlias = '';
		if (!empty($item->alias))
		{
			$dataAlias = ' data-alias=' . strtolower($item->alias);
		}

		echo '<li' . $class . $dataAlias . '>';

		// Render the menu item.
		switch ($item->type) :
			case 'separator':
			case 'url':
			case 'component':
				require $this->getLayoutPath('default_' . $item->type);
			break;

			default:
				require $this->getLayoutPath('default_url');
			break;
		endswitch;

		// The next item is deeper.
		if ($item->deeper)
		{
			echo '<ul>';
		}
		// The next item is shallower.
		elseif ($item->shallower)
		{
			echo '</li>';
			echo str_repeat('</ul></li>', $item->level_diff);
		}
		// The next item is on the same level.
		else
		{
			echo '</li>';
		}
	endforeach;
	?>
</ul>
