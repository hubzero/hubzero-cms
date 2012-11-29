<?php
JLoader::import('Hubzero.Api.Controller');

class NewsletterApiController extends Hubzero_Api_Controller
{
	function execute()
	{
		//needed joomla libraries
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');
		
		//newsletter classes
		require_once( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_newsletter' . DS . 'tables' . DS . 'campaign.php' );
		require_once( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_newsletter' . DS . 'tables' . DS . 'template.php' );
		require_once( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_newsletter' . DS . 'tables' . DS . 'primary.php' );
		require_once( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_newsletter' . DS . 'tables' . DS . 'secondary.php' );
		
		switch($this->segments[0]) 
		{
			case 'current':		$this->current();		break;
			case 'archive':		$this->archive();		break;
			default:			$this->index();			break;
		}
	}
	
	
	private function not_found()
	{
		$response = $this->getResponse();
		$response->setErrorMessage(404,'Not Found');
	}
	
	
	private function index()
	{
		//get the userid
		$userid = JFactory::getApplication()->getAuthn('user_id');
		
		//if we dont have a user return nothing
		if ($userid == null)
		{
			return $this->not_found();
		}
		
		//get the request vars
		$limit = JRequest::getVar("limit", 5);
		
		$database =& JFactory::getDBO();
		$nc = new NewsletterCampaign( $database );
		
		//get campaigns
		$campaigns = $nc->getCampaign();
		
		$obj = new stdClass();
		$obj->newsletters = $campaigns;
		$this->setMessageType("application/json");
		$this->setMessage($obj);
	}
	
	//-----
	
	private function current()
	{
		//return var
		$result = array();
		
		//get request vars
		$format = JRequest::getVar("format", "json");
		
		//instantiate newsletter campaign object
		$database =& JFactory::getDBO();
		$nc = new NewsletterCampaign( $database );
		
		//get the current campaign
		$campaign = (array) $nc->getCurrentCampaign();
		
		//build the newsletter based on campaign
		$newsletter = $nc->buildNewsletter($campaign);
		
		//
		$result['id'] = $campaign['issue'];
		$result['title'] = $campaign['name'];
		$result['content'] = $newsletter;
		
		//encode sessions for return
		$obj = new stdClass();
		$obj->newsletter = $result;
		
		//set format and content
		$this->response->setResponseProvides( $format );
		$this->response->setMessage( $obj );
	}
	
	//-----
	
	private function archive()
	{
		//return var
		$result = array();
		
		//get request vars
		$format = JRequest::getVar("format", "json");
		
		//instantiate newsletter campaign object
		$database =& JFactory::getDBO();
		$nc = new NewsletterCampaign( $database );
		
		$campaigns = $nc->getCampaign();
		
		
		foreach($campaigns as $k => $campaign)
		{
			$result[$k]['id'] = $campaign->issue;
			$result[$k]['title'] = $campaign->name;
			$result[$k]['content'] = $nc->buildNewsletter( (array) $campaign );
		}
		
		//encode sessions for return
		$obj = new stdClass();
		$obj->newsletters = $result;
		
		//set format and content
		$this->response->setResponseProvides( $format );
		$this->response->setMessage( $obj );
	}
}
?>