<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to make the IP address columns wide enough for IPv6
 *
 * Most were varchar(15), which holds a dotted IPv4 address and nothing
 * longer. Under STRICT_TRANS_TABLES writing an IPv6 address into one fails
 * ("Data too long"): an IPv6 visitor could not sign in, since the #__session
 * insert failing ends the request, and failed sign-ins from IPv6 were never
 * counted towards a ban. Without strict mode the address was cut short.
 *
 * Request::ip() gives an IPv6 address compressed, at most 39 characters;
 * varchar(45) also holds the longest text form, with an embedded IPv4 one.
 * Each column keeps its nullability, default, collation and comment.
 *
 * The usage tables in the metrics database are widened too, when it can be
 * reached. Those are MyISAM and can be large, so ALTER copies each; on a
 * busy hub, expect the migration to take a while there.
 **/
class Migration20261104160000CoreIpv6 extends Base
{
	/**
	 * Width that holds any IP address as text
	 *
	 * @var  integer
	 **/
	const WIDTH = 45;

	/**
	 * IP address columns in the CMS database, by table
	 *
	 * @var  array
	 **/
	protected $columns = array(
		'#__session'               => 'ip',
		'#__session_geo'           => 'ip',
		'#__xsession'              => 'ip',
		'#__users'                 => 'registerIP',
		'#__users_log_auth'        => 'ip',
		'#__item_votes'            => 'ip',
		'#__vote_log'              => 'ip',
		'#__answers_log'           => 'ip',
		'#__answers_questions_log' => 'ip',
		'#__faq_helpful_log'       => 'ip',
		'#__kb_votes'              => 'ip',
		'#__courses_asset_views'   => 'ip',
		'#__courses_page_hits'     => 'ip',
		'#__project_logs'          => 'ip',
		'#__xgroups_pages_hits'    => 'ip',
		'#__citations_authors'     => 'ip'
	);

	/**
	 * IP address columns in the metrics database, by table
	 *
	 * @var  array
	 **/
	protected $metricsColumns = array(
		'sessionlog_metrics' => 'ip',
		'toolstart'          => 'ip',
		'userlogin'          => 'ip',
		'web'                => 'ip',
		'websessions'        => 'ip'
	);

	/**
	 * Up
	 **/
	public function up()
	{
		foreach ($this->columns as $table => $column)
		{
			$this->widen($this->db, $table, $column);
		}

		$metrics = $this->metricsDb();

		if ($metrics)
		{
			foreach ($this->metricsColumns as $table => $column)
			{
				$this->widen($metrics, $table, $column);
			}
		}
	}

	/**
	 * Down
	 *
	 * Narrowing the columns again would cut short every IPv6 address stored
	 * since, so leave them as they are
	 **/
	public function down()
	{
	}

	/**
	 * Widen a text column to WIDTH, if it exists and is narrower
	 *
	 * @param   object  $db      Database connection
	 * @param   string  $table
	 * @param   string  $column
	 * @return  void
	 **/
	protected function widen($db, $table, $column)
	{
		if (!$db->tableExists($table) || !$db->tableHasField($table, $column))
		{
			return;
		}

		$fields = $db->getTableColumns($table, false);

		if (!isset($fields[$column]))
		{
			return;
		}

		$field = $fields[$column];

		if (!preg_match('/^(?:var)?char\((\d+)\)$/i', $field->Type, $match) || (int) $match[1] >= self::WIDTH)
		{
			return;
		}

		$query = 'ALTER TABLE ' . $db->quoteName($table)
			. ' MODIFY ' . $db->quoteName($column) . ' varchar(' . self::WIDTH . ')';

		if (!empty($field->Collation))
		{
			$query .= ' COLLATE ' . $field->Collation;
		}

		$query .= ($field->Null == 'YES') ? ' NULL' : ' NOT NULL';

		if ($field->Default !== null)
		{
			$query .= ' DEFAULT ' . $db->quote($field->Default);
		}
		elseif ($field->Null == 'YES')
		{
			$query .= ' DEFAULT NULL';
		}

		if (!empty($field->Comment))
		{
			$query .= ' COMMENT ' . $db->quote($field->Comment);
		}

		$db->setQuery($query);
		$db->query();

		$this->log(sprintf('Widened %s.%s from %s to varchar(%d)', $table, $column, $field->Type, self::WIDTH));
	}

	/**
	 * The metrics database, or null when it cannot be reached
	 *
	 * Through com_usage's own helper, as its migrations do. Where no
	 * separate metrics database is configured the helper gives the CMS
	 * database, which has none of the metrics tables; widen() skips those.
	 *
	 * @return  object|null
	 **/
	protected function metricsDb()
	{
		$helper = PATH_CORE . '/components/com_usage/helpers/helper.php';

		if (!file_exists($helper))
		{
			return null;
		}

		include_once $helper;

		try
		{
			$db = \Components\Usage\Helpers\Helper::getUDBO();
		}
		catch (\Exception $e)
		{
			$db = null;
		}

		if (!$db)
		{
			$this->log('Unable to reach the metrics database; its IP columns are left as they are.', 'warning');
			return null;
		}

		return $db;
	}
}
