<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$handlerBase = DS . trim($this->config->get('handler_base_path', 'srv' . DS . 'projects'), DS);
if (!strstr($handlerBase, '{'))
{
	$handlerBase .= '/{project}/files/{file}';
}
?>

<?php foreach ($this->items as $item): ?>
	<?php
		$this->view('_item')
			->set('connection', $this->connection)
			->set('handlerBase', $handlerBase)
			->set('item', $item)
			->set('model', $this->model)
			->set('subdir', $this->subdir)
			->display();
	?>
<?php endforeach;
