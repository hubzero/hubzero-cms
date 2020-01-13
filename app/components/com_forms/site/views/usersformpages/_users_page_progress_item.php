<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$page = $this->page;
$pageId = $page->get('id');
$pageOrder = $page->get('order');
$pageTitle = $page->get('title');
$response = $this->response;
$completionStatus = $response->pageRequiredCompletionPercentage($page);
?>

<tr>

	<td><?php echo $pageOrder; ?></td>

	<td>
		<?php
			$this->view('_link', 'shared')
				->set('content', $pageTitle)
				->set('urlFunction', 'formsPageResponseUrl')
				->set('urlFunctionArgs', [['page_id' => $pageId]])
				->display();
		?>
	</td>

	<td><?php echo "$completionStatus%"; ?></td>

</tr>
