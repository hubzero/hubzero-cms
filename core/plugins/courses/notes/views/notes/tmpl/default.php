<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

$results = array();

$entries = \Plugins\Courses\Notes\Models\Note::all()
	->whereEquals('section_id', $this->offering->section()->get('id'))
	->whereEquals('created_by', User::get('id'))
	->whereEquals('state', 1);

if ($this->filters['search'])
{
	$entries->whereLike('content', $this->filters['search']);
}
$notes = $entries->rows();

if ($notes->count())
{
	foreach ($notes as $note)
	{
		$ky = $note->get('scope_id');
		if (!isset($results[$ky]))
		{
			$results[$ky] = array();
		}
		$results[$ky][] = $note;
	}
}

$base = $this->offering->link();
?>

<?php if ($this->course->offering()->section()->access('view')) : ?>
	<form action="<?php echo Route::url($base . '&active=notes'); ?>" method="get">
		<fieldset class="filters">
			<div class="filters-inner">
				<ul>
					<li>
						<a class="download btn" href="<?php echo Route::url($base . '&active=notes&action=download&frmt=txt'); ?>">
							<span><?php echo Lang::txt('PLG_COURSES_NOTES_DOWNLOAD'); ?></span>
						</a>
					</li>
				</ul>
				<div class="clear"></div>
				<p>
					<label for="filter-search">
						<span><?php echo Lang::txt('PLG_COURSES_NOTES_SEARCH'); ?></span>
						<input type="text" name="search" id="filter-search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('PLG_COURSES_NOTES_SEARCH_NOTES'); ?>" />
					</label>
					<input type="submit" class="filter-submit" value="<?php echo Lang::txt('PLG_COURSES_NOTES_GO'); ?>" />
				</p>
			</div><!-- / .filters-inner -->
		</fieldset>
	</form>

	<div class="notes-wrap">
	<?php if (count($results)): ?>
		<?php
		foreach ($results as $id => $notes):
			$lecture = new \Components\Courses\Models\Assetgroup($id);
			$unit = \Components\Courses\Models\Unit::getInstance($lecture->get('unit_id'));
			?>
			<div class="section">
				<h3><?php echo $this->escape(stripslashes($lecture->get('title'))); ?></h3>
				<?php foreach ($notes as $note): ?>
					<div class="jSticky-medium static<?php if ($note->get('access')) { echo ' annotation'; } ?>" id="note-<?php echo $note->get('id'); ?>" data-id="<?php echo $note->get('id'); ?>">
						<div class="jSticky-header">
							<?php if ($note->get('timestamp') && $note->get('timestamp') != '00:00:00'): ?>
								<a href="<?php echo str_replace('%3A', ':', Route::url($base . '&active=outline&unit=' . $unit->get('alias') . '&b=' . $lecture->get('alias') . '&time=' . $this->escape($note->get('timestamp')))); ?>" class="time"><?php echo $this->escape($note->get('timestamp')); ?></a>
							<?php endif; ?>
						</div>
						<div class="jStickyNote">
							<textarea name="note_<?php echo $note->get('id'); ?>"><?php echo $this->escape(stripslashes($note->get('content'))); ?></textarea>
						</div>
						<a class="jSticky-delete" href="<?php echo Route::url($base . '&active=notes&action=delete&note=' . $note->get('id')); ?>" title="<?php echo Lang::txt('PLG_COURSES_NOTES_DELETE_NOTE'); ?>">x</a>
					</div>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>
			<?php
		endforeach; ?>
		<script type="text/javascript">
		jQuery(document).ready(function(jQuery) {
			var $ = jQuery;

			var url = "<?php echo Request::base(true) . '/' . $this->offering->link() . '&active=notes&no_html=1&note='; ?>";

			$('#page_content textarea').each(function(i, el) {
				var hgt = $(this).parent().parent().height();
					$(this).css('height', hgt - 32);

				$(el).on('keyup', function (e) {
					var id  = $(this).parent().parent().attr('data-id'),
						txt = $(this).val();

					typewatch(function () {
						$.getJSON(url + id + '&action=save&txt=' + txt, {}, function(data) {
							// Nothing going on here...
						});
					}, 500);
				});
			});
		});
		</script>
	<?php else: ?>
		<div id="notes-introduction">
			<div class="instructions">
				<ol>
					<li><?php echo Lang::txt('PLG_COURSES_NOTES_STEP1'); ?></li>
					<li><?php echo Lang::txt('PLG_COURSES_NOTES_STEP2'); ?></li>
					<li><?php echo Lang::txt('PLG_COURSES_NOTES_STEP3'); ?></li>
					<li><?php echo Lang::txt('PLG_COURSES_NOTES_STEP4'); ?></li>
				</ol>
			</div><!-- / .instructions -->
			<div class="questions">
				<p><strong><?php echo Lang::txt('PLG_COURSES_NOTES_WHERE_IS_SAVE_BUTTON'); ?></strong></p>
				<p><?php echo Lang::txt('PLG_COURSES_NOTES_WHERE_IS_SAVE_BUTTON_EXPLANATION'); ?></p>
				<p><strong><?php echo Lang::txt('PLG_COURSES_NOTES_WHO_CAN_SEE_MY_NOTES'); ?></strong></p>
				<p><?php echo Lang::txt('PLG_COURSES_NOTES_WHO_CAN_SEE_MY_NOTES_EXPLANATION'); ?></p>
			</div><!-- / .post-type -->
		</div><!-- / #notes-introduction -->
	<?php endif; ?>
	</div>
<?php else: ?>
	<?php
	$view = new \Hubzero\Plugin\View(array(
		'folder'  => 'courses',
		'element' => 'outline',
		'name'    => 'shared',
		'layout'  => '_not_enrolled'
	));

	$view->set('course', $this->course)
	     ->set('option', $this->option)
	     ->set('message', Lang::txt('PLG_COURSES_NOTES_ENROLLMENT_REQUIRED'))
	     ->display();
	?>
<?php endif;
