<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$cell = $this->cell;

$source = '';
if (isset($cell->source)):
	$source = implode('', $cell->source);
elseif (isset($cell->input)):
	$source = implode('', $cell->input);
endif;
?>

<?php if ($cell->cell_type == 'markdown'): ?>
	<div class="cell <?php echo $cell->cell_type; ?> rendered">
		<div class="input">
			<div class="prompt input_prompt"> </div>
			<div class="inner_cell">
				<div class="text_cell_render rendered_html"><?php echo ($this->parser) ? $this->parser->parse($source) : $source; ?></div>
			</div>
		</div>
	</div>
<?php endif; ?>
<?php
if ($cell->cell_type == 'code'):
	if (!empty($cell->input)):
		?>
		<div class="cell <?php echo $cell->cell_type; ?> rendered">
			<div class="input">
				<div class="prompt input_prompt">In [<?php echo (isset($cell->prompt_number)) ? $cell->prompt_number : ' '; ?>]:</div>
				<div class="inner_cell">
					<pre name="code" class="<?php echo (isset($cell->language)) ? $cell->language : 'python'; ?>:nogutter:nocontrols"><?php echo $this->escape($source); ?></pre>
				</div>
			</div>
		</div>
		<?php
	endif;
	if (!empty($cell->outputs)):
		$cls = '';
		if (isset($output->png)):
			$cls = 'output_png';
		endif;
		?>
		<div class="cell <?php echo $cell->cell_type; ?> rendered">
			<div class="input">
				<div class="prompt output_prompt">Out[<?php echo (isset($cell->prompt_number)) ? $cell->prompt_number : ' '; ?>]:</div>
				<div class="inner_cell <?php echo $cls; ?> output_execute_result">
					<?php
					$out = array();
					foreach ($cell->outputs as $output):
						if ($output->output_type == 'pyout'):
							?>
							<?php if (isset($output->png)): ?>
								<div class="output_img"><img src="data:image/png;base64,<?php echo trim($output->png); ?>" alt="<?php echo $this->escape(implode('', $output->text)); ?>" /></div>
							<?php else: ?>
								<pre class="output"><?php echo $this->escape(implode('', $output->text)); ?></pre>
							<?php endif; ?>
							<?php
						elseif ($output->output_type == 'stream'):
							$out[] = implode('', $output->text);
						endif;
					endforeach;

					if (!empty($out)):
						?>
						<pre class="output"><?php echo $this->escape(implode('', $out)); ?></pre>
						<?php
					endif;
					?>
				</div>
			</div>
		</div>
		<?php
	endif;
endif;
