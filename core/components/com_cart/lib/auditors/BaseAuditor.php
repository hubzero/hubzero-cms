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
 * @author    Ilya Shunko <ishunko@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Cart\Lib\Auditors;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'Cart.php';

class BaseAuditor
{
	public function __construct($type, $pId = null, $crtId = null)
	{
		$this->type = $type;
		$this->pId = $pId;
		$this->crtId = $crtId;

		// Get user, if any
		$this->uId = \Components\Cart\Models\Cart::getCartUser($crtId);

		// Set up the default empty ok response
		$this->response = new \stdClass();
		$this->response->status = 'ok';
		$this->response->messages = array();
		$this->response->notices = array();
		$this->response->errors = array();
	}

	public function setSku($sId)
	{
		$this->sId = $sId;
	}

	public function getSku()
	{
		if (!empty($this->sId) && $this->sId)
		{
			return $this->sId;
		}
		return false;
	}

	public function audit()
	{
		return ($this->getResponse());
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
