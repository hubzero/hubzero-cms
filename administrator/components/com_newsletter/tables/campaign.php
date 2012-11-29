<?php

class NewsletterCampaign extends JTable
{   
	var $id 		= NULL;
	var $issue		= NULL;
	var $name		= NULL;
	var $date 		= NULL;
	var $template 	= NULL;
	
	//----
	
	public function __construct( &$db )
	{
		parent::__construct( '#__newsletter_campaign', 'id', $db );
	}
	
	//-----
	
	public function getCampaign( $id = null )
	{
		$sql = "SELECT * FROM {$this->_tbl}";
		
		if($id)
		{
			$sql .= " WHERE id=".$id;
			$this->_db->setQuery($sql);
			return $this->_db->loadObject();
		}
		else
		{
			$this->_db->setQuery($sql);
			return $this->_db->loadObjectList();
		}
	}
	
	//-----
	
	public function getCurrentCampaign()
	{
		$sql = "SELECT * FROM {$this->_tbl} ORDER BY `date` LIMIT 1";
		$this->_db->setQuery($sql);
		return $this->_db->loadObject();
	}
	
	//-----
	
	public function buildNewsletter( $campaign )
	{
		//instantiate all objects
		$database =& JFactory::getDBO();
		$nt = new NewsletterTemplate( $database );
	   // $nc = new NewsletterCampaign( $database );
		$nps = new NewsletterPrimaryStory( $database );
		$nss = new NewsletterSecondaryStory( $database );
		
		//get the campaign template
		$nt->load($campaign['template']);
		$template = $nt->template;
		
		//get and format primary stories
		$primary_stories = "";
		$ps = $nps->getStories($campaign['id']); 
		foreach($ps as $p)
		{
			$primary_stories .= "<span style=\"font-size:20px;font-weight:bold;color:#8668AE;font-family:arial;line-height:100%;\">" . $p->title . "</span>";
			$primary_stories .= "<span style=\"font-size:12px;font-weight:normal;color:#444444;font-family:arial;\">" . $p->story . "</span>";
			$primary_stories .= "<br /><br /><br />";
		}
		
		//get secondary stories
		$secondary_stories = "<br /><br />";
		$ss = $nss->getStories($campaign['id']);
		foreach($ss as $s)
		{
			$secondary_stories .= "<span style=\"font-size:15px;font-weight:bold;color:#EF792F;font-family:arial;line-height:150%;\">" . $s->title . "</span><br />";
		    $secondary_stories .= $s->story;
			$secondary_stories .= "<br /><br />";
		}
		
		//
		$link = "http://nanohub.org/newsletters";
		
		//replace placeholders in template
		$template = str_replace("{{LINK}}", $link, $template);
		$template = str_replace("{{TITLE}}", $campaign['name'], $template);
		$template = str_replace("{{ISSUE}}", $campaign['issue'], $template);
		$template = str_replace("{{PRIMARY_STORIES}}", $primary_stories, $template);
		$template = str_replace("{{SECONDARY_STORIES}}", $secondary_stories, $template);
		$template = str_replace("{{COPYRIGHT}}", date("Y"), $template);
		
		//so email clients dont auto convert all nanoHUB.org links
		$template = str_replace("nanoHUB.org", "nanoHUB&#8203;.org", $template);
		$template = str_replace("nanohub.org", "nanohub&#8203;.org", $template);
		
		return $template;
	}
}