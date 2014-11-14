<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Cron plugin for members
 */
class plgCronMembers extends JPlugin
{
	/**
	 * Return a list of events
	 *
	 * @return  array
	 */
	public function onCronEvents()
	{
		$this->loadLanguage();

		$obj = new stdClass();
		$obj->plugin = 'members';
		$obj->events = array(
			array(
				'name'   => 'onPointRoyalties',
				'label'  => JText::_('PLG_CRON_MEMBERS_POINT_ROYALTIES'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Calculate point royalties for members
	 *
	 * @param   object   $job  CronModelJob
	 * @return  boolean
	 */
	public function onPointRoyalties(CronModelJob $job)
	{
		/*
		jimport('joomla.error.profiler');
		$_profiler = JProfiler::getInstance('Cron');
		$_profiler->mark(__CLASS__ . '::' . __METHOD__ . '() -- Start');
		*/

		$this->database = JFactory::getDBO();

		$action = 'royalty';

		// What month/year is it now?
		$curmonth = JFactory::getDate()->format("F");
		$curyear = JFactory::getDate()->format("Y-m");
		$ref = strtotime($curyear);

		$this->_message = JText::sprintf('PLG_CRON_MEMBERS_POINT_ROYALTIES_DISTRIBUTED_ANSWERS', $curyear);
		$rmsg = JText::sprintf('PLG_CRON_MEMBERS_POINT_ROYALTIES_DISTRIBUTED_REVIEWS', $curyear);
		$resmsg = JText::sprintf('PLG_CRON_MEMBERS_POINT_ROYALTIES_DISTRIBUTED_RESOURCES', $curyear);

		// Make sure we distribute royalties only once/ month
		$MH = new \Hubzero\Bank\MarketHistory($this->database);
		$royaltyAnswers   = $MH->getRecord('', $action, 'answers', $curyear, $this->_message);
		$royaltyReviews   = $MH->getRecord('', $action, 'reviews', $curyear, $rmsg);
		$royaltyResources = $MH->getRecord('', $action, 'resources', $curyear, $resmsg);

		// Include economy classes
		if (is_file(JPATH_ROOT . DS . 'components'. DS .'com_answers' . DS . 'helpers' . DS . 'economy.php'))
		{
			require_once( JPATH_ROOT . DS . 'components'. DS .'com_answers' . DS . 'helpers' . DS . 'economy.php');
		}

		if (is_file(JPATH_ROOT . DS . 'components'. DS .'com_resources' . DS . 'helpers' . DS . 'economy.php'))
		{
			require_once( JPATH_ROOT . DS . 'components'. DS .'com_resources' . DS . 'helpers' . DS . 'economy.php');
		}

		$AE = new AnswersEconomy($this->database);
		$accumulated = 0;

		// Get Royalties on Answers
		if (!$royaltyAnswers)
		{
			$rows = $AE->getQuestions();

			if ($rows)
			{
				foreach ($rows as $r)
				{
					$AE->distribute_points($r->id, $r->q_owner, $r->a_owner, $action);
					$accumulated = $accumulated + $AE->calculate_marketvalue($r->id, $action);
				}

				// make a record of royalty payment
				if (intval($accumulated) > 0)
				{
					$MH = new \Hubzero\Bank\MarketHistory($this->database);
					$data['itemid']       = $ref;
					$data['date']         = JFactory::getDate()->toSql();
					$data['market_value'] = $accumulated;
					$data['category']     = 'answers';
					$data['action']       = $action;
					$data['log']          = $this->_message;

					if (!$MH->bind($data))
					{
						$err = $MH->getError();
					}

					if (!$MH->store())
					{
						$err = $MH->getError();
					}
				}
			}
			else
			{
				$this->_message = JText::_('PLG_CRON_MEMBERS_POINT_ROYALTIES_NO_QUESTIONS');
			}
		}
		else
		{
			$this->_message = JText::sprintf('PLG_CRON_MEMBERS_POINT_ROYALTIES_ALREADY_DISTRIBUTED_ANSWERS', $curyear);
		}

		// Get Royalties on Resource Reviews
		if (!$royaltyReviews)
		{
			// get eligible
			$RE = new ReviewsEconomy($this->database);
			$reviews = $RE->getReviews();

			// do we have ratings on reviews enabled?
			$param = JPluginHelper::getPlugin('resources', 'reviews');
			$plparam = new JRegistry($param->params);
			$voting = $plparam->get('voting');

			$accumulated = 0;
			if ($reviews && $voting)
			{
				foreach ($reviews as $r)
				{
					$RE->distribute_points($r, $action);
					$accumulated = $accumulated + $RE->calculate_marketvalue($r, $action);
				}

				$this->_message .= $rmsg;
			}
			else
			{
				$this->_message .= JText::_('PLG_CRON_MEMBERS_POINT_ROYALTIES_NO_REVIEWS');
			}

			// make a record of royalty payment
			if (intval($accumulated) > 0)
			{
				$MH = new \Hubzero\Bank\MarketHistory($this->database);
				$data['itemid']       = $ref;
				$data['date']         = JFactory::getDate()->toSql();
				$data['market_value'] = $accumulated;
				$data['category']     = 'reviews';
				$data['action']       = $action;
				$data['log']          = $rmsg;

				if (!$MH->bind($data))
				{
					$err = $MH->getError();
				}

				if (!$MH->store())
				{
					$err = $MH->getError();
				}
			}
		}
		else
		{
			$this->_message .= JText::sprintf('PLG_CRON_MEMBERS_POINT_ROYALTIES_ALREADY_DISTRIBUTED_REVIEWS', $curyear);
		}

		// Get Royalties on Resources
		if (!$royaltyResources)
		{
			// get eligible
			$ResE = new ResourcesEconomy($this->database);
			$cons = $ResE->getCons();

			$accumulated = 0;
			if ($cons)
			{
				foreach ($cons as $con)
				{
					$ResE->distribute_points($con, $action);
					$accumulated = $accumulated + $con->ranking;
				}

				$this->_message .= $resmsg;
			}
			else
			{
				$this->_message .= JText::_('PLG_CRON_MEMBERS_POINT_ROYALTIES_NO_RESOURCES');
			}

			// make a record of royalty payment
			if (intval($accumulated) > 0)
			{
				$MH = new \Hubzero\Bank\MarketHistory($this->database);
				$data['itemid']       = $ref;
				$data['date']         = JFactory::getDate()->toSql();
				$data['market_value'] = $accumulated;
				$data['category']     = 'resources';
				$data['action']       = $action;
				$data['log']          = $resmsg;

				if (!$MH->bind($data))
				{
					$err = $MH->getError();
				}

				if (!$MH->store())
				{
					$err = $MH->getError();
				}
			}
		}
		else
		{
			$this->_message .= JText::sprintf('PLG_CRON_MEMBERS_POINT_ROYALTIES_ALREADY_DISTRIBUTED_RESOURCES', $curyear);
		}

		//$time_end = microtime(true);
		//$time = $time_end - $time_start;

		//echo "Computed in $time seconds\n";
		//$_profiler->mark(__CLASS__ . '::' . __METHOD__ . '() -- End');

		return true;
	}
}

