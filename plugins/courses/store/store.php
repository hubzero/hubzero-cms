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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Courses Plugin class for store
 */
class plgCoursesStore extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		//$this->loadLanguage();
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
	public function onCourseView($course, $active=null)
	{
		$arr = array(
			'name'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => 'metadata'
			)
		);
		$view->option     = JRequest::getCmd('option', 'com_courses');
		$view->controller = JRequest::getWord('controller', 'course');
		$view->course     = $course;

		$arr['metadata'] = $view->loadTemplate();

		return $arr;
	}

	/**
	 * Actions to perform after saving a course
	 * 
	 * @param      object  $model CoursesModelCourse
	 * @param      boolean $isNew Is this a newly created entry?
	 * @return     void
	 */
	public function onAfterSaveCourse($model, $isNew=false)
	{
		if (!$model->exists())
		{
			return;
		}
		if (JRequest::getInt('store_product', 0))
		{
			if ($isNew)
			{
				ximport('Hubzero_Storefront_Product');
				$course = new Hubzero_Storefront_Course();
				$course->setName($model->get('title'));
				$course->setDescription($model->get('blurb'));
				$course->setPrice(12.00);
				// Membership model: membership duration period (must me in MySQL date format: 1 DAY, 2 MONTH, 3 YEAR...) 
				$course->setTimeToLive('1 YEAR');
				// Course alias id
				$course->setCourseId($model->get('alias'));
				try 
				{
					// Returns object with values, pId is the new product ID to link to
					$info = $course->add();
					//print_r($info);
				}
				catch(Exception $e) 
				{
					echo 'ERROR: ' . $e->getMessage();
				}
			}
			else
			{
				ximport('Hubzero_Storefront_Warehouse');
				$warehouse = new Hubzero_Storefront_Warehouse();
				try 
				{
					// Get course by pID returned with $course->add() above
					$course = $warehouse->getCourse(1023);
					$course->setName($course->get('title'));
					$course->setDescription($course->get('blurb'));
					$course->setPrice(55);
					$course->setTimeToLive('10 YEAR');
					$course->update();
				}
				catch(Exception $e) 
				{
					echo 'ERROR: ' . $e->getMessage();
				}
			}
		}
	}

	/**
	 * Actions to perform after deleting a course
	 * 
	 * @param      object  $model CoursesModelCourse
	 * @return     void
	 */
	public function onAfterDeleteCourse($model)
	{
		if (!$model->exists())
		{
			return;
		}
		ximport('Hubzero_Storefront_Warehouse');
		$warehouse = new Hubzero_Storefront_Warehouse();
		// Delete by existing course ID (pID returned with $course->add() when the course was created)
		$warehouse->deleteProduct(1023);
	}

	/**
	 * Actions to perform after saving an offering
	 * 
	 * @param      object  $model CoursesModelOffering
	 * @param      boolean $isNew Is this a newly created entry?
	 * @return     void
	 */
	public function onAfterSaveOffering($model, $isNew=false)
	{
		if (!$model->exists())
		{
			return;
		}
	}

	/**
	 * Actions to perform after deleting an offering
	 * 
	 * @param      object  $model CoursesModelOffering
	 * @return     void
	 */
	public function onAfterDeleteOffering($model)
	{
		if (!$model->exists())
		{
			return;
		}
	}

	/**
	 * Actions to perform after saving an offering
	 * 
	 * @param      object  $model CoursesModelSection
	 * @param      boolean $isNew Is this a newly created entry?
	 * @return     void
	 */
	public function onAfterSaveSection($model, $isNew=false)
	{
		if (!$model->exists())
		{
			return;
		}
	}

	/**
	 * Actions to perform after deleting an offering
	 * 
	 * @param      object  $model CoursesModelSection
	 * @return     void
	 */
	public function onAfterDeleteSection($model)
	{
		if (!$model->exists())
		{
			return;
		}
	}

	/**
	 * Actions to perform after deleting an offering
	 * 
	 * @param      object  $model CoursesModelSection
	 * @param      boolean $isNew Is this a newly created entry?
	 * @return     void
	 */
	public function onAfterSaveCoupon($model, $isNew=false)
	{
		if (!$model->exists())
		{
			return;
		}
		if ($isNew && JRequest::getInt('store_product', 0))
		{
			ximport('Hubzero_Storefront_Coupon');
			try 
			{
				// Constructor take the coupon code
				$coupon = new Hubzero_Storefront_Coupon($model->get('code'));
				// Couponn description (shows up in the cart)
				$coupon->setDescription(JRequest::getVar('description', 'Test coupon, 10% off product with ID 111'));
				// Expiration date 
				$coupon->setExpiration($model->get('created'));
				// Number of times coupon can be used (unlimited by default)
				$coupon->setUseLimit(1);

				// Product the coupon will be applied to: 
				// first parameter: product ID
				// second parameter [optional, unlimited by default]: max quantity of products coupon will be applied to (if buying multiple)
				//$section = new CorusesModelSection($model->get('section_id'));

				$product = new CorusesModelStore();
				$product->set('course_id', $model->find('course'));

				$coupon->addObject($product->get('product_id'), 1);
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
	 * @param      object  $model CoursesModelSection
	 * @param      boolean $isNew Is this a newly created entry?
	 * @return     void
	 */
	public function onAfterDeleteCoupon($model)
	{
		if (!$model->exists())
		{
			return;
		}

		ximport('Hubzero_Storefront_Warehouse');
		$warehouse = new Hubzero_Storefront_Warehouse();
		try 
		{
			$warehouse->deleteCoupon($model->get('code'));
		}
		catch(Exception $e) 
		{
			echo 'ERROR: ' . $e->getMessage();
		}
		return;
	}
}
