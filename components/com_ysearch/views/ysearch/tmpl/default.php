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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Push some resources to the tmeplate
ximport('Hubzero_Document');
Hubzero_Document::addComponentStyleSheet('com_ysearch');
Hubzero_Document::addComponentScript('com_ysearch', 'assets/js/ysearch');

$show_weight = array_key_exists('show_weight', $_GET);
?>
<div id="content-header" class="full">
	<h2><?php echo JText::_('Search'); ?></h2>
</div><!-- / #content-header -->

<div class="main section">
	<div class="aside">
		<div class="container">
			<h3>
				Filter results
			</h3>
		<?php if ($this->results->get_total_count()): ?>
			<ul class="sub-nav">
				<li>
					<?php if ($this->plugin): ?>
						<a href="/ysearch/?terms=<?php echo $this->url_terms; ?>">All Categories <span class="item-count"><?php echo $this->results->get_total_count(); ?></span></a>
					<?php else: ?>
						<strong>All Categories <span class="item-count"><?php echo $this->results->get_total_count(); ?></span></strong>
					<?php endif; ?>
				</li>
			<?php foreach ($this->results->get_result_counts() as $cat=>$def): ?>
				<?php if ($def['count']): ?>
					<li>
						<?php if ($this->plugin == $cat && !$this->section): ?>
							<strong><?php echo $def['friendly_name']; ?> <span class="item-count"><?php echo $def['count']; ?></span></strong>
						<?php else: ?> 
							<a href="/ysearch/?terms=<?php echo $cat . ':' . $this->url_terms ?>"><?php echo $def['friendly_name']; ?> <span class="item-count"><?php echo $def['count']; ?></span></a>
						<?php endif; ?>
						<?php 
						$fc_child_flag = 'plgYSearch'.$def['plugin_name'].'::FIRST_CLASS_CHILDREN';
						if ((!defined($fc_child_flag) || constant($fc_child_flag)) && array_key_exists('sections', $def) && count($def['sections']) > 1):
						?>
							<ul>
							<?php foreach ($def['sections'] as $section_key=>$sdef): ?>
								<?php 
								if (!$this->plugin || !$this->section || $cat != $this->plugin || $this->section != $section_key):
								?>
									<li><a href="/ysearch/?terms=<?php echo $cat . ':' . $section_key . ':' . $this->url_terms ?>"><?php echo $sdef['name']; ?> <span class="item-count"><?php echo $sdef['count']; ?></span></a></li>
								<?php else: ?>
									<li><strong><?php echo $sdef['name']; ?> <span class="item-count"><?php echo $sdef['count']; ?></span></strong></li>
								<?php endif; ?>
							<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endif; ?>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		</div><!-- / .container -->
	</div><!-- / .aside -->
	<div class="subject">
		<form action="/ysearch/" method="get" class="container data-entry">
			<fieldset>
				<p>
					<input type="submit" value="Search" class="search-submit" />
					<label id="search-terms" for="terms">Search terms</label>
					<input type="text" name="terms" id="terms" <?php $this->attr('value', $this->terms) ?>/>
				</p>
			</fieldset>
		</form>
<?php if ($this->results->valid()) : ?>
	<?php if (($ct = $this->results->get_custom_title())): ?>
		<p class="information">You are viewing <strong><?php echo $ct; ?></strong> matching your query. <a href="/ysearch/?terms=<?php echo urlencode($this->terms); ?>&amp;force-generic=1">View all results.</a></p>
	<?php endif; ?>

	<div class="container">
<?php
		$total = $this->results->get_plugin_list_count();
		$offset = $this->results->get_offset();
		$limit = $this->results->get_limit(); 
		$limit = ($limit == 0) ? ($limit+1) : $limit;
		$current_page = $offset / $limit + 1;
		$total_pages = ceil($total / $limit);
