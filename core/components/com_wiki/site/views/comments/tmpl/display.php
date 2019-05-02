<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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

<?php if (!$this->sub) { ?>
<section class="main section">
	<div class="aside">
		<?php
		$this->view('wikimenu', 'pages')
			->set('option', $this->option)
			->set('controller', $this->controller)
			->set('page', $this->page)
			->set('task', $this->task)
			->set('sub', $this->sub)
			->display();
		?>
	</div>
	<div class="subject">
<?php } ?>

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

<?php if ($this->sub) { ?>
<section class="main section">
	<div class="section-inner">
<?php } ?>

		<p><?php echo Lang::txt('COM_WIKI_COMMENTS_EXPLANATION'); ?></p>

		<h3 id="commentlist-title"><?php echo Lang::txt('COM_WIKI_COMMENTS'); ?></h3>

		<div class="btn-group-wrap">
			<div class="btn-group dropdown">
				<?php
				$url = $this->page->link('comments');
				$txt = Lang::txt('COM_WIKI_ALL');

				$versions = $this->page->versions()
					->whereEquals('approved', 1)
					->order('version', 'asc')
					->rows();
				foreach ($versions as $ver)
				{
					if ($this->version == $ver->get('version'))
					{
						$url = $this->page->link('comments') . '&version=' . $ver->get('version');
						$txt = Lang::txt('COM_WIKI_VERSION_NUM', $ver->get('version'));
					}
				}
				?>
				<a class="btn" href="<?php echo Route::url($url); ?>"><?php echo $this->escape($txt); ?></a>
				<span class="btn dropdown-toggle"></span>
				<ul class="dropdown-menu">
					<?php if ($this->version) { ?>
						<li>
							<a href="<?php echo Route::url($this->page->link('comments')); ?>"><?php echo Lang::txt('COM_WIKI_ALL'); ?></a>
						</li>
					<?php } ?>
					<?php
					foreach ($versions as $ver)
					{
						if ($this->version == $ver->get('version'))
						{
							continue;
						}
						?>
						<li>
							<a href="<?php echo Route::url($this->page->link('comments') . '&version=' . $ver->get('version')); ?>"><?php echo Lang::txt('COM_WIKI_VERSION_NUM', $ver->get('version')); ?></a>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>

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
			echo '<p>' . Lang::txt('COM_WIKI_NO_COMMENTS' . ($this->version ? '_FOR_VERSION' : '')) . '</p>';
		}
		?>

		<?php if (isset($this->mycomment) && $this->mycomment instanceof \Components\Wiki\Models\Comment) { ?>
			<form action="<?php echo Route::url($this->page->link('comments')); ?>" method="post" id="commentform" class="section-inner">
				<h3 id="commentform-title">
					<?php echo Lang::txt('COM_WIKI_ADD_COMMENT'); ?>
				</h3>
				<p class="comment-member-photo">
					<?php
					$anon = (!User::isGuest()) ? 0 : 1;
					?>
					<img src="<?php echo User::picture($anon); ?>" alt="<?php echo Lang::txt('COM_WIKI_MEMBER_PICTURE'); ?>" />
				</p>
				<fieldset>
					<?php if ($this->page->config('comment_ratings') && !$this->mycomment->get('parent')) { ?>
						<fieldset>
							<legend><?php echo Lang::txt('COM_WIKI_FIELD_RATING'); ?>:</legend>
							<label for="review_rating_1"><input class="option" id="review_rating_1" name="comment[rating]" type="radio" value="1"<?php if ($this->mycomment->get('rating') == 1) { echo ' checked="checked"'; } ?> /> &#x272D;&#x2729;&#x2729;&#x2729;&#x2729; <?php echo Lang::txt('COM_WIKI_FIELD_RATING_ONE'); ?></label>
							<label for="review_rating_2"><input class="option" id="review_rating_2" name="comment[rating]" type="radio" value="2"<?php if ($this->mycomment->get('rating') == 2) { echo ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x2729;&#x2729;&#x2729;</label>
							<label for="review_rating_3"><input class="option" id="review_rating_3" name="comment[rating]" type="radio" value="3"<?php if ($this->mycomment->get('rating') == 3) { echo ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x272D;&#x2729;&#x2729;</label>
							<label for="review_rating_4"><input class="option" id="review_rating_4" name="comment[rating]" type="radio" value="4"<?php if ($this->mycomment->get('rating') == 4) { echo ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x272D;&#x272D;&#x2729;</label>
							<label for="review_rating_5"><input class="option" id="review_rating_5" name="comment[rating]" type="radio" value="5"<?php if ($this->mycomment->get('rating') == 5) { echo ' checked="checked"'; } ?> /> &#x272D;&#x272D;&#x272D;&#x272D;&#x272D; <?php echo Lang::txt('COM_WIKI_FIELD_RATING_FIVE'); ?></label>
						</fieldset>
					<?php } ?>

					<div class="form-group">
						<label for="ctext">
							<?php echo Lang::txt('COM_WIKI_FIELD_COMMENTS'); ?>:
							<?php echo \Components\Wiki\Helpers\Editor::getInstance()->display('comment[ctext]', 'ctext', $this->mycomment->get('ctext'), 'form-control minimal no-footer', '35', '15'); ?>
						</label>
					</div>

					<input type="hidden" name="comment[created]" value="<?php echo $this->escape($this->mycomment->get('created')); ?>" />
					<input type="hidden" name="comment[id]" value="<?php echo $this->escape($this->mycomment->get('id')); ?>" />
					<input type="hidden" name="comment[created_by]" value="<?php echo $this->escape($this->mycomment->get('created_by')); ?>" />
					<input type="hidden" name="comment[state]" value="<?php echo $this->escape($this->mycomment->get('state', 1)); ?>" />
					<input type="hidden" name="comment[version]" value="<?php echo $this->escape($this->mycomment->get('version')); ?>" />
					<input type="hidden" name="comment[parent]" value="<?php echo $this->escape($this->mycomment->get('parent')); ?>" />
					<input type="hidden" name="comment[page_id]" value="<?php echo $this->escape($this->mycomment->get('page_id', $this->page->get('id'))); ?>" />

					<input type="hidden" name="pagename" value="<?php echo $this->escape($this->page->pagename); ?>" />

					<?php foreach ($this->page->adapter()->routing('savecomment') as $name => $val) { ?>
						<input type="hidden" name="<?php echo $this->escape($name); ?>" value="<?php echo $this->escape($val); ?>" />
					<?php } ?>

					<div class="form-group form-check">
						<label id="comment-anonymous-label" class="form-check-label" for="comment-anonymous">
							<input class="option form-check-input" type="checkbox" name="comment[anonymous]" id="comment-anonymous" value="1"<?php if ($this->mycomment->get('anonymous') != 0) { echo ' checked="checked"'; } ?> />
							<?php echo Lang::txt('COM_WIKI_FIELD_ANONYMOUS'); ?>
						</label>
					</div>

					<?php echo Html::input('token'); ?>

					<p class="submit">
						<input type="submit" class="btn" value="<?php echo Lang::txt('COM_WIKI_SUBMIT'); ?>" />
					</p>

					<div class="sidenote">
						<p>
							<strong><?php echo Lang::txt('COM_WIKI_COMMENT_KEEP_RELEVANT'); ?></strong>
						</p>
						<p>
							<?php echo Lang::txt('COM_WIKI_COMMENT_FORMATTING_HINT'); ?>
						</p>
					</div>
				</fieldset>
			</form>
		<?php } ?>

	</div>
</section><!-- / .below section -->
