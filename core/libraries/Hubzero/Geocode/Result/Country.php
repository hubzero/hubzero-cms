<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Geocode\Result;

use Geocoder\Result\AbstractResult;
use Geocoder\Result\ResultInterface;

/**
 * @author  William Durand <william.durand1@gmail.com>
 */
class Country extends AbstractResult implements ResultInterface
{
	/**
	 * Country name
	 *
	 * @var  string
	 */
	protected $name = null;

	/**
	 * Country code
	 *
	 * @var  string
	 */
	protected $code = null;

	/**
	 * Country continent
	 *
	 * @var  string
	 */
	protected $continent = null;

	/**
	 * Get latitude/longitude coordinates
	 *
	 * @return  array
	 */
	public function getCoordinates()
	{
		return array(
			'latitude'  => 0.0,
			'longitude' => 0.0
		);
	}

	/**
	 * Get latitude
	 *
	 * @return  float
	 */
	public function getLatitude()
	{
		return 0.0;
	}

	/**
	 * Get longitude
	 *
	 * @return  float
	 */
	public function getLongitude()
	{
		return 0.0;
	}

	/**
	 * Get the coordinates for the "bounding box"
	 * that encompasses the coutnry.
	 *
	 * @return  float
	 */
	public function getBounds()
	{
		return array(
			'south' => 0.0,
			'west'  => 0.0,
			'north' => 0.0,
			'east'  => 0.0
		);
	}

	/**
	 * Get street number (N/A)
	 *
	 * @return  string
	 */
	public function getStreetNumber()
	{
		return '';
	}

	/**
	 * Get street name (N/A)
	 *
	 * @return  string
	 */
	public function getStreetName()
	{
		return '';
	}

	/**
	 * Get city (N/A)
	 *
	 * @return  string
	 */
	public function getCity()
	{
		return '';
	}

	/**
	 * Get zip code (N/A)
	 *
	 * @return  string
	 */
	public function getZipcode()
	{
		return '';
	}

	/**
	 * Get city district (N/A)
	 *
	 * @return  string
	 */
	public function getCityDistrict()
	{
		return '';
	}

	/**
	 * Get county (N/A)
	 *
	 * @return  string
	 */
	public function getCounty()
	{
		return '';
	}

	/**
	 * Get county code (N/A)
	 *
	 * @return  string
	 */
	public function getCountyCode()
	{
		return '';
	}

	/**
	 * Get region
	 *
	 * @return  string
	 */
	public function getRegion()
	{
		return $this->continent;
	}

	/**
	 * Get region code
	 *
	 * @return  string
	 */
	public function getRegionCode()
	{
		return '';
	}

	/**
	 * Get country name
	 *
	 * @return  string
	 */
	public function getCountry()
	{
		return $this->name;
	}

	/**
	 * Get country code
	 *
	 * @return  string
	 */
	public function getCountryCode()
	{
		return $this->code;
	}

	/**
	 * Get timezone (N/A)
	 *
	 * @return  string
	 */
	public function getTimezone()
	{
		return '';
	}

	/**
	 * {@inheritDoc}
	 */
	public function fromArray(array $data = array())
	{
		if (isset($data['continent']))
		{
			$this->continent = $this->formatString($data['continent']);
		}

		if (isset($data['name']))
		{
			$this->name = $this->formatString($data['name']);
		}

		if (isset($data['code']))
		{
			$this->code = $this->upperize($data['code']);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray()
	{
		return array(
			'name'      => $this->name,
			'code'      => $this->code,
			'continent' => $this->continent
		);
	}
}
