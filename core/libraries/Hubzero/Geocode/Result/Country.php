<?php

/**
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

namespace Hubzero\Geocode\Result;

use Geocoder\Result\AbstractResult;
use Geocoder\Result\ResultInterface;

/**
 * @author William Durand <william.durand1@gmail.com>
 */
class Country extends AbstractResult implements ResultInterface
{
	/**
	 * @var string
	 */
	protected $name = null;

	/**
	 * @var string
	 */
	protected $code = null;

	/**
	 * @var string
	 */
	protected $continent = null;

	public function getCoordinates()
	{
		return array(
			'latitude'  => 0.0,
			'longitude' => 0.0
		);
	}

	public function getLatitude()
	{
		return 0.0;
	}

	public function getLongitude()
	{
		return 0.0;
	}

	public function getBounds()
	{
		return array(
			'south' => 0.0,
			'west'  => 0.0,
			'north' => 0.0,
			'east'  => 0.0
		);
	}

	public function getStreetNumber()
	{
		return '';
	}

	public function getStreetName()
	{
		return '';
	}

	public function getCity()
	{
		return '';
	}

	public function getZipcode()
	{
		return '';
	}

	public function getCityDistrict()
	{
		return '';
	}

	public function getCounty()
	{
		return '';
	}

	public function getCountyCode()
	{
		return '';
	}

	public function getRegion()
	{
		return $this->continent;
	}

	public function getRegionCode()
	{
		return '';
	}

	public function getCountry()
	{
		return $this->name;
	}

	public function getCountryCode()
	{
		return $this->code;
	}

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
