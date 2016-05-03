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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Bank\MarketHistory;
use Hubzero\Bank\Transaction;
use Notify;
use Request;
use Config;
use Route;
use User;
use Date;
use Lang;
use App;

/**
 * Members controller class for user points
 */
class Points extends AdminController
{
	/**
	 * Display an overview of point earnings
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// Get top earners
		$this->database->setQuery("SELECT * FROM #__users_points ORDER BY earnings DESC, balance DESC LIMIT 15");
		$this->view->rows = $this->database->loadObjectList();

		$thismonth = Date::of('now')->format('Y-m');
		$lastmonth = Date::of(time() - (32 * 24 * 60 * 60))->format('Y-m');

		// Get overall earnings
		$this->view->stats[] = array(
			'memo'          => 'Earnings - Total',
			'class'         => 'earntotal',
			'alltimepts'    => Transaction::getTotals('', 'deposit', '', 0, '', '', 1, ''),
			'thismonthpts'  => Transaction::getTotals('', 'deposit', '', 0, '', '', 1, $thismonth),
			'lastmonthpts'  => Transaction::getTotals('', 'deposit', '', 0, '', '', 1, $lastmonth),
			'alltimetran'   => Transaction::getTotals('', 'deposit', '', 0, '', '', 1, '', $calc=2),
			'thismonthtran' => Transaction::getTotals('', 'deposit', '', 0, '', '', 1, $thismonth, $calc=2),
			'lastmonthtran' => Transaction::getTotals('', 'deposit', '', 0, '', '', 1, $lastmonth, $calc=2),
			'avg'           => round(Transaction::getTotals('', 'deposit', '', 0, '', '', 1, '', $calc=1))
		);

		// Get overall earnings on Answers
		$this->view->stats[] = array(
			'memo'          => 'Earnings: Answers',
			'class'         => 'earn',
			'alltimepts'    => Transaction::getTotals('answers', 'deposit', '', 0, '', '', 1, ''),
			'thismonthpts'  => Transaction::getTotals('answers', 'deposit', '', 0, '', '', 1, $thismonth),
			'lastmonthpts'  => Transaction::getTotals('answers', 'deposit', '', 0, '', '', 1, $lastmonth),
			'alltimetran'   => Transaction::getTotals('answers', 'deposit', '', 0, '', '', 1, '', $calc=2),
			'thismonthtran' => Transaction::getTotals('answers', 'deposit', '', 0, '', '', 1, $thismonth, $calc=2),
			'lastmonthtran' => Transaction::getTotals('answers', 'deposit', '', 0, '', '', 1, $lastmonth, $calc=2),
			'avg'           => round(Transaction::getTotals('answers', 'deposit', '', 0, '', '', 1, '', $calc=1))
		);

		// Get overall earnings on Wishes
		$this->view->stats[] = array(
			'memo'          => 'Earnings: Wish List',
			'class'         => 'earn',
			'alltimepts'    => Transaction::getTotals('wish', 'deposit', '', 0, '', '', 1, ''),
			'thismonthpts'  => Transaction::getTotals('wish', 'deposit', '', 0, '', '', 1, $thismonth),
			'lastmonthpts'  => Transaction::getTotals('wish', 'deposit', '', 0, '', '', 1, $lastmonth),
			'alltimetran'   => Transaction::getTotals('wish', 'deposit', '', 0, '', '', 1, '', $calc=2),
			'thismonthtran' => Transaction::getTotals('wish', 'deposit', '', 0, '', '', 1, $thismonth, $calc=2),
			'lastmonthtran' => Transaction::getTotals('wish', 'deposit', '', 0, '', '', 1, $lastmonth, $calc=2),
			'avg'           => round(Transaction::getTotals('wish', 'deposit', '', 0, '', '', 1, '', $calc=1))
		);

		// Get overall spending
		$this->view->stats[] = array(
			'memo'          => 'Spending - Total',
			'class'         => 'spendtotal',
			'alltimepts'    => Transaction::getTotals('', 'withdraw', '', 0, '', '', 1, ''),
			'thismonthpts'  => Transaction::getTotals('', 'withdraw', '', 0, '', '', 1, $thismonth),
			'lastmonthpts'  => Transaction::getTotals('', 'withdraw', '', 0, '', '', 1, $lastmonth),
			'alltimetran'   => Transaction::getTotals('', 'withdraw', '', 0, '', '', 1, '', $calc=2),
			'thismonthtran' => Transaction::getTotals('', 'withdraw', '', 0, '', '', 1, $thismonth, $calc=2),
			'lastmonthtran' => Transaction::getTotals('', 'withdraw', '', 0, '', '', 1, $lastmonth, $calc=2),
			'avg'           => round(Transaction::getTotals('', 'withdraw', '', 0, '', '', 1, '', $calc=1))
		);

		// Get overall spending in Store
		$this->view->stats[] = array(
			'memo'          => 'Spending: Store',
			'class'         => 'spend',
			'alltimepts'    => Transaction::getTotals('store', 'withdraw', '', 0, '', '', 1, ''),
			'thismonthpts'  => Transaction::getTotals('store', 'withdraw', '', 0, '', '', 1, $thismonth),
			'lastmonthpts'  => Transaction::getTotals('store', 'withdraw', '', 0, '', '', 1, $lastmonth),
			'alltimetran'   => Transaction::getTotals('store', 'withdraw', '', 0, '', '', 1, '', $calc=2),
			'thismonthtran' => Transaction::getTotals('store', 'withdraw', '', 0, '', '', 1, $thismonth, $calc=2),
			'lastmonthtran' => Transaction::getTotals('store', 'withdraw', '', 0, '', '', 1, $lastmonth, $calc=2),
			'avg'           => round(Transaction::getTotals('store', 'withdraw', '', 0, '', '', 1, '', $calc=1))
		);

		// Get overall spending on Answers
		$this->view->stats[] = array(
			'memo'          => 'Spending: Answers',
			'class'         => 'spend',
			'alltimepts'    => Transaction::getTotals('answers', 'withdraw', '', 0, '', '', 1, ''),
			'thismonthpts'  => Transaction::getTotals('answers', 'withdraw', '', 0, '', '', 1, $thismonth),
			'lastmonthpts'  => Transaction::getTotals('answers', 'withdraw', '', 0, '', '', 1, $lastmonth),
			'alltimetran'   => Transaction::getTotals('answers', 'withdraw', '', 0, '', '', 1, '', $calc=2),
			'thismonthtran' => Transaction::getTotals('answers', 'withdraw', '', 0, '', '', 1, $thismonth, $calc=2),
			'lastmonthtran' => Transaction::getTotals('answers', 'withdraw', '', 0, '', '', 1, $lastmonth, $calc=2),
			'avg'           => round(Transaction::getTotals('answers', 'withdraw', '', 0, '', '', 1, '', $calc=1))
		);

		// Get overall spending on Wishes
		$this->view->stats[] = array(
			'memo'          => 'Spending: Wish List',
			'class'         => 'spend',
			'alltimepts'    => Transaction::getTotals('wish', 'withdraw', '', 0, '', '', 1, ''),
			'thismonthpts'  => Transaction::getTotals('wish', 'withdraw', '', 0, '', '', 1, $thismonth),
			'lastmonthpts'  => Transaction::getTotals('wish', 'withdraw', '', 0, '', '', 1, $lastmonth),
			'alltimetran'   => Transaction::getTotals('wish', 'withdraw', '', 0, '', '', 1, '', $calc=2),
			'thismonthtran' => Transaction::getTotals('wish', 'withdraw', '', 0, '', '', 1, $thismonth, $calc=2),
			'lastmonthtran' => Transaction::getTotals('wish', 'withdraw', '', 0, '', '', 1, $lastmonth, $calc=2),
			'avg'           => round(Transaction::getTotals('wish', 'withdraw', '', 0, '', '', 1, '', $calc=1))
		);

		// Get royalties
		$this->view->stats[] = array(
			'memo'          => 'Royalties - Total',
			'class'         => 'royaltytotal',
			'alltimepts'    => Transaction::getTotals('', 'deposit', '', $royalty=1, '', '', 1, ''),
			'thismonthpts'  => Transaction::getTotals('', 'deposit', '', $royalty=1, '', '', 1, $thismonth),
			'lastmonthpts'  => Transaction::getTotals('', 'deposit', '', $royalty=1, '', '', 1, $lastmonth),
			'alltimetran'   => Transaction::getTotals('', 'deposit', '', $royalty=1, '', '', 1, '', $calc=2),
			'thismonthtran' => Transaction::getTotals('', 'deposit', '', $royalty=1, '', '', 1, $thismonth, $calc=2),
			'lastmonthtran' => Transaction::getTotals('', 'deposit', '', $royalty=1, '', '', 1, $lastmonth, $calc=2),
			'avg'           => round(Transaction::getTotals('', 'deposit', '', $royalty=1, '', '', 1, '', $calc=1))
		);

		// Get royalties on answers
		$this->view->stats[] = array(
			'memo'          => 'Royalties: Answers',
			'class'         => 'royalty',
			'alltimepts'    => Transaction::getTotals('answers', 'deposit', '', $royalty=1, '', '', 1, ''),
			'thismonthpts'  => Transaction::getTotals('answers', 'deposit', '', $royalty=1, '', '', 1, $thismonth),
			'lastmonthpts'  => Transaction::getTotals('answers', 'deposit', '', $royalty=1, '', '', 1, $lastmonth),
			'alltimetran'   => Transaction::getTotals('answers', 'deposit', '', $royalty=1, '', '', 1, '', $calc=2),
			'thismonthtran' => Transaction::getTotals('answers', 'deposit', '', $royalty=1, '', '', 1, $thismonth, $calc=2),
			'lastmonthtran' => Transaction::getTotals('answers', 'deposit', '', $royalty=1, '', '', 1, $lastmonth, $calc=2),
			'avg'           => round(Transaction::getTotals('answers', 'deposit', '', $royalty=1, '', '', 1, '', $calc=1))
		);

		// Get royalties on reviews
		$this->view->stats[] = array(
			'memo'          => 'Royalties: Reviews',
			'class'         => 'royalty',
			'alltimepts'    => Transaction::getTotals('review', 'deposit', '', $royalty=1, '', '', 1, ''),
			'thismonthpts'  => Transaction::getTotals('review', 'deposit', '', $royalty=1, '', '', 1, $thismonth),
			'lastmonthpts'  => Transaction::getTotals('review', 'deposit', '', $royalty=1, '', '', 1, $lastmonth),
			'alltimetran'   => Transaction::getTotals('review', 'deposit', '', $royalty=1, '', '', 1, '', $calc=2),
			'thismonthtran' => Transaction::getTotals('review', 'deposit', '', $royalty=1, '', '', 1, $thismonth, $calc=2),
			'lastmonthtran' => Transaction::getTotals('review', 'deposit', '', $royalty=1, '', '', 1, $lastmonth, $calc=2),
			'avg'           => round(Transaction::getTotals('review', 'deposit', '', $royalty=1, '', '', 1, '', $calc=1))
		);

		// Get royalties on resource contributions
		$this->view->stats[] = array(
			'memo'          => 'Royalties: Resources',
			'class'         => 'royalty',
			'alltimepts'    => Transaction::getTotals('resource', 'deposit', '', $royalty=1, '', '', 1, ''),
			'thismonthpts'  => Transaction::getTotals('resource', 'deposit', '', $royalty=1, '', '', 1, $thismonth),
			'lastmonthpts'  => Transaction::getTotals('resource', 'deposit', '', $royalty=1, '', '', 1, $lastmonth),
			'alltimetran'   => Transaction::getTotals('resource', 'deposit', '', $royalty=1, '', '', 1, '', $calc=2),
			'thismonthtran' => Transaction::getTotals('resource', 'deposit', '', $royalty=1, '', '', 1, $thismonth, $calc=2),
			'lastmonthtran' => Transaction::getTotals('resource', 'deposit', '', $royalty=1, '', '', 1, $lastmonth, $calc=2),
			'avg'           => round(Transaction::getTotals('resource', 'deposit', '', $royalty=1, '', '', 1, '', $calc=1))
		);

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Edit an entry
	 *
	 * @return     void
	 */
	public function editTask()
	{
		if ($uid = Request::getInt('uid', 0))
		{
			$this->view->row = \Hubzero\Bank\Account::oneByUserId($uid);

			if (!$this->view->row->get('balance'))
			{
				$this->view->row->set('uid', $uid);
				$this->view->row->set('balance', 0);
				$this->view->row->set('earnings', 0);
			}

			$this->database->setQuery("SELECT * FROM `#__users_transactions` WHERE uid=" . $uid . " ORDER BY created DESC, id DESC");
			$this->view->history = $this->database->loadObjectList();
		}
		else
		{
			$this->view->setLayout('find');
		}

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Cancel a task and redirect to main view
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$account = Request::getVar('account', array(), 'post');

		$row = \Hubzero\Bank\Account::blank()->set($account);

		$row->set('uid', intval($row->get('uid')));
		$row->set('balance', intval($row->get('balance')));
		$row->set('earnings', intval($row->get('earnings')));

		$data = Request::getVar('transaction', array(), 'post');

		if (isset($data['amount']) && intval($data['amount']) > 0)
		{
			$data['uid'] = $row->get('uid');
			$data['created'] = Date::toSql();
			$data['amount'] = intval($data['amount']);
			if (!isset($data['category']) || !$data['category'])
			{
				$data['category'] = 'general';
			}
			if (!isset($data['description']) || !$data['description'])
			{
				$data['description'] = 'Reason unspecified';
			}
			if (!isset($data['type']) || !$data['type'])
			{
				$data['type'] = '';
			}

			switch ($data['type'])
			{
				case 'withdraw':
					$row->balance  -= $data['amount'];
				break;
				case 'deposit':
					$row->balance  += $data['amount'];
					$row->earnings += $data['amount'];
				break;
				case 'creation':
					$row->balance  = $data['amount'];
					$row->earnings = $data['amount'];
				break;
			}

			$data['balance'] = $row->balance;

			$BT = Transaction::blank()->set($data);

			if (!Transaction::save())
			{
				App::abort(500, $row->getError());
				return;
			}
		}

		if (!$row->save())
		{
			App::abort(500, $row->getError());
			return;
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=edit&uid=' . $row->uid, false),
			Lang::txt('User info saved')
		);
	}

