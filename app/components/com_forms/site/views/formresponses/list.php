<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('formResponsesList');

$feedItems = $this->feedItems;
$responses = $this->responses;
$responsesCount = $responses->count();
$responsesListUrl = $this->listUrl;

$breadcrumbs = [
	'Forms' => ['formListUrl'],
	'Responses' => ['usersResponsesUrl']
];
$this->view('_forms_breadcrumbs', 'shared')
	->set('breadcrumbs', $breadcrumbs)
	->set('page', "Responses")
	->display();
?>

<section class="main section">
	<div class="grid">

		<div class="col span7">
			<?php
				$this->view('_response_list')
					->set('responses', $responses)
					->display();

				$this->view('_pagination', 'shared')
					->set('minDisplayLimit', 1)
					->set('pagination', $responses->pagination)
					->set('paginationUrl', $responsesListUrl)
					->set('recordsCount', $responsesCount)
					->display();
				?>
		</div>

		<div class="col span5 omega">
			<div class="feed-comments">
				<?php
					$this->view('_feed', 'shared')
						->set('feedItems', $feedItems)
						->set('itemView', '_responses_feed_item')
						->set('noticeView', '_responses_feed_empty_notice')
						->set('subviewsSource', 'formresponses')
						->display();
				?>
			</div>
		</div>

	</div>
</section>
