<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
