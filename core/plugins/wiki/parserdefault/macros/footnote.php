<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki macro class for linking footnotes
 */
class FootNoteMacro extends WikiMacro
{
	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = "Add a footnote, or explicitly display collected footnotes when no args are given. Useful for citations.

Examples:
 * [[Footnote(I am a footnote)]]  # add a reference to a footnote with text 'I am a footnote'
 * [[Footnote(reflabel | I am another footnote )]]  # add a reference with label 'reflabel' to a footnote with text 'I am another footnote'. The label can be used in future footnote macro calls to reference the same footnote (see next example).
 * [[Footnote(reflabel)]]  # add a reference to a new footnote with text 'reflabel' or an existing footnote with label or text 'reflabel'
 * [[Footnote]]  # display all collected footnotes in a referenced list";
		$txt['html'] = '<p>Add a footnote, or explicitly display collected footnotes when no args are given.  Useful for citations.</p>
<p>Examples:</p>
<ul>
<li><code>[[Footnote(I am a footnote)]]</code>  # add a reference to a footnote with text "I am a footnote"</li>
<li><code>[[Footnote(reflabel | I am another footnote )]]</code>  # add a reference with label "reflabel" to a footnote with text "I am another footnote". The label can be used in future footnote macro calls to reference the same footnote (see next example).</li>
<li><code>[[Footnote(reflabel)]]</code>  # add a reference to a new footnote with text "reflabel" or an existing footnote with label or text "reflabel"</li>
<li><code>[[Footnote]]</code>  # display all collected footnotes in a referenced list</li>
</ul>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		static $wm;

		if (!is_object($wm))
		{
			$wm = new stdClass();
			$wm->footnotes = array();
			$wm->footnotes_stubs = array();
			$wm->footnotes_count = 0;
		}

		if ($this->args)
		{
			$p = new WikiParser('Footnotes', $this->option, $this->scope, $this->pagename, $this->pageid, $this->filepath, $this->domain);

			// Args are either "note" or "label | note". The label (stub) lets a
			// later [[Footnote(label)]] reference the same footnote. When no label
			// is given the note text itself acts as the stub.
			// Split on the FIRST pipe only so footnote text may contain '|'.
			$args = explode('|', $this->args, 2);
			$stub = trim(array_shift($args));
			$note = (isset($args[0]) ? trim($args[0]) : $stub);

			$wm->footnotes_count++;

			// Only labelled (non-empty stub) footnotes are de-duplicated;
			// otherwise distinct footnotes with empty stubs would collide.
			if ($stub !== '' && in_array($stub, $wm->footnotes_stubs))
			{
				$i = array_search($stub, $wm->footnotes_stubs) + 1;
				$k = $wm->footnotes_count;

				$wm->footnotes[$i-1]->refs[] = 'fndef-' . $k;

				return '<sup id="fndef-' . $k . '" class="tex2jax_ignore"><a href="#fnref-' . $i . '" aria-label="Footnote ' . $i . '">&#91;' . $i . '&#93;</a></sup>';
			}

			$note = $p->parse($note);

			$i = count($wm->footnotes) + 1;

			$footnote = new stdClass;
			$footnote->content = $note;
			$footnote->id      = 'fnref-' . $i;
			$footnote->refs    = array(
				'fndef-' . $i
			);

			$wm->footnotes_stubs[] = $stub;
			$wm->footnotes[] = $footnote;

			return '<sup id="fndef-' . $i . '" class="tex2jax_ignore"><a href="#fnref-' . $i . '" aria-label="Footnote ' . $i . '">&#91;' . $i . '&#93;</a></sup>';
		}
		else
		{
			$letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

			$html  = '<ol class="footnotes">';
			foreach ($wm->footnotes as $i => $footnote)
			{
				$html .= '<li>';
				if (count($footnote->refs) > 1)
				{
					$html .= '^ ';
					foreach ($footnote->refs as $key => $ref)
					{
						$html .= '<sup class="tex2jax_ignore"><a href="#' . $ref . '">' . strtolower($letters[$key]) . '</a></sup> ';
					}
				}
				else if (count($footnote->refs) == 1)
				{
					$html .= '<a href="#' . $footnote->refs[0] . '">^</a> ';
				}
				$html .= '<span id="fnref-' . ($i + 1) . '"></span>' . substr($footnote->content, 3);
				$html .= '</li>';
			}
			$html .= '</ol>';

			$wm = null;

			return $html;
		}
	}
}
