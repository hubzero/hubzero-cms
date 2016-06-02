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

// No direct access
defined('_HZEXEC_') or die();

include_once(PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Warehouse.php');

/**
 * Courses Plugin class for store
 */
class plgCoursesStore extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return     array
	 */
	public function onOfferingEdit()
	{
		$area = array(
			'name'  => $this->_name,
			'title' => Lang::txt('PLG_COURSES_' . strtoupper($this->_name))
		);
		return $area;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      array   $areas     Active area(s)
	 * @param      string  $rtrn      Data to be returned
	 * @return     array
	 */
	/*public function onCourseView($course, $active=null)
	{
		$arr = array(
			'name'     => $this->_name,
			'html'     => '',
			'metadata' => '',
			'controls' => ''
		);

		$view = $this->view('default', 'metadata');
		$view->option     = Request::getCmd('option', 'com_courses');
		$view->controller = Request::getWord('controller', 'course');
		$view->course     = $course;

		$arr['controls'] = $view->loadTemplate();

		return $arr;
	}*/

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      array   $areas     Active area(s)
	 * @param      string  $rtrn      Data to be returned
	 * @return     array
	 */
	public function onCourseEnrollLink($course, $offering, $section)
	{
		if (!$course->exists() || !$offering->exists())
		{
			return;
		}

		$product = null;

		$url = $offering->link() . '&task=enroll';

		if ($offering->params('store_product_id', 0))
		{
			$warehouse = new \Components\Storefront\Models\Warehouse();
			// Get course by pID returned with $course->add() above
			try
			{
				$product = $warehouse->getCourse($offering->params('store_product_id', 0));
			}
			catch (Exception $e)
			{
				echo 'ERROR: ' . $e->getMessage();
			}
		}

		if (is_object($product) && $product->getId())
		{
			$url = 'index.php?option=com_cart'; //index.php?option=com_storefront/product/' . $product->pId;
		}

		return $url;
	}

	/**
	 * Actions to perform after saving a course
	 *
	 * @param      object  $model \Components\Courses\Models\Course
	 * @param      boolean $isNew Is this a newly created entry?
	 * @return     void
	 */
	public function onOfferingSave($model)
	{
		if (!$model->exists())
		{
			return;
		}

		$params = new \Hubzero\Config\Registry($model->get('params'));

		if ($params->get('store_product', 0))
		{
			$course = \Components\Courses\Models\Course::getInstance($model->get('course_id'));

			$title       = $course->get('title') . ' (' . $model->get('title') . ')';
			$description = $course->get('blurb');
			$price       = $params->get('store_price', '30.00');
			$duration    = $params->get('store_membership_duration', '1 YEAR');

			if (!$params->get('store_product_id', 0))
			{
				include_once(PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Course.php');

				$product = new \Components\Storefront\Models\Course();
				$product->setName($title);
				$product->setDescription($description);
				$product->setPrice($price);
				// We don't want products showing up for non-published courses
				if ($model->get('state') != 1)
				{
					$product->setActiveStatus(0);
				}
				else
				{
					$product->setActiveStatus(1);
				}
				// Membership model: membership duration period (must me in MySQL date format: 1 DAY, 2 MONTH, 3 YEAR...)
				$product->setTimeToLive($duration);
				// Course alias id
				$product->setCourseId($course->get('alias'));
				$product->setOfferingId($model->get('alias'));
				try
				{
					$product->save();

					$params->set('store_product_id', $product->getId());

					$model->set('params', $params->toString());
					$model->store();
				}
				catch (Exception $e)
				{
					$this->setError('ERROR: ' . $e->getMessage());
				}
			}
			else
			{
				$warehouse = new \Components\Storefront\Models\Warehouse();
				try
				{
					// Get course by pID returned with $course->add() above
					$product = $warehouse->getCourse($params->get('store_product_id', 0));
					$product->setName($title);
					$product->setDescription($description);
					$product->setPrice($price);
					$product->setTimeToLive($duration);
					if ($model->get('state') != 1)
					{
						$product->setActiveStatus(0);
					}
					else
					{
						$product->setActiveStatus(1);
					}
					$product->save();
				}
				catch (Exception $e)
				{
					$this->setError('ERROR: ' . $e->getMessage());
				}
			}
		}
	}

	/**
	 * Actions to perform after deleting a course
	 *
	 * @param      object  $model \Components\Courses\Models\Course
	 * @return     void
	 */
	public function onOfferingDelete($model)
	{
		if (!$model->exists())
		{
			return;
		}

		$params = new \Hubzero\Config\Registry($model->get('params'));

		if ($product = $params->get('store_product_id', 0))
		{
			$warehouse = new \Components\Storefront\Models\Warehouse();
			$product = $warehouse->getCourse($product);
			$product->delete();
		}
	}

	/**
	 * Actions to perform after saving an offering
	 *
	 * @param      object  $model \Components\Courses\Models\Offering
	 * @param      boolean $isNew Is this a newly created entry?
	 * @return     void
	 */
	public function onCourseSave($model)
	{
		if (!$model->exists())
		{
			return;
		}
	}

	/**
	 * Actions to perform after deleting an offering
	 *
	 * @param      object  $model \Components\Courses\Models\Offering
	 * @return     void
	 */
	public function onCourseDelete($model)
	{
		if (!$model->exists())
		{
			return;
		}
	}

	/**
	 * Actions to perform after saving an offering
	 *
	 * @param      object  $model \Components\Courses\Models\Section
	 * @param      boolean $isNew Is this a newly created entry?
	 * @return     void
	 */
	public function onSectionSave($model, $isNew=false)
	{
		if (!$model->exists())
		{
			return;
		}
	}

	/**
	 * Actions to perform after deleting an offering
	 *
	 * @param      object  $model \Components\Courses\Models\Section
	 * @return     void
	 */
	public function onSectionDelete($model)
	{
		if (!$model->exists())
		{
			return;
		}
	}

	/**
	 * Actions to perform after deleting an offering
	 *
	 * @param      object  $model \Components\Courses\Models\Section
	 * @param      boolean $isNew Is this a newly created entry?
	 * @return     void
	 */
	public function onAfterSaveCoupon($model, $isNew=false)
	{
		if (!$model->exists())
		{
			return;
		}
		if ($isNew && Request::getInt('store_product', 0))
		{
			include_once(PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Coupon.php');

			try
			{
				// Constructor take the coupon code
				$coupon = new \Components\Storefront\Models\Coupon($model->get('code'));
				// Couponn description (shows up in the cart)
				$coupon->setDescription(Request::getVar('description', 'Test coupon, 10% off product with ID 111'));
				// Expiration date
				$coupon->setExpiration($model->get('created'));
				// Number of times coupon can be used (unlimited by default)
				$coupon->setUseLimit(1);

				// Product the coupon will be applied to:
				// first parameter: product ID
				// second parameter [optional, unlimited by default]: max quantity of products coupon will be applied to (if buying multiple)
				//$section = new CorusesModelSection($model->get('section_id'));

				include_once(PATH_CORE . DS. 'components' . DS . 'com_storefront' . DS . 'models' . DS . 'Course.php');

				$product = new \Components\Storefront\Models\Course();
				$product->setCourseId($model->find('course'));

				$coupon->addObject($product->getId());
				// Action, only 'discount' for now
				// second parameter either percentage ('10%') or absolute dollar value ('20')
				$coupon->setAction('discount', '100%');
				// Add coupon
				$coupon->add();
			}
			catch (Exception $e)
			{
				echo 'ERROR: ' . $e->getMessage();
			}
			return;
		}
	}

	/**
	 * Actions to perform after deleting an offering
	 *
	 * @param      object  $model \Components\Courses\Models\Section
	 * @param      boolean $isNew Is this a newly created entry?
	 * @return     void
	 */
	public function onAfterDeleteCoupon($model)
	{
		if (!$model->exists())
		{
			return;
		}

		$warehouse = new \Components\Storefront\Models\Warehouse();
		try
		{
			$warehouse->deleteCoupon($model->get('code'));
		}
		catch (Exception $e)
		{
			echo 'ERROR: ' . $e->getMessage();
		}
		return;
	}
}
