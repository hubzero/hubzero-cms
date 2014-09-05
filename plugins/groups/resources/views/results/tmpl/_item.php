<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'usage.php');

$database = JFactory::getDBO();

// Instantiate a helper object
$RE = new ResourcesHelper($this->row->id, $database);
$RE->getContributors();

// Get the component params and merge with resource params
$config = JComponentHelper::getParams('com_resources');

$rparams = new JRegistry($this->row->params);
$params = $config;
$params->merge($rparams);

// Set the display date
switch ($params->get('show_date'))
{
	case 0: $thedate = ''; break;
	case 1: $thedate = JHTML::_('date', $this->row->created,'d M Y');    break;
	case 2: $thedate = JHTML::_('date', $this->row->modified, 'd M Y');   break;
	case 3: $thedate = JHTML::_('date', $this->row->publish_up, 'd M Y'); break;
}

if (strstr($this->row->href, 'index.php'))
{
	$this->row->href = JRoute::_($this->row->href);
}
$juri = JURI::getInstance();

switch ($this->row->access)
{
	case 1: $cls = 'registered'; break;
	case 2: $cls = 'special';    break;
	case 3: $cls = 'protected';  break;
	case 4: $cls = 'private';    break;
	case 0:
	default: $cls = 'public'; break;
}
?>

<li class="<?php echo $cls; ?> resource">
	<p class="title"><a href="<?php echo $this->row->href; ?>"><?php echo $this->escape(stripslashes($this->row->title)); ?></a></p>

	<?php if ($params->get('show_ranking')) { ?>
		<?php
		$this->row->ranking = round($this->row->ranking, 1);

		$r = (10*$this->row->ranking);
		if (intval($r) < 10)
		{
			$r = '0' . $r;
		}
		?>
		<div class="metadata">
			<dl class="rankinfo">
				<dt class="ranking"><span class="rank-<?php echo $r; ?>"><?php echo JText::_('PLG_GROUPS_RESOURCES_THIS_HAS'); ?></span> <?php echo number_format($this->row->ranking, 1) . ' ' . JText::_('PLG_GROUPS_RESOURCES_RANKING'); ?></dt>
				<dd>
					<p><?php echo JText::_('PLG_GROUPS_RESOURCES_RANKING_EXPLANATION'); ?></p>
					<div>
						<?php
						$RE->getCitationsCount();
						$RE->getLastCitationDate();

						if ($this->row->category == 7)
						{
							$stats = new ToolStats($database, $this->row->id, $this->row->category, $this->row->rating, $RE->citationsCount, $RE->lastCitationDate);
						}
						else
						{
							$stats = new AndmoreStats($database, $this->row->id, $this->row->category, $this->row->rating, $RE->citationsCount, $RE->lastCitationDate);
						}
						echo $stats->display();
						?>
					</div>
				</dd>
			</dl>
		</div>
	<?php } elseif ($params->get('show_rating')) { ?>
		<?php
		switch ($this->row->rating)
		{
			case 0.5: $class = ' half-stars';      break;
			case 1:   $class = ' one-stars';       break;
			case 1.5: $class = ' onehalf-stars';   break;
			case 2:   $class = ' two-stars';       break;
			case 2.5: $class = ' twohalf-stars';   break;
			case 3:   $class = ' three-stars';     break;
			case 3.5: $class = ' threehalf-stars'; break;
			case 4:   $class = ' four-stars';      break;
			case 4.5: $class = ' fourhalf-stars';  break;
			case 5:   $class = ' five-stars';      break;
			case 0:
			default:  $class = ' no-stars';      break;
		}
		?>
		<div class="metadata">
			<p class="rating"><span class="avgrating<?php echo $class; ?>"><span><?php echo JText::sprintf('PLG_GROUPS_RESOURCES_OUT_OF_5_STARS', $this->row->rating); ?></span>&nbsp;</span></p>
		</div>
	<?php } ?>

	<p class="details">
		<?php echo $thedate; ?> <span>|</span> <?php echo stripslashes($this->row->area); ?>
		<?php if ($RE->contributors) { ?>
			<span>|</span> <?php echo JText::_('PLG_GROUPS_RESOURCES_CONTRIBUTORS') . ': ' . $RE->contributors; ?>
		<?php } ?>
	</p>

	<?php
	$text = $this->row->ftext;
	if ($this->row->itext)
	{
		$text = $this->row->itext;
	}
	$text = strip_tags($text);
	echo \Hubzero\Utility\String::truncate(\Hubzero\Utility\Sanitize::clean(stripslashes($text)), 200) . "\n";
	?>

	<p class="href"><?php echo $juri->base() . ltrim($this->row->href, DS); ?></p>
</li>