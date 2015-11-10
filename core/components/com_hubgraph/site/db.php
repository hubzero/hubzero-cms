<?php
defined('_HZEXEC_') or die();

class Db
{
	private static $dbh, $log, $timer;

	public static function getHandle()
	{
		if (!self::$dbh) {
			$cfg = new \Hubzero\Config\Repository('site');
			self::$dbh = new PDO('mysql:host=localhost;dbname='.$cfg->get('db'), $cfg->get('user'), $cfg->get('password'), array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			));
		}
		return self::$dbh;
	}

	public static function getStatementHandle($sql, $params = array(), &$success = NULL)
	{
		$sth = self::getHandle()->prepare($sql);
		$success = $sth->execute($params);
		return $sth;
	}

	public static function query($sql, $params = array())
	{
		return self::getStatementHandle($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function scalarQuery($sql, $params = array())
	{
		$rv = self::getStatementHandle($sql, $params)->fetchAll(PDO::FETCH_NUM);
		return $rv && array_key_exists(0, $rv[0]) ? $rv[0][0] : NULL;
	}

	public static function insert($sql, $params = array())
	{
		self::getStatementHandle($sql, $params, $success);
		return $success ? self::$dbh->lastInsertId() : NULL;
	}

	public static function update($sql, $params = array())
	{
		$sth = self::getStatementHandle($sql, $params, $success);
		return $success ? $sth->rowCount() : FALSE;
	}

	public static function execute($sql, $params = array())
	{
		return self::update($sql, $params);
	}

	public static function startTransaction()
	{
		self::getHandle()->query('START TRANSACTION');
	}

	public static function commit()
	{
		self::getHandle()->query('COMMIT');
	}

	public static function rollback()
	{
		self::getHandle()->query('ROLLBACK');
	}
}
