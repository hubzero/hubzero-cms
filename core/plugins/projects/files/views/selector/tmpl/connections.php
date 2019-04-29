<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<ul class="file-selector" id="file-selector" data-projectid="<?php echo $this->model->get('id');?>">
	<?php $id = 'dir-' . strtolower(\Components\Projects\Helpers\Html::generateCode(5, 5, 0, 1, 1)); ?>
	<li class="type-folder collapsed connection" id="<?php echo $id; ?>" data-connection="-1" data-path=".">
		<span class="item-info"></span>
		<span class="item-wrap collapsor">
			<span class="collapsor-indicator">&nbsp;</span>
			<img src="/core/plugins/filesystem/local/assets/img/icon.png" alt="" />
			<span title=""><?php echo $this->model->get('title'); ?> Master Repository</span>
		</span>
	</li>

	<?php foreach ($this->connections as $connection) : ?>
		<?php $imgRel = '/plugins/filesystem/' . $connection->provider->alias . '/assets/img/icon.png'; ?>
		<?php $img = (is_file(PATH_APP . DS . $imgRel)) ? '/app' . $imgRel : '/core' . $imgRel; ?>
		<?php $id = 'dir-' . strtolower(\Components\Projects\Helpers\Html::generateCode(5, 5, 0, 1, 1)); ?>
		<li class="type-folder collapsed connection" id="<?php echo $id; ?>" data-connection="<?php echo $connection->id ?>" data-path=".">
			<span class="item-info"></span>
			<span class="item-wrap collapsor">
				<span class="collapsor-indicator">&nbsp;</span>
				<img src="<?php echo $img; ?>" alt="" />
				<span title=""><?php echo $connection->name; ?></span>
			</span>
		</li>
	<?php endforeach; ?>
</ul>