	/**
	 * Configure items that can earn points
	 *
	 * @return  void
	 */
	public function configTask()
	{
		$this->database->setQuery("SELECT * FROM `#__users_points_config`");
		$this->view->params = $this->database->loadObjectList();

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save config settings for items that can earn points
	 *
	 * @return     void
	 */
	public function saveconfigTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$points = Request::getVar('points', array());
		$descriptions = Request::getVar('description', array());
		$aliases = Request::getVar('alias', array());

		$this->database->setQuery('DELETE FROM `#__users_points_config`');
		$this->database->query();

		for ($i=0; $i < count($points); $i++)
		{
			$point = intval($points[$i]);
			$description = $descriptions[$i];
			$alias = $aliases[$i];
			if ($point)
			{
				$id = intval($i);
				$this->database->setQuery("INSERT INTO `#__users_points_config` (`id`,`description`,`alias`,`points`) VALUES ($id,'$description','$alias', '$point')");
				$this->database->query();
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=config', false),
			Lang::txt('Config Saved')
		);
	}

	/**
	 * Perform batch operations
	 *
	 * @return     void
	 */
	public function batchTask()
	{
		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Process a batch of records
	 *
	 * @return     void
	 */
	public function process_batchTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$duplicate = 0;

		$log = Request::getVar('log', array());
		$log = array_map('trim', $log);
		$log['category'] = (isset($log['category']) && $log['category']) ? $log['category'] : 'general';
		$log['action']   = (isset($log['action']) && $log['action'])     ? $log['action']   : 'batch';
		$log['ref']      = (isset($log['ref']) && $log['ref'])           ? $log['ref']      : '';

		$data = Request::getVar('transaction', array());
		$data = array_map('trim', $data);

		$when = Date::toSql();

		// make sure this function was not already run
		$duplicate = MarketHistory::getRecord(intval($log['ref']), $log['action'], $log['category'], '', $data['description']);

		if ($data['amount'] && $data['description'] && $data['users'])
		{
			if (!$duplicate)
			{ // run only once
				// get array of affected users
				$users = str_replace(' ', ',', $data['users']);
				$users = explode(',', $users);
				$users = array_unique($users); // get rid of duplicates

				foreach ($users as $user)
				{
					$validuser = \User::getInstance($user);
					if ($user && $validuser->get('id'))
					{
						$BTL = new \Hubzero\Bank\Teller($user);
						switch ($data['type'])
						{
							case 'withdraw':
								$BTL->withdraw($data['amount'], $data['description'], $log['category'], $log['ref']);
							break;
							case 'deposit':
								$BTL->deposit($data['amount'], $data['description'], $log['category'], $log['ref']);
							break;
						}
					}
				}

				// Save log
				$MH = MarketHistory::blank();
				$dat = array();
				$dat['itemid']       = $log['ref'];
				$dat['date']         = Date::toSql();
				$dat['market_value'] = $data['amount'];
				$dat['category']     = $log['category'];
				$dat['action']       = $log['action'];
				$dat['log']          = $data['description'];

				if (!$MH->set($dat))
				{
					$this->setError($MH->getError());
				}

				if (!$this->getError())
				{
					if (!$MH->save())
					{
						$this->setError($MH->getError());
					}
				}

				if ($err = $this->getError())
				{
					Notify::error($err);
				}
				else
				{
					Notify::success(Lang::txt('Batch transaction was processed successfully.'));
				}
			}
			else
			{
				Notify::warning(Lang::txt('This batch transaction was already processed earlier. Use a different identifier if you need to run it again.'));
			}
		}
		else
		{
			Notify::error(Lang::txt('Could not process. Some required fields are missing.'));
		}

		// show output if run manually
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=batch', false)
		);
	}

