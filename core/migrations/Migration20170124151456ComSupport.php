<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ...
 **/
class Migration20170124151456ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if($this->db->tableExists('#__extensions')){
			$params = '{"feed_summary":"0","severities":"critical,major,normal,minor,trivial","webpath":"\/site\/tickets","maxAllowed":"40000000","file_ext":"mp4,jpg,jpeg,jpe,bmp,tif,tiff,png,gif,pdf,zip,mpg,mpeg,avi,mov,wmv,asf,asx,ra,rm,txt,rtf,doc,xsl,html,js,wav,mp3,eps,ppt,pps,swf,tar,tex,gz","group":"","emails":"{config.mailfrom}","0":"","blacklist":"","badwords":"viagra, pharmacy, xanax, phentermine, dating, ringtones, tramadol, hydrocodone, levitra, ambien, vicodin, fioricet, diazepam, cash advance, free online, online gambling, online prescriptions, debt consolidation, baccarat, loan, slots, credit, mortgage, casino, slot, texas holdem, teen nude, orgasm, gay, fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, porno, videosex, sperm, hentai, internet gambling, kasino, kasinos, poker, lottery, texas hold em, texas holdem, fisting","email_processing":"1"}';
			$query = "UPDATE `#__extensions` SET params=" . $this->db->quote($params) . " WHERE name='com_support';";
			$this->db->setQuery($query);
			$this->db->query();	
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{

	}
}
