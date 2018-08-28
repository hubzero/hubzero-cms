<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for creating sym links in the SFTP directory
 **/
class Migration20180627111315ComPublications extends Base
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
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT `params` FROM `#__extensions` WHERE `element` = 'com_publications';";
			$this->db->setQuery($query);
			$params = $this->db->loadResult();
			if ($params)
			{
				$pubParams = new \Hubzero\Config\Registry($params);
				$addParams = array(
					'sftppath' => '/site/publications/ftp'
				);
				$pubParams->merge($addParams);

				$query = "UPDATE `#__extensions` SET `params`=" . $this->db->quote($pubParams->toString()) . " WHERE `element`='com_publications'";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}


		if (is_dir(PATH_APP . '/site/publications/ftp'))
		{
			$offset = 0;
			$versionQuery = "SELECT count(*) FROM `#__publication_versions` WHERE `state` = 1";
			$this->db->setQuery($versionQuery);
			$this->db->query();
			$versionCount = $this->db->loadResult();
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
					$this->createSymLink($pubId, $versionId, $versionNum, $doi);
				}
				$offset += $this->limit;
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$offset = 0;
		$versionQuery = "SELECT count(*) FROM `#__publication_versions` WHERE `state` = 1";
		$this->db->setQuery($versionQuery);
		$this->db->query();
		$versionCount = $this->db->loadResult();
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

	/**
	 * Generate symbolic link for publication package
	 *
	 * @return boolean
	 */
	protected function createSymLink($pubId, $versionId, $versionNum, $doi = '')
	{
		if ($doi != '')
		{
			$doi = str_replace('.', '_', $doi);
			$doi = str_replace('/', '_', $doi);
			$bundleName = $doi;
		}
		else
		{
			$bundleName = 'Publication' . '_' . $pubId;
			$bundleWithVersion = $bundleName . '_' . $versionNum;
		}

		$tarname = $bundleName . '.zip';
		$symFileName = $bundleWithVersion . '.zip';
		$tarPath = '..' . '/' . str_pad($pubId, 5, "0", STR_PAD_LEFT) . '/' . str_pad($versionId, 5, "0", STR_PAD_LEFT) . '/' . $tarname;
		$symLinkPath = $this->_symLinkPath();
		if ($symLinkPath !== false)
		{
			chdir($symLinkPath);
		}
		if (empty($pubId) || $symLinkPath == false || !is_file($tarPath))
		{
			return false;
		}
		$symLink = $symLinkPath . '/' . $symFileName;
		if (!is_file($symLink))
		{
			if (!symlink($tarPath, $symLink))
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
		if ($doi != '')
		{
			$doi = str_replace('.', '_', $doi);
			$doi = str_replace('/', '_', $doi);
			$bundleName = $doi;
		}
		else
		{
			$bundleName = 'Publication' . '_' . $pubId;
			$bundleName .= '_' . $versionNum;
		}

		$tarname = $bundleName . '.zip';
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