	/**
	 * Calculate royalties
	 *
	 * @return     void
	 */
	public function royaltyTask()
	{
		$auto = Request::getInt('auto', 0);
		$action = 'royalty';

		if (!$auto)
		{
			$who = User::get('id');
		}
		else
		{
			$who = 0;
		}

		// What month/year is it now?
		$curmonth = Date::of('now')->format("F");
		$curyear = Date::of('now')->format("Y-m");
		$ref = strtotime($curyear);
		$this->_message = 'Royalties on Answers for '.$curyear.' were distributed successfully.';
		$rmsg = 'Royalties on Reviews for '.$curyear.' were distributed successfully.';
		$resmsg = 'Royalties on Resources for '.$curyear.' were distributed successfully.';

		// Make sure we distribute royalties only once/ month
		$royaltyAnswers = MarketHistory::getRecord('', $action, 'answers', $curyear, $this->_message);
		$royaltyReviews = MarketHistory::getRecord('', $action, 'reviews', $curyear, $rmsg);
		$royaltyResources = MarketHistory::getRecord('', $action, 'resources', $curyear, $resmsg);

		// Include economy classes
		if (is_file(PATH_CORE . DS . 'components'. DS .'com_answers' . DS . 'helpers' . DS . 'economy.php'))
		{
			require_once(PATH_CORE . DS . 'components'. DS .'com_answers' . DS . 'helpers' . DS . 'economy.php');
		}
		if (is_file(PATH_CORE . DS . 'components'. DS .'com_resources' . DS . 'helpers' . DS . 'economy.php'))
		{
			require_once(PATH_CORE . DS . 'components'. DS .'com_resources' . DS . 'helpers' . DS . 'economy.php');
		}

		$AE = new \Components\Answers\Helpers\Economy($this->database);
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
					$MH = MarketHistory::blank();
					$data['itemid']       = $ref;
					$data['date']         = Date::toSql();
					$data['market_value'] = $accumulated;
					$data['category']     = 'answers';
					$data['action']       = $action;
					$data['log']          = $this->_message;

					if (!$MH->set($data))
					{
						$err = $MH->getError();
					}

					if (!$MH->save())
					{
						$err = $MH->getError();
					}
				}
			}
			else
			{
				$this->_message = 'There were no questions eligible for royalty payment. ';
			}
		}
		else
		{
			$this->_message = 'Royalties on Answers for '.$curyear.' were previously distributed. ';
		}

		// Get Royalties on Resource Reviews
		if (!$royaltyReviews)
		{
			// get eligible
			$RE = new \Components\Resources\Helpers\Economy\Reviews($this->database);
			$reviews = $RE->getReviews();

			// do we have ratings on reviews enabled?
			$plparam = Plugin::params('resources', 'reviews');
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
				$this->_message .= 'There were no reviews eligible for royalty payment. ';
			}

			// make a record of royalty payment
			if (intval($accumulated) > 0)
			{
				$MH = MarketHistory::blank();
				$data['itemid']       = $ref;
				$data['date']         = Date::toSql();
				$data['market_value'] = $accumulated;
				$data['category']     = 'reviews';
				$data['action']       = $action;
				$data['log']          = $rmsg;

				if (!$MH->set($data))
				{
					$err = $MH->getError();
				}

				if (!$MH->save())
				{
					$err = $MH->getError();
				}
			}
		}
		else
		{
			$this->_message .= 'Royalties on Reviews for '.$curyear.' were previously distributed. ';
		}

		// Get Royalties on Resources
		if (!$royaltyResources)
		{
			// get eligible
			$ResE = new \Components\Resources\Helpers\Economy\Reviews($this->database);
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
				$this->_message .= 'There were no resources eligible for royalty payment.';
			}

			// make a record of royalty payment
			if (intval($accumulated) > 0)
			{
				$MH = MarketHistory::blank();
				$data['itemid']       = $ref;
				$data['date']         = Date::toSql();
				$data['market_value'] = $accumulated;
				$data['category']     = 'resources';
				$data['action']       = $action;
				$data['log']          = $resmsg;

				if (!$MH->set($data))
				{
					$err = $MH->getError();
				}

				if (!$MH->save())
				{
					$err = $MH->getError();
				}
			}
		}
		else
		{
			$this->_message .= 'Royalties on Resources for ' . $curyear . ' were previously distributed.';
		}

		if (!$auto)
		{
			// show output if run manually
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt($this->_message)
			);
		}
	}
}

