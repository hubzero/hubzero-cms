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
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>
<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode("\n", $this->getErrors()); ?></p>
<?php } ?>
	<div class="aside">
		<div class="container">
			<h3>Community Help</h3>
			<p class="starter"><span class="starter-point"></span>
				Can't find something in the Knowledge Base? Try the community <a href="<?php echo JRoute::_('index.php?option=com_answers'); ?>">Questions &amp; Answers</a> and see if it has already been addressed by the community.
			</p>
		</div><!-- / .container -->
		<div class="container">
			<h3>Feature Requests</h3>
			<p class="starter"><span class="starter-point"></span>
				Have an idea or feature request? <a href="<?php echo JRoute::_('index.php?option=com_wishlist'); ?>">Let us know!</a>
			</p>
		</div><!-- / .container -->
		<div class="container">
			<h3>Trouble Report</h3>
			<p class="starter"><span class="starter-point"></span>
				Found a bug? <a href="<?php echo JRoute::_('index.php?option=com_support&controller=tickets&task=new'); ?>">Let us know!</a>
			</p>
		</div><!-- / .container -->
	</div><!-- / .aside -->
	<div class="subject">
		<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&section=all'); ?>" method="post">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('Search'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo JText::_('Search for articles'); ?></legend>
					<label for="entry-search-field"><?php echo JText::_('Enter keyword or phrase'); ?></label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Enter keyword or phrase'); ?>" />
					<input type="hidden" name="order" value="<?php echo $this->escape($this->filters['order']); ?>" />
					<input type="hidden" name="section" value="all" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				</fieldset>
			</div><!-- / .container -->

			<div class="container">
				<div class="container-block">
					<h3>Articles</h3>
					<div class="grid">
						<div class="col span-half omega">
							<h4><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=article&section=all&order=popularity'); ?>"><?php echo JText::_('Most Popular Articles'); ?> <span class="more">&raquo;</span></a></h4>
							<?php
							if (count($this->articles['top']) > 0) {
								$html  = "\t".'<ul class="articles">'."\n";
								foreach ($this->articles['top'] as $row)
								{
									if (!empty($row->alias)) {
										$link_on = JRoute::_('index.php?option=' . $this->option . '&task=article&section='.$row->section.'&category='.$row->category.'&alias='.$row->alias);
									} else {
										$link_on = JRoute::_('index.php?option=' . $this->option . '&task=article&section='.$row->section.'&category='.$row->category.'&id='.$row->id);
									}
									$html .= "\t\t".'<li><a href="'. $link_on .'" title="'.JText::_('COM_KB_READ_ARTICLE').'">'.$this->escape(stripslashes($row->title)).'</a></li>'."\n";
								}
								$html .= "\t".'</ul>'."\n";
							} else {
								$html  = "\t".'<p>'.JText::_('No articles found.').'</p>'."\n";
							}
							echo $html;
							?>
						</div><!-- / .col span-half -->
						<div class="col span-half omega">
							<h4><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=article&section=all&order=recent'); ?>"><?php echo JText::_('Most Recent Articles'); ?> <span class="more">&raquo;</span></a></h4>
							<?php
							if (count($this->articles['new']) > 0) {
								$html  = "\t".'<ul class="articles">'."\n";
								foreach ($this->articles['new'] as $row)
								{
									if (!empty($row->alias)) {
										$link_on = JRoute::_('index.php?option=' . $this->option . '&task=article&section='.$row->section.'&category='.$row->category.'&alias='.$row->alias);
									} else {
										$link_on = JRoute::_('index.php?option=' . $this->option . '&task=article&section='.$row->section.'&category='.$row->category.'&id='.$row->id);
									}
									$html .= "\t\t".'<li><a href="'. $link_on .'" title="'.JText::_('COM_KB_READ_ARTICLE').'">'.$this->escape(stripslashes($row->title)).'</a></li>'."\n";
								}
								$html .= "\t".'</ul>'."\n";
							} else {
								$html  = "\t".'<p>'.JText::_('No articles found.').'</p>'."\n";
							}
							echo $html;
							?>
						</div><!-- / .col span-half -->
					</div><!-- / .grid -->

					<h3>Categories</h3>
<?php 
		$i = 0;
		$html = '';
		if (count($this->categories) > 0) {
			// Get the list of articles for this category
			$kba = new KbArticle($this->database);

			$filters = array();
			$filters['limit'] = JRequest::getInt('limit', 3);
			$filters['start'] = JRequest::getInt('limitstart', 0);
			$filters['order'] = JRequest::getWord('order', 'recent');
			$filters['category'] = 0;
			$filters['search'] = JRequest::getVar('search','');
			$filters['state'] = 1;

			foreach ($this->categories as $row)
			{
				$i++;

				switch ($i)
				{
					case 1: $cls = 'first';  break;
					case 2: $cls = 'second'; break;
					case 3: $cls = 'third';  break;
				}

				$html .= "\t\t".'<div class="two columns '.$cls.'">'."\n";
				$html .= "\t\t\t".'<h4><a href="'.JRoute::_('index.php?option=' . $this->option . '&section='. $row->alias) .'">'. $this->escape(stripslashes($row->title)) .' <span>('.$row->numitems.')</span> <span class="more">&raquo;</span></a></h4>'."\n";
				/*if ($row->description) {
					$html .= '<p>'.Hubzero_View_Helper_Html::shortenText($row->description, 100, 0).'</p>'."\n";
				}*/
				$filters['section'] = $row->id;

				// Get the records
				$articles = $kba->getRecords($filters);
				if (count($articles) > 0) {
					$html .= "\t".'<ul class="articles">'."\n";
					foreach ($articles as $article)
					{
						if (!empty($article->alias)) {
							$link_on = JRoute::_('index.php?option=' . $this->option . '&task=article&section='.$article->calias.'&category='.$article->ccalias.'&alias='.$article->alias);
						} else {
							$link_on = JRoute::_('index.php?option=' . $this->option . '&task=article&section='.$article->calias.'&category='.$article->ccalias.'&id='.$article->id);
						}
						$html .= "\t\t".'<li><a href="'. $link_on .'">'. $this->escape(stripslashes($article->title)) . '</a></li>'."\n";
					}
					$html .= "\t".'</ul>'."\n";
					/*if ($row->numitems > $filters['limit']) {
						$html .= '<p class="more-entries"><a href="'.JRoute::_('index.php?option=' . $this->option . '&section='. $row->alias) .'">More<span> articles in '. stripslashes($row->title) .'</span> &raquo;</a></p>'."\n";
					}*/
				} else {
					$html .= "\t".'<p>'.JText::_('No articles found.').'</p>'."\n";
				}
				$html .= "\t\t".'</div><!-- / .two columns '.$cls.' -->'."\n";
				$html .= ($i >= 2) ? '<div class="clearfix"></div>' : '';

				if ($i >= 2) {
					$i = 0;
				}
			}
		}
		echo $html;
?>
					<div class="clear"></div>
				</div><!-- / .container-block -->
			</div><!-- / .container -->
		</form>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / .main section -->
