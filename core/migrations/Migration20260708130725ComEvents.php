<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Convert #__events.time_zone from a numeric UTC offset to an IANA time zone.
 *
 * Event start/end (publish_up/publish_down) are already stored in UTC, computed
 * at save time via Hubzero\Utility\Date's numeric-offset map (e.g. '-8' =>
 * 'US/Pacific'). That map already resolves to DST-aware IANA zones, so relabeling
 * time_zone to the *same* zone the save used is lossless: converting the stored
 * UTC back through the IANA zone reproduces the entered wall-clock, now with
 * correct DST. Only time_zone is touched here; the instants are not changed.
 *
 * The two legacy events saved with "ignore DST" used a fixed offset, so they are
 * mapped to a fixed Etc/GMT zone to preserve their (non-DST) display.
 **/
class Migration20260708130725ComEvents extends Base
{
	/**
	 * Numeric offset => IANA zone, matching Hubzero\Utility\Date::$offsets so the
	 * relabel round-trips exactly.
	 *
	 * @var  array
	 */
	protected $map = array(
		'-12' => 'Etc/GMT-12', '-11' => 'Pacific/Midway', '-10' => 'Pacific/Honolulu',
		'-9.5' => 'Pacific/Marquesas', '-9' => 'US/Alaska', '-8' => 'US/Pacific',
		'-7' => 'US/Mountain', '-6' => 'US/Central', '-5' => 'US/Eastern',
		'-4.5' => 'America/Caracas', '-4' => 'America/Barbados', '-3.5' => 'Canada/Newfoundland',
		'-3' => 'America/Buenos_Aires', '-2' => 'Atlantic/South_Georgia', '-1' => 'Atlantic/Azores',
		'0' => 'Europe/London', '1' => 'Europe/Amsterdam', '2' => 'Europe/Istanbul',
		'3' => 'Asia/Riyadh', '3.5' => 'Asia/Tehran', '4' => 'Asia/Muscat', '4.5' => 'Asia/Kabul',
		'5' => 'Asia/Karachi', '5.5' => 'Asia/Calcutta', '5.75' => 'Asia/Katmandu',
		'6' => 'Asia/Dhaka', '6.5' => 'Indian/Cocos', '7' => 'Asia/Bangkok', '8' => 'Australia/Perth',
		'8.75' => 'Australia/West', '9' => 'Asia/Tokyo', '9.5' => 'Australia/Adelaide',
		'10' => 'Australia/Brisbane', '10.5' => 'Australia/Lord_Howe', '11' => 'Pacific/Kosrae',
		'11.5' => 'Pacific/Norfolk', '12' => 'Pacific/Auckland', '12.75' => 'Pacific/Chatham',
		'13' => 'Pacific/Tongatapu', '14' => 'Pacific/Kiritimati'
	);

	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__events') || !$this->db->tableHasField('#__events', 'time_zone'))
		{
			return;
		}

		// Widen the column to hold IANA names.
		$this->db->setQuery("ALTER TABLE `#__events` MODIFY `time_zone` VARCHAR(64) NULL DEFAULT NULL");
		$this->db->query();

		// Events saved with "ignore DST" used a fixed offset; keep them fixed
		// (Etc/GMT sign is inverted, so a -5 offset becomes Etc/GMT+5).
		foreach ($this->map as $offset => $zone)
		{
			$num = (float) $offset;
			if ($num == (int) $num)
			{
				$etc = 'Etc/GMT' . ($num <= 0 ? '+' . abs((int) $num) : '-' . abs((int) $num));
				$this->db->setQuery(
					"UPDATE `#__events` SET `time_zone` = " . $this->db->quote($etc) .
					" WHERE `time_zone` = " . $this->db->quote($offset) .
					" AND `params` LIKE '%\"ignore_dst\":\"1\"%'"
				);
				$this->db->query();
			}
		}

		// Relabel the remaining numeric offsets to their IANA zone.
		foreach ($this->map as $offset => $zone)
		{
			$this->db->setQuery(
				"UPDATE `#__events` SET `time_zone` = " . $this->db->quote($zone) .
				" WHERE `time_zone` = " . $this->db->quote($offset)
			);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__events') || !$this->db->tableHasField('#__events', 'time_zone'))
		{
			return;
		}

		// Map IANA zones (and the fixed Etc/GMT variants) back to numeric offsets.
		foreach ($this->map as $offset => $zone)
		{
			$this->db->setQuery(
				"UPDATE `#__events` SET `time_zone` = " . $this->db->quote($offset) .
				" WHERE `time_zone` = " . $this->db->quote($zone)
			);
			$this->db->query();

			$num = (float) $offset;
			if ($num == (int) $num)
			{
				$etc = 'Etc/GMT' . ($num <= 0 ? '+' . abs((int) $num) : '-' . abs((int) $num));
				$this->db->setQuery(
					"UPDATE `#__events` SET `time_zone` = " . $this->db->quote($offset) .
					" WHERE `time_zone` = " . $this->db->quote($etc)
				);
				$this->db->query();
			}
		}

		$this->db->setQuery("ALTER TABLE `#__events` MODIFY `time_zone` VARCHAR(5) NULL DEFAULT NULL");
		$this->db->query();
	}
}
