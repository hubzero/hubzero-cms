<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;

/**
 * Table class for review
 */
class AnonymousReview extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
    public function __construct(&$db)
    {
        parent::__construct('#__publication_review', 'id', $db);
    }

    /**
     * Save the anonymous-review record
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
		
		$query = "INSERT INTO `$this->_tbl` (`publication_id`, `version_number`, `publication_version_id`, `created`, `created_by`, `expired`, `token_hash`)
				 VALUES (" . $this->_db->quote($publicationId) . ',' . $this->_db->quote($versionNumber) . ',' . $this->_db->quote($publicationVersionId) . ',' . 
				 $this->_db->quote($created) . ',' . $this->_db->quote($createdBy) . ',' . $this->_db->quote($expired) . ',' . $this->_db->quote($tokenHash) . ")";

		$this->_db->setQuery($query);

        return $this->_db->query();
    }
	
	/**
     * Get anonymous-review record by token hash
     *
     * @param   string  $tokenHash
     * @return  mixed
     */
    public function getAnonReviewRecordByTokenHash($tokenHash)
    {
        $query = "SELECT * FROM `$this->_tbl` WHERE token_hash = " . $this->_db->quote($tokenHash) . " LIMIT 1";

        $this->_db->setQuery($query);

        return $this->_db->loadObject();
    }

    /**
     * Get anonymous-review record by publication id and version number
     *
     * @param   integer  $publicationId
     * @param   integer  $versionNumber
     * @return  mixed
     */
    public function getAnonReviewRecordByPubIdVerNum($publicationId, $versionNumber)
    {
        $query = "SELECT * FROM `$this->_tbl` WHERE publication_id = " . $this->_db->quote($publicationId) . " AND version_number = " . $this->_db->quote($versionNumber) . " LIMIT 1";

        $this->_db->setQuery($query);

        return $this->_db->loadObject();
    }
	
	/**
	 * Replace the token on the existing record if it has not changed.
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
	public function regenerateAnonReviewRecord($publicationId, $versionNumber, $publicationVersionId, $expectedTokenHash, $created, $expired, $tokenHash)
	{
		$query = "UPDATE `$this->_tbl`"
			. " SET `created` = " . $this->_db->quote($created)
			. ", `expired` = " . $this->_db->quote($expired)
			. ", `token_hash` = " . $this->_db->quote($tokenHash)
			. " WHERE `publication_id` = " . $this->_db->quote($publicationId)
			. " AND `version_number` = " . $this->_db->quote($versionNumber)
			. " AND `publication_version_id` = " . $this->_db->quote($publicationVersionId)
			. " AND `token_hash` = " . $this->_db->quote($expectedTokenHash);

		$this->_db->setQuery($query);
		$this->_db->query();

		return $this->_db->getAffectedRows() === 1;
	}
}