<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();
?>
<h3 class="section-header">
	<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_RECENT_QUESTIONS'); ?>
</h3>
<div class="container">
	<p class="section-options">
		<a class="icon-add add btn" href="<?php echo Route::url($this->resource->link() . '&active=questions&action=new'); ?>"><?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_ASK_A_QUESTION'); ?></a>
	</p>
	<table class="questions entries">
		<caption>
			<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_RECENT_QUESTIONS'); ?>
			<span>
				(<?php
					$visibleCount = count($this->rows);
					$limit = Request::getInt('limit') ? Request::getInt('limit') : $visibleCount;
					$total = $this->count;
					$start = $limit > $total ? 1 : Request::getInt('limitstart') + 1;

					if ($start + $limit > $total)
					{
						$end = $total;
					}
					else
					{
						$end = ($start - 1) + $limit;
					}
					echo Lang::txt('COM_ANSWERS_RESULTS_TOTAL', $start, $end, $total);
				?>)
			</span>
		</caption>
		<tbody>
	<?php if ($this->rows) { ?>
		<?php
		$i = 1;

		foreach ($this->rows as $row)
		{
			$i++;

			$name = Lang::txt('JANONYMOUS');
			if (!$row->get('anonymous'))
			{
				$name = $this->escape(stripslashes($row->creator->get('name', $name)));
				if (in_array($row->creator->get('access'), User::getAuthorisedviewLevels()))
				{
					$name = '<a href="' . Route::url($row->creator->link()) . '">' . $name . '</a>';
				}
			}

			$cls  = ($row->get('state') == 1) ? 'answered' : '';
			$cls  = ($row->isReported())      ? 'flagged'  : $cls;
			$cls .= ($row->get('created_by') == User::get('username')) ? ' mine' : '';
			?>
			<tr<?php echo ($cls) ? ' class="' . $cls . '"' : ''; ?>>
				<th>
					<span class="entry-id"><?php echo $row->get('id'); ?></span>
				</th>
				<td>
				<?php if (!$row->isReported()) { ?>
					<a class="entry-title" href="<?php echo Route::url($row->link()); ?>"><?php echo $this->escape(strip_tags($row->subject)); ?></a><br />
				<?php } else { ?>
					<span class="entry-title"><?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_QUESTION_UNDER_REVIEW'); ?></span><br />
				<?php } ?>
					<span class="entry-details">
						<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_ASKED_BY', $name); ?>
						<span class="entry-date-at"><?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_AT'); ?></span>
						<span class="entry-time"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('time'); ?></time></span>
						<span class="entry-date-on"><?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_ON'); ?></span>
						<span class="entry-date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time></span>
						<span class="entry-details-divider">&bull;</span>
						<span class="entry-state">
							<?php echo ($row->get('state') == 1) ? Lang::txt('PLG_RESOURCES_QUESTIONS_STATE_CLOSED') : Lang::txt('PLG_RESOURCES_QUESTIONS_STATE_OPEN'); ?>
						</span>
						<span class="entry-details-divider">&bull;</span>
						<span class="entry-comments">
							<a href="<?php echo Route::url($row->link() . '#answers'); ?>" title="<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_NUM_RESPONSES', $row->get('rcount')); ?>">
								<?php echo $row->responses->count(); ?>
							</a>
						</span>
					</span>
				</td>
			<?php if ($this->banking) { ?>
				<td class="reward">
				<?php if ($row->get('reward') == 1 && $this->banking) { ?>
					<span class="entry-reward"><?php echo $row->get('points'); ?> <a href="<?php echo $this->infolink; ?>" title="<?php echo Lang::txt('COM_ANSWERS_THERE_IS_A_REWARD_FOR_ANSWERING', $row->get('points', 0)); ?>"><?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_POINTS'); ?></a></span>
				<?php } ?>
				</td>
			<?php } ?>
				<td class="voting">
					<span class="vote-like">
					<?php if (User::isGuest()) { ?>
						<span class="vote-button <?php echo ($row->get('helpful', 0) > 0) ? 'like' : 'neutral'; ?> tooltips" title="<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_VOTE_UP_LOGIN'); ?>">
							<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_VOTE_LIKES', $row->get('helpful', 0)); ?>
						</span>
					<?php } else { ?>
						<a class="vote-button <?php echo ($row->get('helpful', 0) > 0) ? 'like' : 'neutral'; ?> tooltips" href="<?php echo Route::url('index.php?option=com_answers&task=vote&id=' . $row->get('id') . '&category=question&vote=yes'); ?>" title="<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_VOTE_UP', $row->get('helpful', 0)); ?>">
							<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_VOTE_LIKES', $row->get('helpful', 0)); ?>
						</a>
					<?php } ?>
					</span>
				</td>
			</tr>
		<?php } ?>
	<?php } else { ?>
			<tr class="noresults">
				<td colspan="<?php echo ($this->banking) ? '4' : '3'; ?>">
					<?php echo Lang::txt('PLG_RESOURCES_QUESTIONS_NO_QUESTIONS_FOUND'); ?>
				</td>
			</tr>
	<?php } ?>
		</tbody>
	</table>
	<form>
		<?php
			$pageNav = $this->rows->pagination;
			echo $pageNav;
		?>
	</form>
	<div class="clearfix"></div>
</div><!-- / .container -->

<div class="customfields">
	<?php
		// Parse for <nb:field> tags
		$type = $this->resource->type;

		$data = array();
		preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $this->resource->fulltxt, $matches, PREG_SET_ORDER);
		if (count($matches) > 0)
		{
			foreach ($matches as $match)
			{
				$data[$match[1]] = str_replace('="/site', '="' . substr(PATH_APP, strlen(PATH_ROOT)) . '/site', $match[2]);
			}
		}
		include_once Component::path('com_resources') . DS . 'models' . DS . 'elements.php';
		$elements = new \Components\Resources\Models\Elements($data, $this->resource->type->customFields);
		$schema = $elements->getSchema();
		$tab = Request::getCmd('active', 'questions');  // The active tab (section)

		if (is_object($schema))
		{
			if (!isset($schema->fields) || !is_array($schema->fields))
			{
				$schema->fields = array();
			}
			foreach ($schema->fields as $field)
			{
				if (isset($data[$field->name]))
				{
					if ($elements->display($field->type, $data[$field->name]) && isset($filed->display) && $field->display == $tab )
					{
						?>
						<h4><?php echo $field->label; ?></h4>
						<div class="resource-content">
						<?php echo $elements->display($field->type, $data[$field->name]); ?>
						</div>
						<?php
					}
				}
			}
		}
	?>
</div><!-- / .customfields -->
