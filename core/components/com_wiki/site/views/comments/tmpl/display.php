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

// No direct access.
defined('_HZEXEC_') or die();

if (!$this->sub)
{
	$this->css()
	     ->js();
}
?>
<header id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
	<?php if (count($this->parents)) { ?>
		<p class="wiki-crumbs">
			<?php foreach ($this->parents as $parent) { ?>
				<a class="wiki-crumb" href="<?php echo Route::url($parent->link()); ?>"><?php echo $parent->title; ?></a> /
			<?php } ?>
		</p>
	<?php } ?>

	<h2><?php echo $this->escape($this->page->title); ?></h2>
	<?php
	if (!$this->page->isStatic())
	{
		$this->view('authors', 'pages')
			//->setBasePath($this->base_path)
			->set('page', $this->page)
			->display();
	}
	?>
</header><!-- /#content-header -->

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php
	$this->view('submenu', 'pages')
		//->setBasePath($this->base_path)
		->set('option', $this->option)
		->set('controller', $this->controller)
		->set('page', $this->page)
		->set('task', $this->task)
		->set('sub', $this->sub)
		->display();
?>

<?php if (!$this->sub) { ?>
<section class="section">
	<div class="subject">
<?php } ?>
		<p><?php echo Lang::txt('COM_WIKI_COMMENTS_EXPLANATION'); ?></p>
<?php if (!$this->sub) { ?>
	</div><!-- / .subject -->
	<aside class="aside">
		<p><a href="<?php echo Route::url($this->page->link('addcomment') . '#commentform'); ?>" class="icon-add add btn"><?php echo Lang::txt('COM_WIKI_ADD_COMMENT'); ?></a></p>
	</aside><!-- / .aside -->
</section><!-- / .section -->
<?php } ?>

<section class="main section">
	<div class="section-inner">
		<div class="subject">
			<?php if ($this->sub) { ?>
				<p class="comment-add-btn">
					<a href="<?php echo Route::url($this->page->link('addcomment') . '#commentform'); ?>" class="icon-add add btn"><?php echo Lang::txt('COM_WIKI_ADD_COMMENT'); ?></a>
				</p>
			<?php } ?>
			<h3 id="commentlist-title"><?php echo Lang::txt('COM_WIKI_COMMENTS'); ?></h3>

			<?php
			$model = $this->page->comments()
				->including(['creator', function ($creator){
					$creator->select('*');
				}])
				->whereIn('state', array(
					Components\Wiki\Models\Comment::STATE_PUBLISHED,
					Components\Wiki\Models\Comment::STATE_FLAGGED
				));
			if ($this->version)
			{
				$model->whereEquals('version', $this->version);
			}
			$comments = $model
				->ordered()
				->rows();

			if ($comments->count())
			{
				$this->view('_list', 'comments')
					//->setBasePath($this->base_path)
					->set('parent', 0)
					->set('page', $this->page)
					->set('option', $this->option)
					->set('comments', $comments)
					->set('config', $this->config)
					->set('depth', 0)
					->set('version', $this->version)
					->set('cls', 'odd')
					->display();
			}
			else
			{
				if ($this->version)
				{
					echo '<p>' . Lang::txt('COM_WIKI_NO_COMMENTS_FOR_VERSION') . '</p>';
				}
				else
				{
					echo '<p>' . Lang::txt('COM_WIKI_NO_COMMENTS') . '</p>';
				}
			}
			?>
		</div><!-- / .subject -->
		<aside class="aside">
			<form action="<?php echo Route::url($this->page->link('comments')); ?>" method="get">
				<fieldset class="controls">
					<label for="filter-version">
						<?php echo Lang::txt('COM_WIKI_COMMENT_REVISION'); ?>:
						<select name="version" id="filter-version">
							<option value=""><?php echo Lang::txt('COM_WIKI_ALL'); ?></option>
							<?php
							$versions = $this->page->versions()
								->whereEquals('approved', 1)
								->order('version', 'asc')
								->rows();
							foreach ($versions as $ver)
							{
								?>
								<option value="<?php echo $ver->get('version'); ?>"<?php echo ($this->version == $ver->get('version')) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_WIKI_VERSION_NUM', $ver->get('version')); ?></option>
								<?php
							}
							?>
						</select>
					</label>
					<p class="submit"><input type="submit" class="btn" value="<?php echo Lang::txt('COM_WIKI_GO'); ?>" /></p>
				<?php if ($this->sub) { ?>
					<input type="hidden" name="action" value="comments" />
					<input type="hidden" name="active" value="<?php echo $this->sub; ?>" />
				<?php } else { ?>
					<input type="hidden" name="task" value="comments" />
				<?php } ?>
				</fieldset>
			</form>
		</aside><!-- / .aside -->
	</div>
</section><!-- / .main section -->

