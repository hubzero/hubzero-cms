<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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

$database = JFactory::getDBO();
$juser = JFactory::getUser();

// Instantiate a helper object
$helper = new ResourcesHelper($this->line->id, $database);
$helper->getContributors();
$helper->getContributorIDs();

/*
// Determine if they have access to edit
if (!$juser->get('guest'))
{
	if ((!$this->show_edit && $this->line->created_by == $juser->get('id'))
	 || in_array($juser->get('id'), $helper->contributorIDs))
	{
		$this->show_edit = 2;
	}
}
*/

// Get parameters
$params = clone($this->config);
$rparams = new JRegistry($this->line->params);
$params->merge($rparams);

if (!$this->line->modified || $this->line->modified == '0000-00-00 00:00:00')
{
	$this->line->modified = $this->line->created;
}
if (!$this->line->publish_up || $this->line->publish_up == '0000-00-00 00:00:00')
{
	$this->line->publish_up = $this->line->created;
}

// Set the display date
switch ($params->get('show_date'))
{
	case 0: $thedate = ''; break;
	case 1: $thedate = JHTML::_('date', $this->line->created, JText::_('DATE_FORMAT_HZ1'));    break;
	case 2: $thedate = JHTML::_('date', $this->line->modified, JText::_('DATE_FORMAT_HZ1'));   break;
	case 3: $thedate = JHTML::_('date', $this->line->publish_up, JText::_('DATE_FORMAT_HZ1')); break;
}

switch ($this->line->access)
{
	case 1: $cls = 'registered'; break;
	case 2: $cls = 'special';    break;
	case 3: $cls = 'protected';  break;
	case 4: $cls = 'private';    break;
	case 0:
	default: $cls = 'public';    break;
}

if ($this->config->get('supportedtag'))
{
	$cls .= ' supported';
}
?>

<li class="<?php echo $cls; ?>">
	<p class="title">
		<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&' . ($this->line->alias ? 'alias=' . $this->line->alias : 'id=' . $this->line->id)); ?>">
			<?php echo $this->escape(stripslashes($this->line->title)); ?>
		</a>
		<?php /*if ($this->show_edit != 0) {
			if ($this->line->published >= 0) {
				if ($this->line->type == 7) {
					$link = JRoute::_('index.php?option=com_tools&task=resource&step=1&app='. $this->line->alias);
				} else {
					$link = JRoute::_('index.php?option=com_resources&task=draft&step=1&id='. $this->line->id);
				}
				$html .= ' <a class="edit button" href="'. $link .'" title="'. JText::_('COM_RESOURCES_EDIT') .'">'. JText::_('COM_RESOURCES_EDIT') .'</a>';
			}
		}*/ ?>
	</p>

<?php if ($params->get('show_ranking')) { ?>
	<div class="metadata">
		<dl class="rankinfo">
			<dt class="ranking">
				<?php
				//$database = JFactory::getDBO();

				// Get statistics info
				$helper->getCitationsCount();
				$helper->getLastCitationDate();

				$this->line->ranking = round($this->line->ranking, 1);

				$r = (10 * $this->line->ranking);
				?>
				<span class="rank">
					<span class="rank-<?php echo $r; ?>" style="width: <?php echo $r; ?>%;"><?php echo JText::_('COM_RESOURCES_THIS_HAS'); ?></span>
				</span>
				<?php echo number_format($this->line->ranking, 1) . ' ' . JText::_('COM_RESOURCES_RANKING'); ?>
			</dt>
			<dd>
				<p><?php echo JText::_('COM_RESOURCES_RANKING_EXPLANATION'); ?></p>
				<div>
					<?php
					if ($this->line->type == 7)
					{
						$stats = new ToolStats($database, $this->line->id, $this->line->type, $this->line->rating, $helper->citationsCount, $helper->lastCitationDate);
					}
					else
					{
						$stats = new AndmoreStats($database, $this->line->id, $this->line->type, $this->line->rating, $helper->citationsCount, $helper->lastCitationDate);
					}
					echo $stats->display();
					?>
				</div>
			</dd>
		</dl>
	</div>
<?php } elseif ($params->get('show_rating')) { ?>
	<?php
	switch ($this->line->rating)
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
		default:  $class = ' no-stars';        break;
	}
	?>
	<div class="metadata">
		<p class="rating">
			<span title="<?php echo JText::sprintf('COM_RESOURCES_OUT_OF_5_STARS', $this->line->rating); ?>" class="avgrating<?php echo $class; ?>">
				<span><?php echo JText::sprintf('COM_RESOURCES_OUT_OF_5_STARS', $this->line->rating); ?></span>&nbsp;
			</span>
		</p>
	</div>
<?php } ?>
	<p class="details">
		<?php
		$info = array();
		if ($thedate)
		{
			$info[] = $thedate;
		}
		if (($this->line->type && $params->get('show_type')) || $this->line->standalone == 1)
		{
			$info[] = stripslashes($this->line->typetitle);
		}
		if ($helper->contributors && $params->get('show_authors'))
		{
			$info[] = JText::_('COM_RESOURCES_CONTRIBUTORS') . ': ' . $helper->contributors;
		}
		echo implode(' <span>|</span> ', $info);
		?>
	</p>
	<p>
		<?php
		$content = '';
		if ($this->line->introtext)
		{
			$content = $this->line->introtext;
		}
		else if ($this->line->fulltxt)
		{
			$content = $this->line->fulltxt;
			$content = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $content);
			$content = trim($content);
		}

		echo \Hubzero\Utility\String::truncate(strip_tags(\Hubzero\Utility\Sanitize::stripAll(stripslashes($content))), 300);
		?>
	</p>
</li>