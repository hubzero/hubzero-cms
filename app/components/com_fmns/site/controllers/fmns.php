<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2018 HUBzero Foundation, LLC.
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
 * @author		M. Drew LaMar <drew.lamar@gmail.com>
 * @copyright Copyright 2005-2018 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */
 
namespace Components\Fmns\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Fmns\Models\Fmn;

use Request;
use Notify;
use Event;
use Lang;
use User;
use App;

/**
 * FMN controller class for entries
 */
class Fmns extends SiteController
{
	/**
	 * Determine task to perform and execute it.
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');

		parent::execute();
	}

	/**
	 * Default task (main FMN page)
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		$this->view->model = new Fmn();

		$this->view
		     ->setLayout('intro')
		     ->display();
	}
  
  /**
	 * Display specific page
	 *
	 * @return	void
	 */
  public function pageTask()
  {
    $pageName = \Request::getCmd('page', 'intro');
    $this->view
		     ->setLayout($pageName)
		     ->display();    
  }
}
