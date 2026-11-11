<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;
use Components\Publications\Tables;

require_once dirname(__DIR__) . DS . 'tables' . DS . 'anonymousreview.php';

class Review extends Obj
{
	/**
	 * Database
	 *
	 * @var  object
	 */
	private $_db = null;
	
	/**
	 * Authorized
	 *
	 * @var  mixed
	 */
	private $_authorized = false;
	
	/**
	 * Constructor
	 *
	 * @param   null
	 * @return  void
	 */
    public function __construct()
    {
        $this->_db = \App::get('db');

        $this->review = new Tables\AnonymousReview($this->_db);
    }
	
	/**
	 * Save a review record.
	 *
	 * @param   integer  $publicationId
	 * @param   integer  $versionNumber
	 * @param   integer  $publicationVersionId
	 * @param   string   $created
	 * @param   integer  $createdBy
	 * @param   string   $expired
	 * @param   string   $tokenHash
	 * @return  boolean
	 */
	public function saveAnonReviewRecord($publicationId, $versionNumber, $publicationVersionId, $created, $createdBy, $expired, $tokenHash)
	{
		if (!$publicationId || !$versionNumber || !$publicationVersionId)
		{
			return false;
		}
		
		return $this->review->saveAnonReviewRecord(
			$publicationId,
			$versionNumber,
			$publicationVersionId,
			$created,
			$createdBy,
			$expired,
			$tokenHash
		);
	}
	
	/**
	 * Get the review record by token hash.
	 *
	 * @param   string  $tokenHash
	 * @return  mixed
	 */
	public function getAnonReviewRecordByTokenHash($tokenHash)
	{
		if (!$tokenHash)
		{
			return false;
		}
		
		return $this->review->getAnonReviewRecordByTokenHash($tokenHash);
	}
	
	/**
	 * Get the anonymous review record by publication id and version number.
	 *
	 * @param   integer  $publicationId
	 * @param   integer  $versionNumber
	 * @return  mixed
	 */
	public function getAnonReviewRecordByPubIdVerNum($publicationId, $versionNumber)
	{
		if (!$publicationId || !$versionNumber)
		{
			return false;
		}
		
		return $this->review->getAnonReviewRecordByPubIdVerNum($publicationId, $versionNumber);
	}
	
	/**
	 * Check that a review record has a valid future expiry.
	 *
	 * @param   object  $record
	 * @return  boolean
	 */
	public function isAnonReviewRecordUnexpired($record)
	{
		if (!is_object($record) || !isset($record->expired))
		{
			return false;
		}

		$expired = trim((string) $record->expired);

		if ($expired === '' || $expired === '0000-00-00 00:00:00')
		{
			return false;
		}

		$expiredAt = \DateTime::createFromFormat('!Y-m-d H:i:s', $expired);
		$errors = \DateTime::getLastErrors();

		if ($expiredAt === false || ($errors !== false && ($errors['warning_count'] || $errors['error_count']))	|| $expiredAt->format('Y-m-d H:i:s') !== $expired)
		{
			return false;
		}

		$now = new \DateTime('now', $expiredAt->getTimezone());

		return $now < $expiredAt;
	}
	
	/**
	 * Regenerate an existing anonymous review record.
	 *
	 * @param   integer  $publicationId
	 * @param   integer  $versionNumber
	 * @param   integer  $publicationVersionId
	 * @param   string   $expectedTokenHash
	 * @param   string   $created
	 * @param   string   $expired
	 * @param   string   $tokenHash
	 * @return  boolean
	 */
	public function regenerateAnonReviewRecord(
		$publicationId,
		$versionNumber,
		$publicationVersionId,
		$expectedTokenHash,
		$created,
		$expired,
		$tokenHash
	)
	{
		if ($publicationId <= 0 || $versionNumber <= 0 || $publicationVersionId <= 0
			|| $expectedTokenHash === '' || $tokenHash === '')
		{
			return false;
		}

		return $this->review->regenerateAnonReviewRecord(
			$publicationId,
			$versionNumber,
			$publicationVersionId,
			$expectedTokenHash,
			$created,
			$expired,
			$tokenHash
		);
	}
}