<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die;

$rootLink = $this->get('rootLink');
$parentLink = $this->get('parentLink');

if ($rootLink)
{
	$item->anchor_css = $item->anchor_css ? $item->anchor_css . ' main-link' : 'main-link';
}

// Note. It is important to remove spaces between elements.
$class = $item->anchor_css   ? 'class="' . $item->anchor_css . '" '   : '';
$title = $item->anchor_title ? 'title="' . $item->anchor_title . '" ' : '';
if ($item->menu_image)
{
	$item->params->get('menu_text', 1 ) ?
		$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" /><span class="image-title">' . $item->title . '</span> ' :
		$linktype = '<img src="' . $item->menu_image . '" alt="' . $item->title . '" />';
}
else
{
	$linktype = $item->title;
}

if ($disclosureMenu)
{
echo '<div class="inner">';
}

switch ($item->browserNav) :
	default:
	case 0:
		?><a <?php echo $class; ?>href="<?php echo $item->flink; ?>" <?php echo $title; ?>><?php echo $linktype; ?></a><?php
		break;
	case 1:
		// _blank
		?><a <?php echo $class; ?>href="<?php echo $item->flink; ?>" rel="noopener" target="_blank" <?php echo $title; ?>><?php echo $linktype; ?></a><?php
		break;
	case 2:
	// window.open
		?><a <?php echo $class; ?>href="<?php echo $item->flink; ?>" onclick="window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes');return false;" <?php echo $title; ?>><?php echo $linktype; ?></a><?php
		break;
endswitch;

if ($parentLink && $disclosureMenu)
{
	?><button type="button" aria-expanded="false" aria-controls="<?php echo $ariaControlTarget; ?>" aria-label="More pages for: <?php echo $linktype; ?>"> </button><?php
}

if ($disclosureMenu)
{
echo '</div>';
}

