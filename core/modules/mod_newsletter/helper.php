<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 * All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Newsletter;

use Hubzero\Module\Module;
use User;

/**
 * Module class for displaying Newsletter Mailing List Sign up
 */
class Helper extends Module
{
	/**
	 * Display module
	 *
	 * @return  void
	 */
	public function display()
	{
		// Instantiate database object
		$this->database = \App::get('db');

		// Get mailing list details that we are wanting users to sign up for
		$sql = "SELECT * FROM `#__newsletter_mailinglists` WHERE deleted=0 AND private=0 AND id=" . $this->database->quote($this->params->get('mailinglist', 0));
		$this->database->setQuery($sql);
		$this->mailinglist = $this->database->loadObject();

		// Get mailing list subscription if not guest
		$this->subscription   = null;
		$this->subscriptionId = null;
		if (!User::isGuest())
		{
			$sql = "SELECT * FROM `#__newsletter_mailinglist_emails` WHERE mid=" . $this->database->quote($this->params->get('mailinglist', 0)) . " AND email=" . $this->database->quote(User::get('email'));
			$this->database->setQuery($sql);
			$this->subscription = $this->database->loadObject();
		}

		// If we are unsubscribed...
		if (is_object($this->subscription) && $this->subscription->status == 'unsubscribed')
		{
			$this->subscriptionId = $this->subscription->id;
			$this->subscription   = null;
		}

		// Add stylesheets and scripts
		$this->css()
		     ->js();

		// Display module
		require $this->getLayoutPath();
	}
}
