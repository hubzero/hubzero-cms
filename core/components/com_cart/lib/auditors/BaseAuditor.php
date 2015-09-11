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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

require_once(JPATH_BASE . DS . 'components' . DS . 'com_cart' . DS . 'models' . DS . 'Cart.php');

class BaseAuditor {

	public function __construct($type, $pId = NULL, $crtId = NULL)
	{
		$this->type = $type;
		$this->pId = $pId;
		$this->crtId = $crtId;

		// Get user, if any
		$this->uId = CartModelCart::getCartUser($crtId);

		// Set up the default empty ok
		$this->response = new stdClass();
		$this->response->status = 'ok';
		$this->response->messages = array();
		$this->response->notices = array();
		$this->response->errors = array();
	}

	public function audit()
	{
		return($this->getResponse());
	}

	protected function setResponseStatus($status)
	{
		$this->response->status = $status;
	}

	protected function setResponseMessage($msg)
	{
		$this->response->messages[] = $msg;
	}

	protected function setResponseNotice($msg)
	{
		$this->response->notices[] = $msg;
	}

	// ok, notice, error
	protected function setResponseError($msg)
	{
		$this->response->errors[] = $msg;
	}

	public function getResponseError()
	{
		if (sizeof($this->response->errors) > 0)
		{
			return $this->response->errors[0];
		}
		return false;
	}

	protected function getResponse()
	{
		return $this->response;
	}
}