<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding MP4 extension to params
 **/
class Migration20170124151456ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if($this->db->tableExists('#__extensions')){
			$query = "SELECT params FROM #__extensions WHERE name = 'com_support';";
			$this->db->setQuery($query);
			$params = $this->db->query()->loadResult();

			$params = json_decode($params);
			$params->file_ext = 'mp4,jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz';
            		$params = json_encode($params);
			$query2 = "UPDATE `#__extensions` SET params=" . $this->db->quote($params) . " WHERE name='com_support';";
			$this->db->setQuery($query2);
			$this->db->query();	
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
			$query = "SELECT params FROM #__extensions WHERE name = 'com_support';";
                        $this->db->setQuery($query);
                        $params = $this->db->query()->loadResult();

                        $params = json_decode($params);
                        $params->file_ext = 'jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz';
                        $params = json_encode($params);
                        $query2 = "UPDATE `#__extensions` SET params=" . $this->db->quote($params) . " WHERE name='com_support';";
                        $this->db->setQuery($query2);
                        $this->db->query();
	}
}
