<?php
JLoader::import('Hubzero.Api.Controller');

class NewsletterControllerApi extends \Hubzero\Component\ApiController
{
	function execute()
	{
		//needed joomla libraries
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		//newsletter classes
		require_once( JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_newsletter' . DS . 'tables' . DS . 'newsletter.php' );
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

		//get newsletter object
		$database = JFactory::getDBO();
		$newsletterNewsletter = new NewsletterNewsletter( $database );

		//get newsletters
		$newsletters = $newsletterNewsletter->getNewsletters(null, true);

		//output
		$obj = new stdClass();
		$obj->newsletters = $newsletters;
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
		$database = JFactory::getDBO();
		$newsletterNewsletter = new NewsletterNewsletter( $database );

		//get the current newsletter
		$newsletter = $newsletterNewsletter->getCurrentNewsletter();

		//build the newsletter based on campaign
		$newsletterHTML = $newsletterNewsletter->buildNewsletter( $newsletter );
		$result['id'] = $newsletter->issue;
		$result['title'] = $newsletter->name;
		$result['content'] = $newsletterHTML;

		//encode sessions for return
		$obj = new stdClass();
		$obj->newsletter = $result;

		//set format and content
		$this->setMessageType( $format );
		$this->setMessage( $obj );
	}

	//-----

	private function archive()
	{
		//return var
		$result = array();

		//get request vars
		$format = JRequest::getVar("format", "json");

		//instantiate newsletter campaign object
		$database = JFactory::getDBO();
		$newsletterNewsletter = new NewsletterNewsletter( $database );

		//get newsletters
		$newsletters = $newsletterNewsletter->getNewsletters();

		//add newsletter details to return array
		foreach($newsletters as $k => $newsletter)
		{
			$result[$k]['id'] = $newsletter->issue;
			$result[$k]['title'] = $newsletter->name;
			$result[$k]['content'] = $newsletterNewsletter->buildNewsletter( $newsletter );
		}

		//encode sessions for return
		$obj = new stdClass();
		$obj->newsletters = $result;

		//set format and content
		$this->setMessageType( $format );
		$this->setMessage( $obj );
	}
}
?>