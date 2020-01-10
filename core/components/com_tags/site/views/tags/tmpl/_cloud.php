<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

	if (!$this->tags->count())
	{
		echo '';
		return;
	}

	$min_font_size = 1;
	$max_font_size = 1.8;

	if ($this->config->get('show_sizes', 0) == 1)
	{
		$retarr = array();
		foreach ($tags as $tag)
		{
			$retarr[$tag->raw_tag] = $tag->count;
		}
		ksort($retarr);

		$max_qty = max(array_values($retarr));  // Get the max qty of tagged objects in the set
		$min_qty = min(array_values($retarr));  // Get the min qty of tagged objects in the set

		// For ever additional tagged object from min to max, we add $step to the font size.
		$spread = $max_qty - $min_qty;
		if (0 == $spread)
		{ // Divide by zero
			$spread = 1;
		}
		$step = ($max_font_size - $min_font_size)/($spread);
	}

	// build HTML
	$tll = array();
	foreach ($this->tags as $tag)
	{
		$class = '';
		switch ($tag->get('admin'))
		{
			case 1:
				$class = ' class=" admin"';
				break;
			case 2:
				$class = ' class=" core"';
				break;
		}

		if ($this->config->get('show_sizes', 0) == 2)
		{
			$tll[$tag->get('tag')] = '<li' . $class . '><a href="' . Route::url('index.php?option=com_tags&tag=' . $tag->get('tag')) . '" data-tag="' . $this->escape($tag->get('tag')) . '">' . $this->escape(stripslashes($tag->get('raw_tag'))) . ' <span>' . $tag->objects()->total() . '</span></a></li>';
		}
		else
		{
			$tll[$tag->get('tag')]  = '<li' . $class . '>';
			if ($this->config->get('show_sizes', 0) == 1)
			{
				$size = $min_font_size + ($tag->get('count') - $min_qty) * $step;

				$tll[$tag->get('tag')] .= '<span data-size="' . round($size, 1) . 'em">';
			}
			$link_class = '';
			switch ($tag->get('admin'))
			{
				case 1:
					$link_class = ' admin';
					break;
				case 2:
					$link_class = ' core';
					break;
			}
			$tll[$tag->get('tag')] .= '<a class="tag' . $link_class . '" href="' . Route::url('index.php?option=com_tags&tag=' . $tag->get('tag')) . '">' . $this->escape(stripslashes($tag->get('raw_tag')));
			if ($this->config->get('show_tag_count', 0))
			{
				$tll[$tag->get('tag')] .= ' <span>' . $tag->get('count') . '</span>';
			}
			$tll[$tag->get('tag')] .= '</a>';
			if ($this->config->get('show_sizes') == 1)
			{
				$tll[$tag->get('tag')] .= '</span>';
			}
			$tll[$tag->get('tag')] .= '</li>';
		}
	}
	if ($this->config->get('show_tags_sort', 'alpha') == 'alpha')
	{
		ksort($tll);
	}

	$html  = '<ol class="tags">' . "\n";
	$html .= implode("\n", $tll);
	$html .= '</ol>' . "\n";

	echo $html;
