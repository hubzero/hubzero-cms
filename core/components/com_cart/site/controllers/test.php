<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Site\Controllers;

/**
 * Cart controller class
 */
class Test extends ComponentController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		// Get the task
		$this->_task  = Request::getCmd('task', '');

		if (empty($this->_task))
		{
			$this->_task = 'display';
			$this->registerTask('__default', $this->_task);
		}

		parent::execute();
	}

	/**
	 * Display default page
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		die('no access');
	}

	/**
	 * Display default page
	 *
	 * @return     void
	 */
	public function testgroundTask()
	{

		if (0)
		{
			// CREATE COUPON
			include_once \Component::path('com_storefront') . DS . 'models' . DS . 'StorefrontModelCoupon.php';
			try
			{
				// Constructor take the coupon code
				$coupon = new Coupon('hui');
				// Coupon description (shows up in the cart)
				$coupon->setDescription('Test coupon, 10% off product with ID 3');
				// Expiration date
				$coupon->setExpiration('Feb 22, 2022');
				// Number of times coupon can be used (unlimited by default)
				$coupon->setUseLimit(1);

				// Product the coupon will be applied to:
				// first parameter: product ID
				// second parameter [optional, unlimited by default]: max quantity of products coupon will be applied to (if buying multiple)
				$coupon->addObject(3, 1);
				// Action, only 'discount' for now
				// second parameter either percentage ('10%') or absolute dollar value ('20')
				$coupon->setAction('discount', '10%');
				// Add coupon
				$coupon->add();
			}
			catch (\Exception $e)
			{
				echo 'ERROR: ' . $e->getMessage();
			}
			return;
		}

		if (0)
		{
			// DELETE COUPON

			$warehouse = new Warehouse();
			try
			{
				$warehouse->deleteCoupon('couponcode3');
			}
			catch (\Exception $e)
			{
				echo 'ERROR: ' . $e->getMessage();
			}
			return;
		}

		if (0)
		{
			// CREATE NEW COURSE
			include_once \Component::path('com_storefront') . DS . 'models' . DS . 'Course.php';

			$course = new Course();
			$course->setName('Name of the course');
			$course->setDescription('Short description');
			$course->setPrice(12.00);
			$course->addToCollection('courses');
			// Membership model: membership duration period (must me in MySQL date format: 1 DAY, 2 MONTH, 3 YEAR...)
			$course->setTimeToLive('1 YEAR');
			// Course alias id
			$course->setCourseId('nanoscaletransistors');
			try
			{
				// Returns object with values, pId is the new product ID to link to
				$info = $course->add();
				//print_r($info);
			}
			catch (\Exception $e)
			{
				echo 'ERROR: ' . $e->getMessage();
			}
			return;
		}

		if (0)
		{
			// GET EXISTING COURSE, modify it and save

			$warehouse = new Warehouse();
			try
			{
				// Get course by pID returned with $course->add() above
				$course = $warehouse->getCourse(1);

				$course->setName('Renamed');
				$course->setDescription('New description');
				$course->setPrice(55.22);
				$course->setTimeToLive('10 YEAR');
				$course->update();
			}
			catch (\Exception $e)
			{
				echo 'ERROR: ' . $e->getMessage();
			}
			return;
		}

		if (0)
		{
			// UPDATE COURSE by recreating it
			include_once \Component::path('com_storefront') . DS . 'models' . DS . 'StorefrontModelCourse.php';
			$course = new Course();
			$course->setName('Operations Management 104');
			$course->setDescription('Operations Management 104 is some kind of test course for now...');
			$course->setPrice(13.05);
			$course->setCourseId(5);

			// Existing course ID (pID returned with $course->add() when the course was created). Must be set to be able to update.
			$course->setId(1023);
			try
			{
				$info = $course->update();
				//print_r($info);
			}
			catch (\Exception $e)
			{
				echo 'ERROR: ' . $e->getMessage();
			}
			return;
		}

		if (0)
		{
			// DELETE COURSE

			$warehouse = new Warehouse();
			// Delete by existing course ID (pID returned with $course->add() when the course was created)
			$warehouse->deleteProduct(1023);
			return;
		}

		// test type handler
		if (1)
		{
			\Hubzero\Access\Map::addUserToGroup('1003', 11);

			$table = User::getInstance(1003);

			// Trigger the onAftereStoreUser event
			Event::trigger('user.onUserAfterSave', array($table->toArray(), false, true, null));
		}
	}

	public function postTask()
	{

		//$user =& JUser::getInstance((int)1057);

		//echo '==>' . $user->get( 'gid' );

		//print_r($user);
		//$user->delete();
		//die;

		\Document::addScript(DS . 'components' . DS . 'com_cart' . DS . 'assets' . DS . 'js' . DS . 'test.js');

		$this->view->display();
	}

	public function apipostTask()
	{

		$curl_result = '';
		$curl_err = '';

		//$url = ('https://dev26.hubzero.org/api/courses/premisRegister');
		//$url = ('https://dev26.hubzero.org/api/register/premisRegister');
		$url = 'https://dev.courses.purduenext.purdue.edu/api/register/premisRegister/';

		// !! $value = urlencode(stripslashes($value));

		$data['fName'] = 'Tolik';
		$data['lName'] = 'Dusik';
		$data['email'] = 'ilya@zuki.com';
		$data['premisId'] = 'zero0';
		$data['premisEnrollmentId'] = 'primus0';
		//$data['casId'] = 'ishunko';
		$data['password'] = 'e9f5713dec55d727bb35392cec6190ce';

		$data['addRegistration'] = 'nanoscaletransistors';
		$data['dropRegistration'] = '';

		$req = 'ss=VezefruchASpEdruvE_RAmE4pesWep!A';

		foreach ($data as $key => $value)
		{
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded", "Content-Length: " . strlen($req)));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		$curl_result = @curl_exec($ch);
		$curl_err = curl_error($ch);
		curl_close($ch);

		//print_r(json_decode($curl_result));
		//print_r($curl_result);
		die('+');

		\Document::addScript(DS . 'components' . DS . 'com_cart' . DS . 'assets' . DS . 'js' . DS . 'test.js');

		$this->view->display();
	}

	public function apideleteTask()
	{
		$curl_result = '';
		$curl_err = '';

		$url = 'https://dev.courses.purduenext.purdue.edu/api/register/premisDeleteProfile/';

		// !! $value = urlencode(stripslashes($value));

		$data['email'] = 'ilya@zuki.com';

		$req = 'ss=VezefruchASpEdruvE_RAmE4pesWep!A';

		foreach ($data as $key => $value)
		{
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded", "Content-Length: " . strlen($req)));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		$curl_result = @curl_exec($ch);
		$curl_err = curl_error($ch);
		curl_close($ch);

		//print_r(json_decode($curl_result));
		//print_r($curl_result);
		die('~');
	}

	public function passportTask()
	{
		// Instantiate badges manager, provide badges provider (right now there is only one: 'PASSPORT').
		//$badges = new Hubzero_Badges('PASSPORT');

		// Get the actual badges provider class
		$passport = $badges->getProvider();

		// Set credentials and settings (outh in not secured at this point)
		$credentials = new \stdClass();
		$credentials->clientId = 43;
		$credentials->issuerId = 17;
		// These are not used so far, but have some value
		$credentials->consumer_key = 'xxx';
		$credentials->consumer_secret = 'xxx';
		$credentials->username = 'xxx';
		$credentials->password = 'xxx';

		// Set credentials
		try
		{
			$passport->setCredentials($credentials);
		}
		catch (\Exception $e)
		{
			echo $e->getMessage();
		}

		// Set badges details
		$badge = new \stdClass();
		$badge->id = 83;
		$badge->evidenceUrl = 'http://hubzero.org';

		// Award a badge
		try
		{
			// Single user
			//$passport->grantBadge($badge, 'example@example.com');

			// Multiple users
			$users = array('example@example.com', 'sample@sample.com');
			$passport->grantBadge($badge, $users);

			echo 'Badges granted';
		}
		catch (\Exception $e)
		{
			echo 'Error: ' . $e->getMessage();
		}
	}

	/**
	 * Test payment task
	 *
	 * @return     void
	 */
	public function payTask()
	{
		if (!empty($_POST['dummypay']))
		{
			$req = 'ss=VezefruchASpEdruvE_RAmE4pesWep!A';

			foreach ($_POST as $key => $value)
			{
				if ($key != 'option')
				{
					$value = urlencode(stripslashes($value));
					$req .= "&$key=$value";
				}
			}

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, Request::root() . 'cart/order/postback');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded", "Content-Length: " . strlen($req)));
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);

			$curl_result = @curl_exec($ch);
			$curl_err = curl_error($ch);
			curl_close($ch);

			//echo Route::url('index.php?option=' . 'com_cart') . 'order/complete?' . $req; die;

			// Redirect to confirmation page
			App::redirect(
				Route::url('index.php?option=' . 'com_cart') . 'order/complete?' . $req
			);
		}

		$this->view->display();

	}

	/**
	 * Test express add to cart task
	 *
	 * @return     void
	 */
	public function addTask()
	{
		$this->view->display();
	}
}
