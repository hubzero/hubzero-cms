<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (!defined("n")) {
	define("t","\t");
	define("n","\n");
	define("r","\r");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}


class MailingListHtml 
{

	
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	
	public function subscribeHtml($gid) 
	{
		$html  = '<div id="mailinglist-subscription">'.n;
		$html  = '<div id="mailinglist-subscription">'.n;
		$html .= '<form>'.n;
		$html .= t.'Email address<input type=text length=50 maxlength=50>'.n;
		$html .= t.'<input type=submit length=50 value=\'Join\'>'.n;
		$html .= '</form>'.n;
		$html .= '</div><!-- mailinglist-subscription -->'.n;
		
		return $html;
	}

	
	//-----------
	
	
	public function emailAddressConfirmNotificationHtml() 
	{
		$html  = '<div id="mailinglist-list-confirmation">'.n;
		$html .= '<form>'.n;
		$html .= t.'<input type=text length=50 maxlength=50'.n;
		$html .= '</form>'.n;
		$html .= '</div><!-- mailinglist-list-confirmation -->'.n;
		
		return $html;
	}
	
	
	
	
	//-----------
	

	
	
	
}
?>
