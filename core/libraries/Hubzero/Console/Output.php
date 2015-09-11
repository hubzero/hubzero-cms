<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console;

use Hubzero\Console\Config;

/**
 * Output class for rendering content to command line
 **/
class Output
{
	/**
	 * Array containing lines to be rendered out
	 *
	 * @var  array
	 **/
	private $response = array();

	/**
	 * Track default indentation for lines
	 *
	 * If prefering predominant indentation other than 0,
	 * set with setDefaultIndentation() to avoid having
	 * to set on all calls to addLine().
	 *
	 * @var  string
	 **/
	private $defaultIndentation = '';

	/**
	 * Track whether we're in interactive mode
	 *
	 * While in interactive mode, output each line as it's given
	 * rather than pooling and waiting until render is called.
	 *
	 * @var  string
	 **/
	private $isInteractive = true;

	/**
	 * Whether or not to color output
	 *
	 * @var  bool
	 **/
	private $colored = true;

	/**
	 * Set the output mode
	 *
	 * Assume normal, but minimal and verbose are also options. This isn't setting the format, but it's allowing us to distinguish what
	 * amount/sorts of data the command should return.  The individual format handler would then format the output appropriately.
	 *
	 * @var  string
	 **/
	private $mode = 'normal';

	/**
	 * Render out stored output to command line
	 *
	 * @param   bool  $newLine  Whether or not to include new line with each response (really only applies to interactive output)
	 * @return  void
	 **/
	public function render($newLine = true)
	{
		// Make sure there is something there
		if (isset($this->response) && count($this->response) > 0)
		{
			foreach ($this->response as $line)
			{
				// Echo out the message
				echo $line['message'];

				if ($newLine)
				{
					echo "\n";
				}
			}

			// Reset response
			$this->response = array();
		}
	}

	/**
	 * Add a new line to the output buffer (not actually a real php output buffer)
	 *
	 * @param   string  $message  Text of line
	 * @param   mixed   $styles   Array of custom styles or string containing predefined term (see formatLine() for posibilities)
	 * @param   bool    $newLine  Whether or not line should end with a new line
	 * @return  $this
	 **/
	public function addLine($message, $styles = null, $newLine = true)
	{
		$this->formatLine($message, $styles);

		$this->response[] = array(
			'message' => $message
		);

		if ($this->isInteractive())
		{
			$this->render($newLine);
		}

		return $this;
	}

	/**
	 * Add a new string to the output buffer
	 *
	 * Main difference between this and addLine() is that this is a shortcut for not
	 * including a new line at the end of the output
	 *
	 * @param   string  $message  Text of string
	 * @param   mixed   $styles   Array of custom styles or string containing predefined term (see formatLine() for posibilities)
	 * @return  $this
	 **/
	public function addString($message, $styles = null)
	{
		$this->addLine($message, $styles, false);

		return $this;
	}

	/**
	 * Add a paragraph to the output buffer.
	 * This will chop the text up to maintain lines of approximately 80 characters.
	 *
	 * @param   string  $paragraph  Text to be chopped into lines and stored
	 * @param   mixed   $styles     Array of custom styles or string containing predefined term (see formatLine() for posibilities)
	 * @return  $this
	 **/
	public function addParagraph($paragraph, $styles = array())
	{
		// Sanitize the given text of new lines, double spaces, and tabs
		$paragraph = str_replace("\n", " ", $paragraph);
		$paragraph = str_replace("  ", " ", $paragraph);
		$paragraph = str_replace("\t", "",  $paragraph);

		// Now check if the paragraph is longer than 70 characters and subdivide as appropriate
		do
		{
			if (strlen($paragraph) > 70 && $break = strpos($paragraph, " ", 70))
			{
				$message   = substr($paragraph, 0, $break);
				$paragraph = trim(substr($paragraph, $break));
			}
			else
			{
				$message = trim($paragraph);
				$break   = false;
			}

			// Add the individual line
			$this->addLine($message, $styles);
		}
		while ($break !== false);

		return $this;
	}

