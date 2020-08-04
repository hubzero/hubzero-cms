<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\User;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;

/**
 * User class for terms of use functions
 **/
class Terms extends Base implements CommandInterface
{
	/**
	 * Default (required) command
	 *
	 * Generates list of available commands and their respective tasks
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->help();
	}

	/**
	 * Help doc for user command
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output
		     ->getHelpOutput()
		     ->addOverview('Functions for updating hub user terms of use.')
		     ->render();
	}

	/**
	 * Clear terms of use agreements
	 *
	 * @return  void
	 */
	public function clear()
	{
		// Initialize confirm
		$confirm = 'no';

		if (!$this->output->isInteractive() && !$this->arguments->getOpt('f'))
		{
			$this->output->addLine('To forcibly clear all terms of use agreements for all users, please provide the -f flag. This action is irreversable.', 'warning');
			return;
		}
		else if (!$this->output->isInteractive() && $this->arguments->getOpt('f'))
		{
			$confirm = 'yes';
		}
		else if ($this->output->isInteractive())
		{
			// Confirm clearing
			$confirm = $this->output->getResponse('Are you sure you want to clear Terms of Use for all users? This will also require users to agree to new terms with next login? (yes/no)');
		}

		// Did we get a yes?
		if (strtolower($confirm) == 'yes' || strtolower($confirm) == 'y')
		{
			// Get db object
			$dbo = App::get('db');

			// Update registration config value to require re-agreeing upon next login
			$params = \Component::params('com_members');
			$currentTOU = $params->get('registrationTOU', 'RHRH');
			$newTOU     = substr_replace($currentTOU, 'R', 3);
			$params->set('registrationTOU', $newTOU);

			// Update registration param in db
			$query = "UPDATE `#__extensions` SET `params`=" . $dbo->quote($params->toString()) . " WHERE `name`='com_members'";
			$dbo->setQuery($query);
			if (!$dbo->query())
			{
				$this->output->error('Unable to set registration field TOU to required on next update.');
			}

			// Clear all old TOU states
			if ($dbo->tableExists('#__users') && $dbo->tableHasField('#__users', 'usageAgreement'))
			{
				$dbo->setQuery("UPDATE `#__users` SET `usageAgreement`=0;");
				if (!$dbo->query())
				{
					$this->output->error('Unable to clear users terms of use.');
				}
			}

			if ($dbo->tableExists('#__xprofiles') && $dbo->tableHasField('#__xprofiles', 'usageAgreement'))
			{
				$dbo->setQuery("UPDATE `#__xprofiles` SET `usageAgreement`=0;");
				if (!$dbo->query())
				{
					$this->output->error('Unable to clear xprofiles terms of use.');
				}
			}

			// Output message to let admin know everything went well
			$this->output->addLine('Terms of Use successfully cleared & registration param updated!', 'success');
		}
		else
		{
			$this->output->addLine('Operation aborted.');
		}
	}
}
