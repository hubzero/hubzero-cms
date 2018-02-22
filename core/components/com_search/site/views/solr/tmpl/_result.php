<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */
?>

<div class="result <?php echo (isset($this->result['access_level']) ? $this->result['access_level'] : 'public'); ?>" id="<?php echo $this->result['id']; ?>">
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

	<?php if (isset($this->result['_childDocuments_'])): ?>
		<!-- Tags -->
		<div class="result-tags">
			<ul class="tags">
				<?php 
					$baseTagUrl = Route::url('index.php?option=com_search&terms=' . $this->terms); 
				?>
				<?php foreach ($this->result['_childDocuments_'] as $tag): ?>
					<li>
						<?php $description = !empty($tag['description']) ? $tag['description'] : $tag['title'][0];?>
						<a class="tag" href="<?php echo $baseTagUrl . '&tags=' . $description;?>" data-tag="<?php echo $description;?>">
							<?php echo $tag['title'][0]; ?>
						</a>
					</li>
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
	<div class="result-url"><a href="<?php echo $this->result['url']; ?>"><?php echo $this->result['url']; ?></a></div>
</div> <!-- End Result Body -->
</div> <!-- End Result -->
