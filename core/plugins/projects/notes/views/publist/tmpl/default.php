<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (count($this->items) > 0) {
?>
<div class="public-list-header">
	<h3><?php echo ucfirst(Lang::txt('COM_PROJECTS_PUBLIC')); ?> <?php echo Lang::txt('COM_PROJECTS_NOTES'); ?></h3>
</div>
<div class="public-list-wrap">
	<ul>
		<?php foreach ($this->items as $item) {
			$ref = json_decode($item->reference);

			if (isset($ref->pageid) && $this->page->loadById( $ref->pageid ))
			{
		?>
		<li class="notes"><a href="<?php echo Route::url($this->model->link('stamp') . '&s=' . $item->stamp); ?>"><?php echo $this->page->title; ?></li>
		<?php }
		} ?>
	</ul>
</div>
<?php }