<?php if (isset($this->mycomment) && $this->mycomment instanceof \Components\Wiki\Models\Comment) { ?>
<section class="below section">
	<form action="<?php echo Route::url($this->page->link('comments')); ?>" method="post" id="commentform" class="section-inner">
		<div class="subject">
			<h3 id="commentform-title">
				<?php echo Lang::txt('COM_WIKI_ADD_COMMENT'); ?>
			</h3>
			<p class="comment-member-photo">
				<?php
				$anon = (!User::isGuest()) ? 0 : 1;
				?>
				<img src="<?php echo \Hubzero\User\Profile\Helper::getMemberPhoto(User::getRoot(), $anon); ?>" alt="<?php echo Lang::txt('COM_WIKI_MEMBER_PICTURE'); ?>" />
			</p>
			<fieldset>
				<?php if (!$this->mycomment->get('parent')) { ?>
					<fieldset>
						<legend><?php echo Lang::txt('COM_WIKI_FIELD_RATING'); ?>:</legend>
						<label><input class="option" id="review_rating_1" name="comment[rating]" type="radio" value="1"<?php if ($this->mycomment->get('rating') == 1) { echo ' checked="checked"'; } ?> /> &#x272D;&#x2729;&#x2729;&#x2729;&#x2729; <?php echo Lang::txt('COM_WIKI_FIELD_RATING_ONE'); ?></label>
						<label><input class="option" id="review_rating_2" name="comment[rating]" type="radio" value="2"<?php if ($this->mycomment->get('rating') == 2) { echo ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x2729;&#x2729;&#x2729;</label>
						<label><input class="option" id="review_rating_3" name="comment[rating]" type="radio" value="3"<?php if ($this->mycomment->get('rating') == 3) { echo ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x272D;&#x2729;&#x2729;</label>
						<label><input class="option" id="review_rating_4" name="comment[rating]" type="radio" value="4"<?php if ($this->mycomment->get('rating') == 4) { echo ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x272D;&#x272D;&#x2729;</label>
						<label><input class="option" id="review_rating_5" name="comment[rating]" type="radio" value="5"<?php if ($this->mycomment->get('rating') == 5) { echo ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x272D;&#x272D;&#x272D; <?php echo Lang::txt('COM_WIKI_FIELD_RATING_FIVE'); ?></label>
					</fieldset>
				<?php } ?>
				<label>
					<?php echo Lang::txt('COM_WIKI_FIELD_COMMENTS'); ?>:
					<?php
					echo \Components\Wiki\Helpers\Editor::getInstance()->display('comment[ctext]', 'ctext', $this->mycomment->get('ctext'), '', '35', '15');
					?>
				</label>

				<input type="hidden" name="comment[created]" value="<?php echo $this->escape($this->mycomment->get('created')); ?>" />
				<input type="hidden" name="comment[id]" value="<?php echo $this->escape($this->mycomment->get('id')); ?>" />
				<input type="hidden" name="comment[created_by]" value="<?php echo $this->escape($this->mycomment->get('created_by')); ?>" />
				<input type="hidden" name="comment[status]" value="<?php echo $this->escape($this->mycomment->get('status', 1)); ?>" />
				<input type="hidden" name="comment[version]" value="<?php echo $this->escape($this->mycomment->get('version')); ?>" />
				<input type="hidden" name="comment[parent]" value="<?php echo $this->escape($this->mycomment->get('parent')); ?>" />
				<input type="hidden" name="comment[page_id]" value="<?php echo $this->escape($this->mycomment->get('page_id')); ?>" />

				<input type="hidden" name="pagename" value="<?php echo $this->escape($this->page->pagename); ?>" />

				<?php foreach ($this->page->adapter()->routing('savecomment') as $name => $val) { ?>
					<input type="hidden" name="<?php echo $this->escape($name); ?>" value="<?php echo $this->escape($val); ?>" />
				<?php } ?>

				<label id="comment-anonymous-label">
					<input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1"<?php if ($this->mycomment->get('anonymous') != 0) { echo ' checked="checked"'; } ?> />
					<?php echo Lang::txt('COM_WIKI_FIELD_ANONYMOUS'); ?>
				</label>

				<?php echo Html::input('token'); ?>

				<p class="submit"><input type="submit" class="btn" value="<?php echo Lang::txt('COM_WIKI_SUBMIT'); ?>" /></p>
				<div class="sidenote">
					<p>
						<strong><?php echo Lang::txt('COM_WIKI_COMMENT_KEEP_RELEVANT'); ?></strong>
					</p>
					<p>
						<?php echo Lang::txt('COM_WIKI_COMMENT_FORMATTING_HINT'); ?>
					</p>
				</div>
			</fieldset>
		</div><!-- / .subject -->
		<aside class="aside">
			<table class="wiki-reference">
				<caption><?php echo Lang::txt('COM_WIKI_SYNTAX_REFERENCE'); ?></caption>
				<tbody>
					<tr>
						<td>'''bold'''</td>
						<td><b>bold</b></td>
					</tr>
					<tr>
						<td>''italic''</td>
						<td><i>italic</i></td>
					</tr>
					<tr>
						<td>__underline__</td>
						<td><span style="text-decoration:underline;">underline</span></td>
					</tr>
					<tr>
						<td>{{{monospace}}}</td>
						<td><code>monospace</code></td>
					</tr>
					<tr>
						<td>~~strike-through~~</td>
						<td><del>strike-through</del></td>
					</tr>
					<tr>
						<td>^superscript^</td>
						<td><sup>superscript</sup></td>
					</tr>
					<tr>
						<td>,,subscript,,</td>
						<td><sub>subscript</sub></td>
					</tr>
				</tbody>
			</table>
		</aside><!-- / .aside -->
	</form>
</section><!-- / .below section -->
<?php } ?>
