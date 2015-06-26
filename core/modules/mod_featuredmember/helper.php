<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\Featuredmember;

use Hubzero\Module\Module;
use Hubzero\User\Profile;
use Hubzero\Config\Registry;
use MembersProfile;
use Component;
use User;

/**
 * Module class for displaying featured members
 */
class Helper extends Module
{
	/**
	 * Generate module contents
	 *
	 * @return  void
	 */
	public function run()
	{
		include_once(Component::path('com_members') . DS . 'tables' . DS . 'profile.php');
		include_once(Component::path('com_members') . DS . 'tables' . DS . 'association.php');

		$database = \App::get('db');
		$this->row = null;
		$this->profile = null;

		// Randomly choose one
		$filters = array(
			'limit'      => 1,
			'show'       => trim($this->params->get('show')),
			'start'      => 0,
			'sortby'     => "RAND()",
			'search'     => '',
			'authorized' => false,
			'show'       => trim($this->params->get('show'))
		);
		if ($min = $this->params->get('min_contributions'))
		{
			$filters['contributions'] = $min;
		}

		$mp = new \Components\Members\Tables\Profile($database);

		$rows = $mp->getRecords($filters, false);
		if (count($rows) > 0)
		{
			$this->row = $rows[0];
		}

		// Load their bio
		$this->profile = Profile::getInstance($this->row->uidNumber);

		if (trim(strip_tags($this->profile->get('bio'))) == '')
		{
			return '';
		}

		// Did we have a result to display?
		if ($this->row)
		{
			$this->cls = trim($this->params->get('moduleclass_sfx'));
			$this->txt_length = trim($this->params->get('txt_length'));

			$config = Component::params('com_members');

			$rparams = new Registry($this->profile->get('params'));
			$this->params = $config;
			$this->params->merge($rparams);

			if ($this->params->get('access_bio') == 0
			 || ($this->params->get('access_bio') == 1 && !User::isGuest())
			)
			{
				$this->txt = $this->profile->getBio('parsed');
			}
			else
			{
				$this->txt = '';
			}

			// Member profile
			$this->title = $this->row->name;
			if (!trim($this->title))
			{
				$this->title = $this->row->givenName . ' ' . $this->row->surname;
			}
			$this->id = $this->row->uidNumber;

			$this->thumb   = $this->profile->getPicture();
			$this->filters = $filters;

			require $this->getLayoutPath();
		}
	}

	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		if ($content = $this->getCacheContent())
		{
			echo $content;
			return;
		}

		$this->run();
	}
}

