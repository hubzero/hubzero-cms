<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$data = '';
foreach ($this->sections as $section)
{
	if ($section['area'] == 'collect')
	{
		echo (isset($section['metadata'])) ? $section['metadata'] : '';
		continue;
	}
	$data .= (isset($section['metadata'])) ? $section['metadata'] : '';
}

if ($this->model->params->get('show_ranking', 0) || $this->model->params->get('show_audience',0) || $this->model->params->get('supportedtag',0) || $data)
{
	$database = App::get('db');
	?>
	<div class="metadata">
		<?php
		if ($this->model->params->get('show_ranking', 0))
		{
			if ($this->model->isTool())
			{
				$stats = new \Components\Resources\Helpers\Usage\Tools($database, $this->model->id, $this->model->type, $this->model->rating);
			}
			else
			{
				$stats = new \Components\Resources\Helpers\Usage\Andmore($database, $this->model->id, $this->model->type, $this->model->rating);
			}

			$rank = round($this->model->ranking, 1);

			$r = (10*$rank);

			$this->css('
				#rank-' . $this->model->id . ' {
					width: ' . $r . '%;
				}
			');
			?>
			<dl class="rankinfo">
				<dt class="ranking">
					<span class="rank"><span class="rank-<?php echo $r; ?>" id="rank-<?php echo $this->model->id; ?>">This resource has a</span></span> <?php echo number_format($rank, 1); ?> Ranking
				</dt>
				<dd>
					<p>
						Ranking is calculated from a formula comprised of <a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->model->id . '&active=reviews'); ?>">user reviews</a> and usage statistics. <a href="about/ranking/">Learn more &rsaquo;</a>
					</p>
					<div>
						<?php echo $stats->display(); ?>
					</div>
				</dd>
			</dl>
			<?php
		}

		if ($this->model->params->get('show_audience'))
		{
			include_once Component::path($this->option) . DS . 'models' . DS . 'audience.php';
			include_once Component::path($this->option) . DS . 'models' . DS . 'audience' . DS . 'level.php';

			$audience = \Components\Resources\Models\Audience::all()
				->whereEquals('rid', $this->model->id)
				->row();

			$this->view('_audience', 'view')
			     ->set('audience', $audience)
			     ->set('showtips', 1)
			     ->set('numlevels', 4)
			     ->set('audiencelink', $this->model->params->get('audiencelink'))
			     ->display();
		}

		if ($this->model->params->get('supportedtag'))
		{
			$rt = new \Components\Resources\Helpers\Tags($this->model->id);
			if ($rt->checkTagUsage($this->model->params->get('supportedtag'), $this->model->id))
			{
				include_once Component::path('com_tags') . DS . 'models' . DS . 'cloud.php';

				$tag = \Components\Tags\Models\Tag::oneByTag($this->model->params->get('supportedtag'));
			?>
			<p class="supported">
				<a href="<?php echo $this->model->params->get('supportedlink', Route::url('index.php?option=com_tags&tag=' . $tag->get('tag'))); ?>"><?php echo $this->escape(stripslashes($tag->get('raw_tag'))); ?></a>
			</p>
			<?php
			}
		}

		echo $data;
		?>
		<div class="clear"></div>
	</div><!-- / .metadata -->
	<?php
}
