<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$i = 1;
$formId = $this->formId;
$pages = $this->pages;
$orderedPages = $pages->order('order', 'asc')->rows();
$pageCount = $orderedPages->count();
$position = isset($this->position) ? $this->position : null;
?>

<?php foreach ($orderedPages as $page): ?>

	<span class="page-number">
		<?php if ($position == $i):	?>
				<span class="current-position">
					<?php echo $i; ?>
				<span>
		<?php
			else:
				$this->view('_link', 'shared')
					->set('content', $i)
					->set('urlFunction', 'formsPageResponseUrl')
					->set('urlFunctionArgs', [['form_id' => $formId, 'ordinal' => $i]])
					->display();
			endif;

				$i++;
				if ($i <= $pageCount) echo ',';
		?>
	</span>

<?php endforeach; ?>
