<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$id = str_replace('resource-', '', $this->result['id']);
$extras = Event::trigger('resources.onResourcesList', array($id));
?>

<div class="result <?php echo isset($this->result['access_level']) ? $this->result['access_level'] : 'public'; ?>" id="<?php echo $this->result['id']; ?>">
	<div class="result-body">
		<!-- Cateogory : mandatory -->
		<span class="result-category"><?php echo ucfirst($this->result['hubtype']); ?></span>
		<br/>
		<div class="result-subtype">
			<?php if (isset($this->result['type'])): ?>
				<span class="result-category"><?php echo $this->result['type']; ?></span>
			<?php endif; ?>
			<div class="result-badges">
				<span class="tags">
					<?php if (isset($this->result['_childDocuments_'])): ?>
						<?php foreach ($this->result['_childDocuments_'] as $index => $badge): ?>
							<?php if (isset($badge['badge_b']) && $badge['badge_b']): ?>
								<?php $description = !empty($badge['description']) ? $badge['description'] : $badge['title'][0];?>
								<a class="tag" href="<?php echo Route::url('index.php?option=com_search&terms=' . $this->terms . '&tags=' . $description); ?>" data-tag="<?php echo $description; ?>">
									<?php echo $badge['title'][0]; ?>
								</a>
								<?php unset($this->result['_childDocuments_'][$index]); ?>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				</span>
			</div>
		</div>

		<!-- Title : mandatory -->
		<h3 class="result-title"><a href="<?php echo $this->result['url']; ?>"><b><!-- highlight portion --></b><?php echo $this->result['title']; ?></a></h3>

		<div class="result-extras">
			<?php if (!empty($extras)) : ?>
				<?php echo implode("\n", $extras); ?>
			<?php endif; ?>

			<?php if (isset($this->result['date'])): ?>
				<?php $date = new \Hubzero\Utility\Date($this->result['date']); ?>
				<span class="result-timestamp"><time datetime="<?php echo $this->result['date']; ?>"><?php echo $date->toLocal('Y-m-d h:mA'); ?></time></span>
			<?php endif; ?>
			<span class="result-citation">
				<?php
				$fullCitation = '';
				$fullCitation .= !empty($this->result['authorString']) ? $this->result['authorString'] : '';
				$year = !empty($this->result['yearofpublication_s']) ? Date::of($this->result['yearofpublication_s'])->year : '';
				$fullCitation .= !empty($year) ? ' ('. $year . '). ' : '. ';
				$title = !empty($this->result['title']) ? $this->result['title'] : '';
				$title = in_array(substr(trim($title), -1), array('.', '?', '!')) ? $title : $title . '.';
				$fullCitation .= $title . ' ';
				$journalTitle = !empty($this->result['journaltitle_s']) ? $this->result['journaltitle_s'] : '';
				$journalTitle = in_array(substr(trim($journalTitle), -1), array('.', '?', '!')) || empty($journalTitle) ? $journalTitle : $journalTitle . '.';
				$fullCitation .= '<em>' . $journalTitle . '</em> ';
				$fullCitation .= !empty($this->result['volumeno_s']) ? '<em>' . $this->result['volumeno_s']  . '</em>' : '';
				$issueNumber = '';
				if (!empty($this->result['issuenomonth_s']))
				{
					$issueNumber = $this->result['issuenomonth_s'];
					if (!is_numeric($issueNumber))
					{
						$subStrNum = strpos($issueNumber, '/');
						$issueNum = substr($issueNumber, 0, $subStrNum);
					}
					$issueNumber = !empty($issueNum) ? $issueNum : $issueNumber;
					$issueNumber = '(' . $issueNumber . ').';
				}
				$fullCitation .= $issueNumber;
				$fullCitation .= !empty($this->result['pagenumbers_s']) ? ' pp. ' . $this->result['pagenumbers_s'] . '.' : '';

				echo $fullCitation;
				?>
			</span>
		</div>

		<?php if (isset($this->result['snippet']) && $this->result['snippet'] != '…'): ?>
			<!-- Snippet : mandatory -->
			<div class="result-snippet">
				<?php echo $this->result['snippet']; ?>
			</div><!-- end result snippet -->
		<?php endif; ?>

		<?php if (isset($this->result['_childDocuments_']) && $this->tagSearch): ?>
			<!-- Tags -->
			<div class="result-tags">
				<ul class="tags">
					<?php foreach ($this->result['_childDocuments_'] as $tag): ?>
						<?php if (!empty($tag['title'][0])): ?>
						<li>
							<?php $description = !empty($tag['description']) ? $tag['description'] : $tag['title'][0]; ?>
							<a class="tag" href="<?php echo Route::url('index.php?option=com_search&terms=' . $this->terms . '&tags=' . $description); ?>" data-tag="<?php echo $description; ?>">
								<?php echo $tag['title'][0]; ?>
							</a>
						</li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php elseif (isset($this->result['tags'])): ?>
			<!-- Tags -->
			<div class="result-tags">
				<ul class="tags">
					<?php foreach ($this->result['tags'] as $tag): ?>
						<li>
							<a class="tag" href="<?php echo Route::url('index.php?option=com_search&terms=' . $tag); ?>">
								<?php echo $tag; ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<!-- Result URL -->
		<div class="result-url">
			<a href="<?php echo $this->result['url']; ?>"><?php echo $this->result['url']; ?></a>
		</div>

		<?php if (User::authorise('core.admin') && isset($this->result['access_level'])): ?>
			<!-- Access -->
			<div class="result-extras">
				<span class="result-access">
					Access: <?php echo $this->result['access_level']; ?>
				</span>
			</div>
		<?php endif; ?>
	</div> <!-- End Result Body -->
</div> <!-- End Result -->
