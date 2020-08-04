<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
