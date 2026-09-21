<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// Nearly every indexer writes 'url' with Route::urlForClient(), whose $xhtml
// argument defaults to true, so the stored value can already contain &amp;
// (com_groups is the exception -- it builds root . 'groups/' . cn by hand).
// Escaping it again would emit &amp;amp; and break the query string wherever
// SEF is off, hence $double_encode = false on the url sinks below rather than
// escape(); that still encodes a literal &, <, > or " in a value from any
// indexer, so nothing is left unescaped either way.
?>

<div class="result <?php echo $this->escape(isset($this->result['access_level']) ? $this->result['access_level'] : 'public'); ?>" id="<?php echo $this->escape($this->result['id']); ?>">
<div class="result-body">
	<!-- Title : mandatory -->
	<h3 class="result-title"><a href="<?php echo htmlspecialchars((string) $this->result['url'], ENT_COMPAT, 'UTF-8', false); ?>" rel="nofollow"><b><!-- highlight portion --></b><?php echo $this->escape($this->result['title']); ?></a></h3>

	<div class="result-extras">
		<!-- Cateogory : mandatory -->
		<span class="result-category"><?php echo $this->escape(ucfirst($this->result['hubtype'])); ?></span>

		<?php if (isset($this->result['date'])): ?>
			<?php $date = new \Hubzero\Utility\Date($this->result['date']); ?>
			<span class="result-timestamp"><time datetime="<?php echo $this->escape($this->result['date']); ?>"><?php echo $date->toLocal('Y-m-d h:mA'); ?></time></span>
		<?php endif; ?>

		<?php if (isset($this->result['author'])): ?>
			<!-- Authors -->
			<span class="result-authors">
				<span class="result-author"><?php echo $this->escape($this->result['authorString']); ?></span>
			</span>
		<?php endif; ?>

		<?php if (User::authorise('core.admin') && isset($this->result['access_level'])): ?>
			<!-- Access -->
			<span class="result-access">
				Access: <?php echo $this->escape($this->result['access_level']); ?>
			</span>
		<?php endif; ?>
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
				<?php 
					$baseTagUrl = Route::url('index.php?option=com_search&terms=' . $this->terms);
				?>
				<?php foreach ($this->result['_childDocuments_'] as $tag): ?>
					<?php if (!empty($tag['title'][0])): ?>
					<li>
						<?php $description = !empty($tag['description']) ? $tag['description'] : $tag['title'][0];?>
						<a class="tag" href="<?php echo $baseTagUrl . '&tags=' . urlencode($description);?>" data-tag="<?php echo $this->escape($description);?>">
							<?php echo $this->escape($tag['title'][0]); ?>
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
					<li><a class="tag" href="<?php echo Route::url('index.php?option=com_search&terms=' . urlencode($tag)); ?>"><?php echo $this->escape($tag); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	<!-- Result URL -->
	<?php if (isset($this->result['url'])): ?>
	<div class="result-url"><a href="<?php echo htmlspecialchars((string) $this->result['url'], ENT_COMPAT, 'UTF-8', false); ?>" rel="nofollow"><?php echo htmlspecialchars((string) $this->result['url'], ENT_COMPAT, 'UTF-8', false); ?></a></div>
	<?php endif; ?>
</div> <!-- End Result Body -->
</div> <!-- End Result -->
