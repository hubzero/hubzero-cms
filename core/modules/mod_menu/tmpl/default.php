<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die;

$classnames = "menu{$class_sfx}";

if ($disclosureMenu)
{
	$classnames .= " main-nav-bar disclosure-nav";
}

$ariaControlTarget = '';

// Note. It is important to remove spaces between elements.
?>

<ul class="<?php echo $classnames ?>"<?php
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
		$rootLink = false;
		$parentLink = false;

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

		if ($item->level == 1)
		{
			$rootLink = true;
		}

		if ($item->deeper)
		{
			$class .= ' deeper';
		}

		if ($item->parent)
		{
			$parentLink = true;
			$class .= ' parent';
			$ariaControlTarget = 'aria-control-target-' . $item->id;
		}

		if (!empty($class))
		{
			$class = ' class="' . trim($class) . '"';
		}

		echo '<li' . $class . '>';

		$this->set('rootLink', $rootLink)
			 ->set('parentLink', $parentLink);
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
			echo '<ul id="' . $ariaControlTarget . '">';
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
