<?php
/**
 * Schedule class
 */
class Schedule extends CCObject
{
	public $link;
	public $id;
	public $updated;
	public $time;
	public $campaign;

	public function __construct($params = array())
	{
		$this->link = (isset($params['link'])) ? $params['link'] : '';
		$this->id = (isset($params['id'])) ? $params['id'] : '';
		$this->updated = (isset($params['updated'])) ? $params['updated'] : '';
		$this->time = (isset($params['time'])) ? $params['time'] : '';
		$this->campaign = (isset($params['campaign'])) ? $params['campaign'] : '';
	}

	public function createXml()
	{
		$this->validate(array('time'));
		$xml = simplexml_load_string("<?xml version='1.0' encoding='UTF-8' standalone='yes'?><entry xmlns='http://www.w3.org/2005/Atom'/>");
		$linkNode = $xml->addChild("link");
		$linkNode->addAttribute('href', $this->campaign->link.'/schedules/1');
		$linkNode->addAttribute('rel', 'edit');
		$xml->addChild("id", $this->campaign->id);
		$xml->addChild("title", $this->time);
		$xml->addChild("updated", $this->time);
		$author = $xml->addChild("author");
		$author->addChild("name", "ConstantContact");
		$content = $xml->addChild("content");
		$content->addAttribute("type", "application/vnd.ctct+xml");
		$schedule = $content->addChild("Schedule", "", 'http://ws.constantcontact.com/ns/1.0/');
		$schedule->addAttribute("id", $this->campaign->link.'/schedules/1');
		$schedule->addChild("ScheduledTime", $this->time);
		return $xml->asXML();
	}

	public static function createStruct($parsedResponse)
	{
		$schedule['link'] = (string) $parsedResponse->link->Attributes()->href;
		$schedule['id'] = (string) $parsedResponse->id;
		$schedule['updated'] = (string) $parsedResponse->updated;
		$schedule['time'] = (string) $parsedResponse->content->Schedule->ScheduledTime;
		return $schedule;
	}
}