	/**
	 * Renders rows in a table-like structure
	 *
	 * @param   array  $rows     The rows of text to render
	 * @param   bool   $headers  If the first row contains header information
	 * @return  $this
	 **/
	public function addTable($rows, $headers = false)
	{
		// Figure out some items we need to know
		$maxLengths = [];

		foreach ($rows as $i => $row)
		{
			foreach ($row as $k => $field)
			{
				$maxLengths[$k] = isset($maxLengths[$k]) ? $maxLengths[$k] : 0;
				$maxLengths[$k] = strlen($field) > $maxLengths[$k] ? strlen($field) : $maxLengths[$k];
			}
		}

		// Compute the total length of the table
		$width = array_sum($maxLengths) + ((count($row) - 1) * 3) + 2;

		// Add the top border
		$this->addLine('/' . str_repeat('-', ($width)) . '\\');

		// Draw the rows
		foreach ($rows as $i => $row)
		{
			$styles = ($i == 0 && $headers) ? ['format' => 'underline'] : null;
			foreach ($row as $k => $field)
			{
				$padding = $maxLengths[$k] - strlen($field);
				$this->addString('| ');
				$this->addString($field, $styles);
				$this->addString(' ' . str_repeat(' ', $padding));
			}

			$this->addLine('|');
		}

		// Add the bottom border
		$this->addLine('\\' . str_repeat('-', ($width)) . '/');
	}

	/**
	 * Add raw text to output buffer
	 *
	 * @param   string  $text  The text to add
	 * @return  $this
	 **/
	public function addRaw($text)
	{
		$this->response[] = array(
			'message' => $text
		);

		if ($this->isInteractive())
		{
			$this->render(true);
		}
	}

	/**
	 * Helper method to add an array of lines to the output buffer.
	 *
	 * Here we're expecting an array, with each entry also containing an
	 * array with at least one key of 'message'. Another key
	 * can also be provided with a message type, which translates to
	 * one of the predefined styles used in formatLine().
	 *
	 * @param   array  $lines  Array of lines to add
	 * @return  void
	 **/
	public function addLinesFromArray($lines)
	{
		foreach ($lines as $line)
		{
			$this->addLine($line['message'], ((isset($line['type'])) ? $line['type'] : null));
		}
	}

	/**
	 * Add a blank line to the output
	 *
	 * @return  $this
	 **/
	public function addSpacer()
	{
		$this->addLine('');

		return $this;
	}

	/**
	 * Send beep
	 *
	 * @return  $this
	 **/
	public function beep()
	{
		echo chr(7);

		return $this;
	}

	/**
	 * Send backspace
	 *
	 * @param   int    $spaces       Number of spaces to back up
	 * @param   bool   $destructive  Whether or not to destroy existing chars
	 * @return  $this
	 **/
	public function backspace($spaces = 1, $destructive = false)
	{
		echo chr(27) . "[" . (int)$spaces . "D";

		if ($destructive)
		{
			for ($i=0; $i < $spaces; $i++)
			{
				echo chr(32);
			}

			echo chr(27) . "[" . (int)$spaces . "D";
		}

		return $this;
	}

	/**
	 * Get response from the user
	 *
	 * @param   string  $prompt  Question to ask user
	 * @return  string
	 **/
	public function getResponse($prompt)
	{
		$prompt = trim($prompt);
		$this->addString("{$prompt} ");

		$response = fgets(STDIN);
		$response = trim($response);

		return $response;
	}

	/**
	 * Shortcut function to print an error, render the error, and halt execution
	 *
	 * @param   string  $message  Line of text used in error
	 * @return  void
	 **/
	public function error($message)
	{
		$this->addLine($message, 'error');
		$this->render();
		exit(1);
	}

	/**
	 * Set the default indentation. This will be used unless an indentation is
	 * explicitly given while adding a line.
	 *
	 * @param   int  $indentation  Intiger of number of spaces to indent lines
	 * @return  void
	 **/
	public function setDefaultIndentation($indentation)
	{
		$ind = '';
		for ($i=0; $i < (int) $indentation; $i++)
		{
			$ind .= ' ';
		}
		$this->defaultIndentation = $ind;
	}

	/**
	 * Get our output subclass specialized for rendering help doc
	 *
	 * @return  \Hubzero\Console\Output\Help
	 **/
	public function getHelpOutput()
	{
		$class = __NAMESPACE__ . '\\Output\\Help';

		return new $class();
	}

