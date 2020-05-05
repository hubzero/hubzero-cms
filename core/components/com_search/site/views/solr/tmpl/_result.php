<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
?>

<div class="result <?php echo isset($this->result['access_level']) ? $this->result['access_level'] : 'public'; ?>" id="<?php echo $this->result['id']; ?>">
<div class="result-body">
	<!-- Title : mandatory -->
	<h3 class="result-title"><a href="<?php echo $this->result['url']; ?>"><b><!-- highlight portion --></b><?php echo $this->result['title']; ?></a></h3>

	<div class="result-extras">
		<!-- Cateogory : mandatory -->
		<span class="result-category"><?php echo ucfirst($this->result['hubtype']); ?></span>

		<?php if (isset($this->result['date'])): ?>
			<?php $date = new \Hubzero\Utility\Date($this->result['date']); ?>
			<span class="result-timestamp"><time datetime="<?php echo $this->result['date']; ?>"><?php echo $date->toLocal('Y-m-d h:mA'); ?></time></span>
		<?php endif; ?>

		<?php if (isset($this->result['author'])): ?>
			<!-- Authors -->
			<span class="result-authors">
				<span class="result-author"><?php echo $this->result['authorString']; ?></span>
			</span>
		<?php endif; ?>

		<?php if (User::authorise('core.admin') && isset($this->result['access_level'])): ?>
			<!-- Access -->
			<span class="result-access">
				Access: <?php echo $this->result['access_level']; ?>
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
						<a class="tag" href="<?php echo $baseTagUrl . '&tags=' . $description;?>" data-tag="<?php echo $description;?>">
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
					<li><a class="tag" href="<?php echo Route::url('index.php?option=com_search&terms=' . $tag); ?>"><?php echo $tag; ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	<!-- Result URL -->
	<?php if (isset($this->result['url'])): ?>
	<div class="result-url"><a href="<?php echo $this->result['url']; ?>"><?php echo $this->result['url']; ?></a></div>
	<?php endif; ?>
</div> <!-- End Result Body -->
</div> <!-- End Result -->
