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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Api;

use Hubzero\Http\Response as BaseResponse;
use Hubzero\Api\Response\Xml;

/**
 * Response represents an HTTP response.
 */
class Response extends BaseResponse
{
	/**
	 * The original content of the response.
	 *
	 * @var  mixed
	 */
	public $original;

	/**
	 * Set the content on the response.
	 *
	 * @param   mixed   $content
	 * @return  object  $this
	 */
	public function setContent($content)
	{
		$this->original = $content;

		$status = $this->getStatusCode();
		$reason = '';
		$output = '';

		switch ($this->headers->get('content-type'))
		{
			case 'text/plain':
				if ($suppress_response_codes)
				{
					$output .= "Status: $status\n";
					$output .= "Reason: $reason\n";
					$output .= "\n";
				}

				if (!is_object($content) && !is_array($content))
				{
					$output .= $content;
				}
				else
				{
					$output .= json_encode($content);
				}
			break;

			case 'text/html':
				$reason  = htmlspecialchars($reason);
				$content = htmlspecialchars($content);

				$output .= "<!DOCTYPE html>\n";
				$output .= "<html lang='en'>\n";
				$output .= "<head>\n";
				$output .= "<meta charset='utf-8'>\n";
				$output .= "<title>$status $reason</title>\n";
				$output .= "</head>\n";
				$output .= "<body>\n";
				$output .= '<div class="error">' . "\n";

				$output .= '<h1 id="reason">' . $reason . "</h1>\n";

				if ($suppress_response_codes)
				{
					$output .= '<p id="status">' . htmlspecialchars($status) . "</p>\n";
				}

				if (!is_object($content) && !is_array($content))
				{
					$output .= '<p id ="message">' . $content . "</p>\n";
				}
				else
				{
					$output .= '<p id ="message">' . json_encode($content) . "</p>\n";
				}

				$output .= "</div>\n";
				$output .= "</body>\n";
				$output .= "</html>";
			break;
			case 'application/xhtml+xml':
				$reason  = htmlspecialchars($reason);
				$content = htmlspecialchars($content);

				$output .= '<?xml version="1.0" ?>' . "\n";
				$output .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
				$output .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">' . "\n";
				$output .= "<head>\n";
				$output .= "<title>$status $reason</title>\n";
				$output .= "</head>\n";
				$output .= "<body>\n";
				$output .= '<div class="error">' . "\n";

				$output .= '<h1 id="reason">' . $reason . "</h1>\n";

				if ($suppress_response_codes)
				{
					$output .= '<p id="status">' . htmlspecialchars($status) . "</p>\n";
				}
				if (!is_object($content) && !is_array($content))
				{
					$output .= '<p id ="message">' . $content . "</p>\n";
				}
				else
				{
					$output .= '<p id ="message">' . json_encode($content) . "</p>\n";
				}

				$output .= "</div>\n";
				$output .= "</body>\n";
				$output .= "</html>";
			break;
			case "application/xml":
				$output .= Xml::encode($content);
			break;
			case 'application/json':
				$output .= json_encode($content);
			break;
			case 'application/vnd.php.serialized':
				$output .= serialize($content);
			break;
			case 'application/php':
				$output .= var_export($content, true);
			break;
			case 'application/x-www-form-urlencoded':
				if (!is_object($content))
				{
					$output .= $content;
				}
				else
				{
					$output .= json_encode($content);
				}
			break;
		}

		return parent::setContent($output);
	}

	/**
	 * Sends HTTP headers and content.
	 *
	 * @param   boolean  $flush
	 * @return  object   Response
	 */
	public function send($flush = false)
	{
		if (!$this->getContent() && $this->original && $this->headers->get('Content-Type'))
		{
			$this->setContent($this->original);
		}

		return parent::send($flush);
	}
}
