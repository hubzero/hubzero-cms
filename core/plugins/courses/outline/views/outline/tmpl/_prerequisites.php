<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$db = App::get('db');

$this->css('prerequisites');
$this->js('prerequisites');
\Hubzero\Document\Assets::AddSystemScript('handlebars');
$includeForm = (isset($this->includeForm)) ? $this->includeForm : true;

$prereqs  = new \Components\Courses\Tables\Prerequisites($db);
$existing = $prereqs->loadAllByScope($this->scope, $this->scope_id, $this->section_id);
$ids      = array();
foreach ($existing as $value)
{
	$ids[] = $value->requisite_id;
}
?>

<script id="prerequisite-item" type="text/x-handlebars-template">
	<li class="requisite-list-item" data-id="{{id}}">
		<div class="requisite-item clearfix">
			<div class="remove-requisite" data-delete-id="{{req_id}}">x</div>
			<div class="requisite-item-title">{{title}}</div>
		</div>
	</li>
</script>

<?php if ($includeForm) : ?><form class="prerequisites-form"><?php endif; ?>
	<div class="prerequisites-wrap">
		<div class="title">Prerequisites:</div>
		<ul>
			<?php if ($existing && count($existing) > 0) : ?>
				<?php foreach ($existing as $v) : ?>
					<li class="requisite-list-item" data-id="<?php echo $v->requisite_id; ?>">
						<div class="requisite-item clearfix">
							<div class="remove-requisite" data-delete-id="<?php echo $v->id; ?>">x</div>
							<?php $class = '\\Components\\Courses\\Models\\'.ucfirst($v->requisite_scope); ?>
							<?php $item  = new $class($v->requisite_id); ?>
							<div class="requisite-item-title">
								<?php echo $item->get('title'); ?>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			<?php endif; ?>
		</ul>
		<div class="add-prerequisite">
			<select name="requisite_id">
				<option value="">add prerequisite...</option>
				<?php foreach ($this->items as $item) : ?>
					<?php if (!in_array($item->get('id'), $ids) && $item->get('id') != $this->scope_id) : ?>
						<option value="<?php echo $item->get('id'); ?>"><?php echo ($item->get('longTitle', false)) ? $item->get('longTitle') : $item->get('title'); ?></option>
					<?php endif; ?>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<input type="hidden" name="item_scope" value="<?php echo $this->scope; ?>" />
	<input type="hidden" name="item_id" value="<?php echo $this->scope_id; ?>" />
	<input type="hidden" name="requisite_scope" value="<?php echo $this->scope; ?>" />
	<input type="hidden" name="section_id" value="<?php echo $this->section_id; ?>" />
<?php if ($includeForm) : ?></form><?php endif;
