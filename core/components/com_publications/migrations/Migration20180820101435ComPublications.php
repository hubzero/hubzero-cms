<?php

use Hubzero\Content\Migration\Base;
use \Components\Publications\Models\Publication;
include Component::path('com_publications') . '/models/publication.php';
// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for creating sym links in the SFTP directory
 **/
class Migration20180820101435ComPublications extends Base
{
	/**
	 * Number of database rows to process at a time
	 *
	 * @var  integer
	 */
	public $limit = 1000;

	/**
	 * Up
	 **/
	public function up()
	{
		$offset = 0;
		$versionQuery = "SELECT count(*) FROM `#__publication_versions` WHERE `state` = 1";
		$this->db->setQuery($versionQuery);
		$this->db->query();
		$versionCount = $this->db->loadResult();
		while ($offset < $versionCount)
		{
			$query = "SELECT `v`.`doi`, `v`.`id`, `v`.`version_number`, `v`.`publication_id`, `v`.`state`, `p`.`master_type`, `t`.`alias` FROM `#__publication_versions` v";
			$query .= " JOIN `#__publications` p ON `v`.`publication_id` = `p`.`id` JOIN `#__publication_master_types` t ON `p`.`master_type` = `t`.`id`";
			$query .= " WHERE `v`.`state` = 1 AND `v`.`published_up` < NOW() AND `t`.`alias` NOT IN ('series', 'databases') LIMIT {$offset}, {$this->limit};";

			$this->db->setQuery($query);
			$this->db->query();
			$publications = $this->db->loadAssocList();

			foreach ($publications as $publication)
			{
				$pubId = $publication['publication_id'];
				$versionId = $publication['id'];
				$doi = $publication['doi'];
				$versionNum = $publication['version_number'];
				$this->removeSymLink($pubId, $versionId, $versionNum, $doi);
				if (is_dir($this->_symLinkPath()))
				{
					$this->createSymLink($pubId, $versionId, $versionNum, $doi);
				}
			}
			$offset += $this->limit;
		}

	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (is_dir($this->_symlinkPath()))
		{
			$versionQuery = "SELECT count(*) FROM `#__publication_versions` WHERE `state` = 1";
			$this->db->setQuery($versionQuery);
			$this->db->query();
			$versionCount = $this->db->loadResult();
			$offset = 0;
			while ($offset < $versionCount)
			{
				$query = "SELECT `doi`, `publication_id`, `id`, `version_number`, `state` FROM `#__publication_versions` WHERE `state` = 1 LIMIT {$offset}, {$this->limit};";

				$this->db->setQuery($query);
				$this->db->query();
				$publications = $this->db->loadAssocList();

				foreach ($publications as $publication)
				{
					$pubId = $publication['publication_id'];
					$versionId = $publication['id'];
					$doi = $publication['doi'];
					$versionNum = $publication['version_number'];
					$this->removeSymLink($pubId, $versionId, $versionNum, $doi);
				}
				$offset += $this->limit;
			}
		}

	}

	/**
	 * Generate symbolic link for publication package
	 *
	 * @return boolean
	 */
	protected function createSymLink($pubId, $versionId, $versionNum, $doi = '')
	{
		$bundleName = 'Publication' . '_' . $pubId;
		$bundleWithVersion = $bundleName . '_' . $versionNum;

		if ($doi != '')
		{
			$doi = str_replace('.', '_', $doi);
			$doi = str_replace('/', '_', $doi);
			$bundleName = $doi;
		}

		$tarname = $bundleName . '.zip';
		$symFileName = $bundleWithVersion . '.zip';
		$tarPath = '..' . '/' . str_pad($pubId, 5, "0", STR_PAD_LEFT) . '/' . str_pad($versionId, 5, "0", STR_PAD_LEFT) . '/' . $tarname;
		$symLinkPath = $this->_symLinkPath();
		if ($symLinkPath !== false)
		{
			chdir($symLinkPath);
		}
		if (!is_file($tarPath))
		{
			echo "Creating package for {$pubId}_{$versionNum}...." . PHP_EOL;
			$pubModel = new Publication($pubId, null, $versionId);
			$pubModel->setCuration();
			$pubModel->_curationModel->package();
			echo "Finished creating package for {$pubId}_{$versionNum}...." . PHP_EOL;
		}
		if (empty($pubId) || $symLinkPath == false || !is_file($tarPath))
		{
			return false;
		}
		$symLink = $symLinkPath . '/' . $symFileName;
		if (!is_file($symLink))
		{
			if (!link($tarPath, $symLink))
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * Remove symbolic link for publication package
	 *
	 * @return boolean
	 */
	protected function removeSymLink($pubId, $versionId, $versionNum, $doi='')
	{
		$bundleName = 'Publication' . '_' . $pubId;
		$bundleWithVersion = $bundleName . '_' . $versionNum;
		if ($doi != '')
		{
			$doi = str_replace('.', '_', $doi);
			$doi = str_replace('/', '_', $doi);
			$bundleName = $doi;
		}

		$tarname = $bundleWithVersion . '.zip';
		$symLinkPath = $this->_symLinkPath();
		$symLink = $symLinkPath . '/' . $tarname;
		if ($symLink == false)
		{
			return false;
		}
		if (file_exists($symLink))
		{
			unlink($symLink);
		}
		return true;
	}

	/**
	 * Get path to symbolic link used for downloading package via SFTP
	 *
	 * @return 	mixed 	string if sftp path provided, false if not
	 */
	private function _symLinkPath()
	{
		$sftpPath = PATH_APP . Component::params('com_publications')->get('sftppath');
		if (!is_dir($sftpPath))
		{
			return false;
		}
		return $sftpPath;
	}
}
