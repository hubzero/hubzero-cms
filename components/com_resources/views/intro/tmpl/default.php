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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="introduction" class="section">
	<div class="aside">
		<p id="getstarted"><a href="<?php echo JRoute::_('index.php?option=com_contribute&task=start'); ?>"><?php echo JText::_('Submit a resource'); ?></a></p>
		<ul>
			<li><a href="/kb/resources/faq"><?php echo JText::_('Resources FAQ'); ?></a></li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
		<div class="two columns first">
			<h3><?php echo JText::_('What are resources?'); ?></h3>
			<p><?php echo JText::_('Resources are user-submitted pieces of content that range from video presentations to publications to simulation tools.'); ?></p>
		</div>
		<div class="two columns second">
			<h3><?php echo JText::_('Who can submit a resource?'); ?></h3>
			<p><?php echo JText::_('Anyone can submit a resource! Resources must be relevant to the community and may undergo a short approval process to ensure all appropriate files and information are included.'); ?></p>
		</div>
		<div class="clear"></div>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / #introduction.section -->

<div class="section">
	
	<div class="four columns first">
		<h2><?php echo JText::_('Find a resource'); ?></h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="two columns first">
			<form action="/search" method="get" class="search">
				<fieldset>
					<p>
						<label for="rsearch"><?php echo JText::_('Keyword or phrase:'); ?></label>
						<input type="text" name="terms" id="rsearch" value="" />
						<input type="hidden" name="domains[]" value="resources" />
						<input type="submit" value="<?php echo JText::_('Search'); ?>" />
					</p>
				</fieldset>
			</form>
		</div><!-- / .two columns first -->
		<div class="two columns second">
			<div class="browse">
				<p><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo JText::_('Browse the list of available resources'); ?></a></p>
			</div><!-- / .browse -->
		</div><!-- / .two columns second -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>

<?php
if ($this->categories) {
?>
	<div class="four columns first">
		<h2><?php echo JText::_('Categories'); ?></h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
<?php
	$i = 0;
	$clm = '';

	foreach ($this->categories as $category)
	{
		$i++;
		switch ($clm)
		{
			case 'second': $clm = 'third'; break;
			case 'first': $clm = 'second'; break;
			case '':
			default: $clm = 'first'; break;
		}

		if (substr($category->alias, -3) == 'ies') {
			$cls = $category->alias;
		} else {
			$cls = rtrim($category->alias, 's');
		}
?>
		<div class="three columns <?php echo $clm; ?>">
			<div class="<?php echo $cls; ?>">
				<h3>
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&type=' . $category->alias); ?>">
						<?php echo $this->escape(stripslashes($category->type)); ?>
					</a>
				</h3>
				<p>
					<?php echo $this->escape(stripslashes($category->description)); ?>
				</p>
				<p>
					<a class="read-more" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&type=' . $category->alias); ?>" title="<?php echo JText::sprintf('Browse %s', $this->escape(stripslashes($category->type))); ?>">
						<?php echo JText::sprintf('Browse <span>%s </span>&rsaquo;', $this->escape(stripslashes($category->type))); ?>
					</a>
				</p>
			</div>
		</div><!-- / .three columns <?php echo $clm; ?> -->
<?php
		if ($clm == 'third') {
			echo '<div class="clear"></div>';
			$clm = '';
			$i = 0;
		}
	}
	if ($i == 1) {
		?>
		<div class="three columns second">
			<p> </p>
		</div><!-- / .three columns second -->
		<?php
	}
	if ($i == 1 || $i == 2) {
		?>
		<div class="three columns third">
			<p> </p>
		</div><!-- / .three columns third -->
		<?php
	}
?>
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>
<?php
}
?>

</div><!-- / .section -->

