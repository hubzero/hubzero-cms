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
defined('_JEXEC') or die('Restricted access');

$dateformat = '%d %b %Y';
$timeformat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateformat = 'd M Y';
	$timeformat = 'H:i p';
	$tz = true;
}

$offerings = $this->course->offerings(array('available' => true, 'sort' => 'publish_up'));
if ($offerings)
{
	$offering = $offerings->fetch('first');
}
else
{
	$offering = new CoursesModelOffering(0, $this->course->get('id'));
}

if ($offering->exists()) 
{
	$product = null;

	$url = 'index.php?option=' . $this->option . '&controller=offering&gid=' . $this->course->get('alias') . '&offering=' . $offering->get('alias') . '&task=enroll';
	if ($product && $product->pId)
	{
		$url = 'index.php?option=com_storefront/product/' . $product->pId;
	}
?>
		<div class="offering-info">
			<table>
				<tbody>
					<tr>
						<th scope="row">Starts</th>
						<td>
							<time datetime="<?php echo $offering->get('publish_up'); ?>"><?php echo JHTML::_('date', $offering->get('publish_up'), $dateformat, $tz); ?></time>
						</td>
					</tr>
					<tr>
						<th scope="row">Ends</th>
						<td>
							<time datetime="<?php echo $offering->get('publish_down'); ?>"><?php echo ($offering->get('publish_down') == '0000-00-00 00:00:00') ? JText::_('(never)') : JHTML::_('date', $offering->get('publish_down'), $dateformat, $tz); ?></time>
						</td>
					</tr>
				</tbody>
			</table>
		<?php if ($this->course->isManager() || $this->course->isStudent()) { ?>
			<p>
				<a class="outline btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=offering&gid=' . $this->course->get('alias') . '&offering=' . $offering->get('alias')); ?>">
					View outline
				</a>
			</p>
		<?php } else { ?>
			<p>
				<a class="enroll btn" href="<?php echo JRoute::_($url); ?>">
					Enroll
				</a>
			</p>
		<?php } ?>
		</div>
<?php }