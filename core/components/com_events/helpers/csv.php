<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Helpers;

/**
 * Events helper for working with CSV files
 */
class Csv
{
	/**
	 * Description for 'field_ordering'
	 *
	 * @var array
	 */
	private static $field_ordering = array(
		'name' => 0,
		'email' => 1,
		'telephone' => 2,
		'affiliation' => 3,
		'position' => 4,
		'address' => 5,
		'arrival' => 6,
		'departure' => 7,
		'website' => 8,
		'gender' => 9,
		'disability' => 10,
		'dietary' => 11,
		'dinner' => 12,
		'abstract' => 13,
		'comments' => 14,
		'degree' => 15,
		'race' => 16,
		'fax' => 17,
		'title' => 18,
		'registered' => 19 // folded into previous entries
	);

	/**
	 * Short description for 'fieldSorter'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $a Parameter description (if any) ...
	 * @param      unknown $b Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function fieldSorter($a, $b)
	{
		return self::$field_ordering[$a] < self::$field_ordering[$b] ? -1 : 1;
	}

	/**
	 * Short description for 'quoteCsv'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $val Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function quote($val)
	{
		if (!isset($val))
		{
			return '';
		}
		if (strpos($val, "\n") !== false || strpos($val, ',') !== false)
		{
			return '"' . str_replace(array('\\', '"'), array('\\\\', '""'), $val) . '"';
		}

		return $val;
	}

	/**
	 * Short description for 'quoteCsvRow'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $vals Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function quoteRow($vals)
	{
		return implode(',', array_map(array('\Components\Events\Helpers\Csv', 'quote'), $vals)) . "\n";
	}

	/**
	 * Short description for 'downloadlist'
	 *
	 * Long description (if any) ...
	 *
	 * @param      object $respondents   Parameter description (if any) ...
	 * @param      unknown $option       Parameter description (if any) ...
	 * @return     void
	 */
	public static function downloadlist($respondents, $option)
	{
		$database = \App::get('db');
		$ee = new \Components\Events\Tables\Event($database);
		$fields = array('name', 'registered', 'affiliation', 'email', 'telephone', 'arrival', 'departure', 'disability', 'dietary', 'dinner');

		header('Content-type: text/comma-separated-values');
		header('Content-disposition: attachment; filename="eventrsvp.csv"');

		// Output header
		usort($fields, array('\Components\Events\Helpers\Csv', 'fieldSorter'));
		echo self::quoteRow(array_map('ucfirst', $fields));

		// Output rows
		foreach ($respondents as $respondent)
		{
			if (!$respondent->get('last_name'))
			{
				$respondent->set('last_name', '[unknown]');
			}
			if (!$respondent->get('first_name'))
			{
				$respondent->set('first_name', '[unknown]');
			}
			$row = array(
				$respondent->last_name . ', ' . $respondent->first_name
			);

			foreach ($fields as $field)
			{
				switch ($field)
				{
					case 'name':
						break;
					case 'position':
						$row[] = $respondent->get('position_description');
						break;
					case 'comments':
						$row[] = $respondent->get('comment');
						break;
					case 'degree':
						$row[] = $respondent->get('highest_degree');
						break;
					case 'disability':
						$row[] = $respondent->get('disability_needs') ? 'Yes' : 'No';
						break;
					case 'dietary':
						$row[] = $respondent->get('dietary_needs');
						break;
					case 'dinner':
						$row[] = $respondent->get('attending_dinner') ? 'Yes' : 'No';
						break;
					default:
						if (isset($respondent->$field))
						{
							$row[] = $respondent->get($field);
						}
						else
						{
							$row[] = '';
						}
					break;
				}
			}
			echo self::quoteRow($row);
		}
		exit;
	}
}
