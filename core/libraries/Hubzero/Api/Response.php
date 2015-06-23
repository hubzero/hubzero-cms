<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	 * @var mixed
	 */
	public $original;

	/**
	 * Set the content on the response.
	 *
	 * @param  mixed  $content
	 * @return $this
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
	 * @return  object  Response
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
