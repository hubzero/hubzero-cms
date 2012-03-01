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
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>
<div id="content-header-extra">
	<p>
		<a href="<?php echo JRoute::_('index.php?option='.$this->option); ?>">&larr; Main page</a>
	</p>
</div>
<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<div class="aside">
		<div class="container">
			<h3>Categories<span class="starter-point"></span></h3>
			<ul class="categories">
<?php
		$cls = '';
		if ($this->catid == 0) 
		{
			$cls = ' class="active"';
		}
		$html = "\t".'<li><a'.$cls.' href="'.JRoute::_('index.php?option='.$this->option.'&section=all').'">'.JText::_('All Articles').'</a></li>'."\n";
		if (count($this->categories) > 0) 
		{
			foreach ($this->categories as $row)
			{
				$html .= "\t".'<li><a ';
				if ($this->catid == $row->id) 
				{
					$html .= ' class="active"';
				}
				$html .= 'href="'.JRoute::_('index.php?option='.$this->option.'&section='.$row->alias).'">'.Hubzero_View_Helper_Html::xhtml($row->title).'</a> ('.$row->numitems.')'."\n";
				if (count($this->subcategories) > 0 && $this->catid == $row->id) 
				{
					$html .= "\t".'<ul class="categories">'."\n";
					foreach ($this->subcategories as $cat)
					{
						$html .= "\t\t".'<li><a ';
						if ($this->filters['category'] == $cat->id) 
						{
							$html .= ' class="active"';
						}
						$html .= 'href="'. JRoute::_('index.php?option='.$this->option.'&section='.$row->alias.'&category='. $cat->alias) .'">'. stripslashes($cat->title) .'</a> ('.$cat->numitems.')</li>'."\n";
					}
					$html .= "\t".'</ul>'."\n";
				}
				$html .= '</li>'."\n";
			}
		}
		echo $html;
?>
			</ul>
		</div><!-- / .container -->
	</div><!-- / .aside -->
	<div class="subject">
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&section='.$this->category->alias); ?>" method="post">
		
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="Search" />
				<fieldset class="entry-search">
					<input type="text" name="search" value="<?php echo htmlentities($this->filters['search'], ENT_COMPAT, 'UTF-8'); ?>" />
					<input type="hidden" name="order" value="<?php echo htmlentities($this->filters['order']); ?>" />
					<input type="hidden" name="section" value="<?php echo $this->category->alias; ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				</fieldset>
			</div><!-- / .container -->

			<div class="container">
				<ul class="entries-menu">
					<li><a<?php echo ($this->filters['order'] == 'popularity') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&section='.$this->category->alias.'&order=popularity'); ?>" title="Sort by most liked to least liked">&darr; Popular</a></li>
					<li><a<?php echo ($this->filters['order'] == 'recent') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&section='.$this->category->alias.'&order=recent'); ?>" title="Sort by newest to oldest">&darr; Recent</a></li>
				</ul>
	
				<table class="articles entries" summary="Articles">
<?php
$s = $this->filters['start']+1;
$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;
?>
					<caption>
<?php
if ($this->filters['search'] != '') 
{
	echo 'Search for "'.$this->filters['search'].'" in ';
}
?>
						<?php echo stripslashes($this->category->title); ?> 
						<span>(<?php echo $s.'-'.$e; ?> of <?php echo $this->total; ?>)</span>
					</caption>
					<tbody>
<?php
if (count($this->articles) > 0) 
{
	foreach ($this->articles as $row)
	{
		$link  = 'index.php?option='.$this->option.'&section='.$row->calias;
		$link .= ($row->ccalias) ? '&category='.$row->ccalias : '';
		$link .= ($row->alias) ? '&alias='. $row->alias : '&alias='. $row->id;
?>
						<tr>
							<th>
								<span class="entry-id"><?php echo $row->id; ?></span>
							</th>
							<td>
								<a class="entry-title" href="<?php echo JRoute::_($link); ?>"><?php echo stripslashes($row->title); ?></a><br />
								<span class="entry-details">
									Last updated @ 
									<span class="entry-time"><?php echo JHTML::_('date',$row->modified, '%I:%M %p', 0); ?></span> on 
									<span class="entry-date"><?php echo JHTML::_('date',$row->modified, '%d %b %Y', 0); ?></span>
								</span>
							</td>
							<td class="voting">
<?php
								$view = new JView(array('name'=>'vote'));
								$view->option = $this->option;
								$view->item = $row;
								$view->type = 'entry';
								$view->vote = '';
								$view->id = '';
								if (!$this->juser->get('guest')) 
								{
									if ($row->user_id == $this->juser->get('id')) 
									{
										$view->vote = $row->vote;
										$view->id = $row->id;
									}
								}
								$view->display();
?>
							</td>
						</tr>
<?php 
	}
}
?>
					</tbody>
				</table>
				<?php 
				$pagenavhtml = $this->pageNav->getListFooter();
				$pagenavhtml = str_replace('&amp;&amp;','&amp;',$pagenavhtml);
				$pagenavhtml = str_replace('?&amp;','?',$pagenavhtml);
				$pagenavhtml = str_replace('/kb/?','/kb/'.$this->category->alias.'/?',$pagenavhtml);
				echo $pagenavhtml;
				?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .main section -->
