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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Eprivacy;

use Hubzero\Module\Module;
use App;

/**
 * Module class for site activity
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		if (!App::isSite() || !User::isGuest())
		{
			return;
		}

		if (User::getState($this->module->module))
		{
			return;
		}

		$this->moduleid = $this->params->get('moduleid', 'eprivacy');
		$this->duration = $this->params->get('duration', 365);

		// Get current unix timestamp
		$now = time() + (Config::get('offset') * 60 * 60);

		$expires = $now + 60*60*24* intval($this->duration);

		$hide = Request::cookie($this->moduleid, '');

		if (!$hide && Request::getVar($this->moduleid, '', 'get'))
		{
			setcookie($this->moduleid, 'acknowledged', $expires);
			return;
		}

		if ($hide)
		{
			return;
		}

		$this->message  = $this->params->get('message', Lang::txt('MOD_EPRIVACY_DEFAULT_MESSAGE', Config::get('sitename')));

		$uri  = Request::getString('REQUEST_URI', '', 'server');
		$uri .= (strstr($uri, '?')) ? '&' : '?';
		$uri .= $this->moduleid . '=close';

		$this->uri = $uri;

		// Get the view
		parent::display();
	}
}