?>
		<h3>
			Results
			<span>(page <?php echo $current_page; ?> of <?php echo $total_pages; ?>)</span>
		</h3>
	<?php if (($tags = $this->results->get_tags())): ?>
		<ol class="tags">
			<?php foreach ($tags as $tag): ?>
			<li><a href="<?php echo $tag->get_link(); ?>"><?php echo $tag->get_title(); ?></a></li>
			<?php endforeach; ?>
		</ol>
	<?php endif; ?>

	<?php foreach ($this->results->get_widgets() as $widget): ?>
		<?php echo $widget; ?>
	<?php endforeach; ?>

	<ol class="results">
	<?php foreach ($this->results as $res) : ?>
		<li>
			<p class="title"><a <?php $this->attr('href', $res->get_link()); ?>><?php echo $res->get_highlighted_title(); ?></a></p>
			<?php $before = $this->app->triggerEvent('onBeforeYSearchRender' . $res->get_plugin(), array($res)); ?>
			<div class="summary">
				<?php if ($res->has_metadata()): ?>
					<p class="details">
						<?php if (($section = $res->get_section())) {?><span class="section"><?php echo $section; ?></span><?php }?>
						<?php if (($date = $res->get_date())) { ?><span class="date"><?php echo date('j M Y', $date); ?></span><?php } ?>
						<?php if (($contributors = $res->get_contributors())): ?>
						<span class="contributors">
								<?php 
									$contrib_ids = $res->get_contributor_ids();
									$contrib_len = count($contributors);
								?>
								Contributor(s): 
								<?php foreach ($contributors as $idx=>$contrib): ?>
									<a href="/members/<?php echo $contrib_ids[$idx]; ?>"><?php echo $contrib; ?></a><?php if ($idx != $contrib_len - 1) echo ', '; ?>
								<?php endforeach; ?>
						</span>
						<?php endif; ?>
					</p>
				<?php endif; ?>
				<?php if ($before): ?>
					<div class="result-pre">
						<?php foreach ($before as $html): ?>
							<?php echo $html; ?>
						<?php endforeach; ?>
					</div><!-- / .result-pre -->
				<?php endif; ?>
				<?php if ($show_weight): ?>
					<ul class="weight-log">
						<?php foreach ($res->get_weight_log() as $entry): ?>
							<li><?php echo $entry; ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<div class="result-description<?php echo $before ? ' shifted' : ''; ?>">
					<p><?php echo $res->get_highlighted_excerpt(); ?></p>
				</div><!-- / .result-description<?php echo $before ? ' shifted' : ''; ?> -->
				<p class="clear"></p>
			</div><!-- / .summary -->
			<?php 
				$last_type = NULL;
				if (($children = $res->get_children())):
					$ctypec = array();
					foreach ($children as $child)
					{
						if (($section = $child->get_section()))
							if (!array_key_exists($section, $ctypec))
								$ctypec[$section] = 1;
							else
								++$ctypec[$section];
					}
					if ($ctypec):
				?>
				<ul class="child-types">
					<?php foreach ($children as $idx=>$child): ?>
						<?php 
							if (!($current_type = $child->get_section()))
								continue;
							if (!$last_type):
						?>
							<li>
								<h4><span class="expand"></span><?php echo $current_type == 'Questions' ? 'Answers' : $current_type; ?> <small>(<?php echo $ctypec[$child->get_section()]; ?>)</small></h4>
								<ul class="child-result">
						<?php elseif ($last_type != $current_type): ?>
								</ul>
							</li>
							<li>
								<h4><span class="expand"></span><?php echo $current_type; ?> <small>(<?php echo $ctypec[$child->get_section()]; ?>)</small></h4>
								<ul class="child-result">
						<?php 
							endif;
							$last_type = $current_type;
						?>
							<li class="<?php echo $idx&1 ? 'odd' : 'even'; ?>">
								<a href="<?php echo $child->get_link(); ?>"><?php echo $child->get_highlighted_title(); ?></a>
								<p><?php echo $child->get_highlighted_excerpt(); ?></p>
							</li>
					<?php endforeach; ?>
						</ul>
					</li>
				</ul>
				<?php endif; ?>
			<?php endif; ?>
	  		<p class="url"><a href="<?php echo $res->get_link(); ?>"><?php echo $res->get_link(); ?></a></p>
		</li>
	<?php endforeach; ?>
	</ol>
	<form action="/ysearch/" method="get">
	<?php 
	$this->pagination->setAdditionalUrlParam('terms', $this->terms);
	echo $this->pagination->getListFooter(); ?>
		<input type="hidden" name="terms" <?php $this->attr('value', $this->terms) ?>/>
	<div class="clearfix"></div>
	</form>
</div><!-- / .container -->
<?php elseif (($raw = $this->terms->get_raw())): ?>
	<p>No results were found for '<?php echo $this->escape($raw); ?>'</p>
	<?php 
		# raw terms were specified but no chunks were parsed out, meaning they were all stop words, so we can give a quasi-helpful explanation of why nothing turned up
		if (!$this->terms->any() || strlen($raw) <= 3):
	?>
		<p><em>Note: Due to technical limitations, we are unable to search the site for very common or very short words.</em></p>
	<?php endif; ?>
<?php endif; ?>
</div><!-- / .subject -->
</form>
</div><!-- / .main section -->