	/**
	 * Get our output subclass specialized for a certain format
	 *
	 * @param   string  $format  The format to get
	 * @return  object
	 **/
	public function getOutputFormatter($format)
	{
		$class = __NAMESPACE__ . '\\Output\\' . ucfirst(strtolower($format));

		if (class_exists($class))
		{
			return new $class();
		}
		else
		{
			return $this;
		}
	}

	/**
	 * Get our output subclass specialized for rendering progress tracking
	 *
	 * @return  \Hubzero\Console\Output\Progress
	 **/
	public function getProgressOutput()
	{
		$class = __NAMESPACE__ . '\\Output\\Progress';

		return new $class();
	}

	/**
	 * Take line of text and styles and give back a formatted line.
	 *
	 * This will also translate textual colors and formatting words
	 * to bash escape sequences.
	 *
	 * @param   string  $message  Raw line of text
	 * @param   mixed   $styles   String or array of styles
	 * @return  void
	 **/
	private function formatLine(&$message, $styles)
	{
		$style = array(
			'format'      => '0',
			'color'       => '',
			'indentation' => $this->defaultIndentation
		);

		// If array, parse for individual style declarations
		if (is_array($styles) && count($styles) > 0)
		{
			foreach ($styles as $k => $v)
			{
				switch ($k)
				{
					case 'color':
						$style['color'] = $this->translateColor($v);
						break;

					case 'format':
						$style['format'] = $this->translateFormat($v);
						break;

					case 'indentation':
						$style['indentation'] = '';
						for ($i=0; $i < $v; $i++)
						{
							$style['indentation'] .= ' ';
						}
						break;
				}
			}
		}
		// If string, parse for predefined formatting key words
		elseif (is_string($styles))
		{
			switch ($styles)
			{
				case 'warning':
					$style['color'] = '43';
					break;

				case 'error':
					$style['format'] = '1';
					$style['color']  = '41';
					break;

				case 'info':
					$style['color'] = $this->translateColor('blue');
					break;

				case 'success':
					$style['color'] = $this->translateColor('green');
					break;
			}
		}

		if (!Config::get('color', $this->colored))
		{
			$message = $style['indentation'] . $message;
		}
		else
		{
			$messageStyles  = $style['format'];
			$messageStyles .= ($style['color']) ? ';' . $style['color'] : '';
			$message        = chr(27) . "[" . $messageStyles . "m" . $style['indentation'] . $message . chr(27) . "[0m";
		}
	}

	/**
	 * Make output stream rather than pooled and dumped out at the end when render is called
	 *
	 * @return  void
	 **/
	public function makeInteractive()
	{
		$this->isInteractive = true;
	}

	/**
	 * Make output pooled
	 *
	 * @return  void
	 **/
	public function makeNonInteractive()
	{
		$this->isInteractive = false;
	}

	/**
	 * Check if output is streamed
	 *
	 * @return  bool
	 **/
	public function isInteractive()
	{
		return $this->isInteractive;
	}

	/**
	 * Set the output mode
	 *
	 * @return  void
	 **/
	public function setMode($mode)
	{
		$this->mode = $mode;
	}

	/**
	 * Get the output mode
	 *
	 * @return  string
	 **/
	public function getMode()
	{
		return $this->mode;
	}

	/**
	 * Make output colored
	 *
	 * @return  void
	 **/
	public function makeColored()
	{
		$this->colored = true;
	}

	/**
	 * Make output b&w
	 *
	 * @return  void
	 **/
	public function makeUnColored()
	{
		$this->colored = false;
	}

	/**
	 * Simple translation table to map color words to bash equivalents
	 *
	 * @param   string  $color  Human readable color name
	 * @return  string
	 **/
	private function translateColor($color)
	{
		$colors = array(
			'black'  => '30',
			'red'    => '31',
			'green'  => '32',
			'yellow' => '33',
			'blue'   => '34',
			'purple' => '35',
			'cyan'   => '36',
			'white'  => '37'
		);

		return $colors[$color];
	}

	/**
	 * Simple translation table to map formatting key words to bash equivalents
	 *
	 * @param   string  $format  Human readable format name
	 * @return  string
	 **/
	private function translateFormat($format)
	{
		$formats = array(
			'normal'    => '0',
			'bold'      => '1',
			'underline' => '4'
		);

		return $formats[$format];
	}
}