<?php
/**
 * @package   hubzero-cms
 * @copyright Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('ipynb.css');
$this->js('highlighter/shCore.js')
     ->js('highlighter/shBrushPython.js')
     ->js('ipynb.js');
?>
<div class="file-preview ipynb">
	<div class="file-preview-code">
		<div class="cells">
			<?php
			$contents = json_decode($this->file->read());

			$parser = null;
			$mdpath = Plugin::path('handlers', 'markdown');

			if ($mdpath):
				$md = array(
					'block/CodeTrait.php',
					'block/FencedCodeTrait.php',
					'block/HeadlineTrait.php',
					'block/HtmlTrait.php',
					'block/ListTrait.php',
					'block/QuoteTrait.php',
					'block/RuleTrait.php',
					'block/TableTrait.php',
					'inline/CodeTrait.php',
					'inline/EmphStrongTrait.php',
					'inline/LinkTrait.php',
					'inline/StrikeoutTrait.php',
					'inline/UrlLinkTrait.php',
					'Parser.php',
					'Markdown.php',
					'MarkdownExtra.php',
					'GithubMarkdown.php'
				);
				foreach ($md as $mdfile):
					include_once $mdpath . '/markdown/' . $mdfile;
				endforeach;

				$parser = new cebe\Markdown\GithubMarkdown();
			endif;

			$output = array();

			foreach ($contents->cells as $cell):
				$source = implode('', $cell->source);

				if ($cell->cell_type == 'markdown'):
					$output[] = '<div class="cell ' . $cell->cell_type . ' rendered">';
						$output[] = '<div class="input">';
							$output[] = '<div class="prompt input_prompt"> </div>';
							$output[] = '<div class="inner_cell">';
								$output[] = '<div class="text_cell_render rendered_html">' . ($parser ? $parser->parse($source) : $source) . '</div>';
							$output[] = '</div>';
						$output[] = '</div>';
					$output[] = '</div>';
				endif;

				if ($cell->cell_type == 'code'):
					$output[] = '<div class="cell ' . $cell->cell_type . ' rendered">';
						$output[] = '<div class="input">';
							$output[] = '<div class="prompt input_prompt">In [ ]:</div>';
							$output[] = '<div class="inner_cell">';
								$output[] = '<pre name="code" class="python:nogutter:nocontrols">' . $source . '</pre>';
							$output[] = '</div>';
						$output[] = '</div>';
					$output[] = '</div>';
				endif;
			endforeach;

			echo implode("\n", $output);
			?>
		</div>
	</div>
</div>