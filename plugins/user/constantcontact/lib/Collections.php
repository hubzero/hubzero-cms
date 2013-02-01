<?php

abstract class Collection{
    public $CTCTRequest;
    public $uri;

    /**
     * Collection constructor
     * @param CTCTRequest $CTCTRequest
     * @param string $collectionUri - URI for a collection
     */
    public function __construct(CTCTRequest $CTCTRequest, $collectionUri) {
        $this->CTCTRequest = $CTCTRequest;
        $this->uri = $CTCTRequest->baseUri.$collectionUri;
    }
}
class ActivitiesCollection extends Collection{
    public $CTCTRequest;
    public $uri;

    /**
     * ActivitiesCollection constructor
     * @param CTCTRequest $CTCTRequest
     */
    public function __construct($CTCTRequest){
        parent::__construct($CTCTRequest, '/ws/customers/'.$CTCTRequest->username.'/activities');
    }

	/**
     * Add multiple contacts at once
	 * @param string $postString - urlencoded string of data
	 * @return bool
     */
    public function bulkAddContacts($postString){
        $response =  $this->CTCTRequest->makeRequest($this->uri, 'POST', $postString, 'application/x-www-form-urlencoded');
		return ($response['info']['http_code'] == 204) ? true : false;
    }
    
}

class ListsCollection extends Collection{
    public $CTCTRequest;
    public $uri;

    /**
     * ListsCollection constructor
     * @param CTCTRequest $CTCTRequest
     */
    public function __construct($CTCTRequest){
        parent::__construct($CTCTRequest, '/ws/customers/'.$CTCTRequest->username.'/lists');
    }

    /**
     * Get an array of ContactList objects
     * @param string $page - optional link to a lists page, default is first page
     * @return array
     */
    public function getLists($page=null, $systemLists=false){
        $page = ($page) ? $this->CTCTRequest->baseUri.$page : $this->uri;
        $listsCollection = array('lists' => array(), 'nextLink' => '');
        $ignoreArray = array("Active", "Removed", "Do Not Mail");
        $response = $this->CTCTRequest->makeRequest($page, 'GET');
        $parsedResponse = simplexml_load_string($response['xml']);
        foreach ($parsedResponse->entry as $entry){
            if($systemLists == false && !in_array((string)$entry->content->ContactList->Name, $ignoreArray)){
                $listsCollection['lists'][] = new ContactList(ContactList::createStruct($entry));
            } elseif($systemLists == true) {
                $listsCollection['lists'][] = new ContactList(ContactList::createStruct($entry)); 
            }
        }
        $listsCollection['nextLink'] = Utility::findNextLink($parsedResponse);
        return $listsCollection;
    }

    /**
     *  Add a new list to Constant Contact account
     *  @param ContactList $List - List object created by
     *  @return ContactList - newly created ContactList object
     */
    public function addList(ContactList $List){
        $listXml = $List->createXml();
        $response = $this->CTCTRequest->makeRequest($this->uri, 'POST', $listXml);
        $parsedResponse = simplexml_load_string($response['xml']);
        return new ContactList(ContactList::createStruct($parsedResponse));
    }

    /**
     * Get a page of contacts from a given list
     * @param string $url - url to request
     * @return array
     */
    public function getListMembers(ContactList $List, $page = null){
        $membersCollection = array('members' => array(), 'nextLink' => null);
		$url = ($page) ? $this->CTCTRequest->baseUri.$page : $this->CTCTRequest->baseUri.$List->link.'/members';
        $response = $this->CTCTRequest->makeRequest($url, 'GET');
        $parsedResponse = simplexml_load_string($response['xml']);
        foreach($parsedResponse->entry as $entry){
            $membersCollection['members'][] = new Contact(ContactList::createMemberStruct($entry));
        }
        $membersCollection['nextLink'] = Utility::findNextLink($parsedResponse);
        return $membersCollection;
    }

    /**
     *  Get full details for a ContactList
     *  @param string $url - address to a list
     *  @return ContactList
     */
    public function getListDetails($url){
        $response = $this->CTCTRequest->makeRequest($url, 'GET');
        $parsedResponse = simplexml_load_string($response['xml']);
        return new ContactList(ContactList::createStruct($parsedResponse));
    }

    /**
     *  Delete a ContactList from an account
     *  @param string $url - address to a list
     *  @return bool - true is successful, else false
     */
    public function deleteList($url){
        $response = $this->CTCTRequest->makeRequest($url, 'DELETE');
        return ($response['info']['http_code'] == 204) ? true : false;
    }

    /**
     *  Update a ContactList
     *  @param string $url - address to a list
     *  @return bool - true is successful, else false
     */
    public function updateList(ContactList $List){
        $listXml = $List->createXml();
        $response = $this->CTCTRequest->makeRequest($this->CTCTRequest->baseUri.$List->link, 'PUT', $listXml);
        return ($response['info']['http_code'] == 204) ? true : false;
    }
}

class ContactsCollection extends Collection{
    public $CTCTRequest;
    public $uri;

    /**
     * ContactsCollection constructor
     * @param CTCTRequest  $CTCTRequest
     */
    public function __construct($CTCTRequest){
        parent::__construct($CTCTRequest, '/ws/customers/'.$CTCTRequest->username.'/contacts');
    }

    /**
     * @param string $page - optional link to a contacts page, default is first page
     * @return array
     */
    public function getContacts($page=null){
        $page = ($page) ? $this->CTCTRequest->baseUri.$page : $this->uri;
        $contactsCollection = array('contacts' => array(), 'nextLink' => '');
        $response = $this->CTCTRequest->makeRequest($page, 'GET');
        $parsedResponse = simplexml_load_string($response['xml']);
        foreach ( $parsedResponse->entry as $entry){
            $contactsCollection['contacts'][] = new Contact(Contact::createStruct($entry));
        }
        $contactsCollection['nextLink'] = Utility::findNextLink($parsedResponse);
        return $contactsCollection;    
    }

    /**
     * Get detailed Contact object for a given contact
     * @param string  $url - url of contact to get details for
     * @return Contact
     */
    public function getContactDetails($url){
        $response = $this->CTCTRequest->makeRequest($url, 'GET');
        $parsedResponse = simplexml_load_string($response['xml']);
        return new Contact(Contact::createStruct($parsedResponse));
    }

    /**
     * Get events for a given Contact
     * @param string url - url to access events from
     * @param string $eventType - event type to pull
     * @return array
     */
    public function getContactEvents($url, $eventType){
        $response = $this->CTCTRequest->makeRequest($url, 'GET');
        $parsedResponse = simplexml_load_string($response['xml']);
        $contactEvents = array('events' => array(), 'nextLink' => '');
        if($parsedResponse->entry){
            foreach($parsedResponse->entry as $entry){
                $contactEvents['events'][] = new CampaignEvent(CampaignEvent::createStruct($entry, $eventType));
            }
        }
        $contactEvents['nextLink'] = Utility::findNextLink($parsedResponse);
        return $contactEvents;
    }

    /**
     * Add a contact to account
     * @param Contact $Contact - Contact object to add
     * @return Contact
     */
    public function addContact(Contact $Contact){
        $contactXml = $Contact->createXml();
        $response = $this->CTCTRequest->makeRequest($this->uri, 'POST', $contactXml);
        $parsedResponse = simplexml_load_string($response['xml']);
        return new Contact(Contact::createStruct($parsedResponse));
    }

    /**
     * Update a Contact
     * @param Contact $Contact
     * @return bool - true is successful, else false
     */
    public function updateContact(Contact $Contact){
        $contactXml = $Contact->createXml();
        $response = $this->CTCTRequest->makeRequest($this->CTCTRequest->baseUri.$Contact->link, 'PUT', $Contact->createXml());
        return ($response['info']['http_code'] == 204) ? true : false;
    }

    /**
     * Delete a Contact
     * @param string $url - url to a contact
     * @return bool - true is successful, else false
     */
    public function deleteContact($url){
        $response = $this->CTCTRequest->makeRequest($url, 'DELETE');
        return ($response['info']['http_code'] == 204) ? true : false;
    }

    /**
     * Search for Contacts by email addresses
     * @param mixed extension - string to be appended to url
     * @return - array of found contacts, otherwise returns false
     */
    public function searchContactsByEmail($extension){
        $response = $this->CTCTRequest->makeRequest($this->uri.$extension, 'GET');
        $parsedResponse = simplexml_load_string($response['xml']);
        $returnArray = array();
        if ($parsedResponse->entry){
            foreach ($parsedResponse->entry as $contact){
                $returnArray[] = new Contact(Contact::createStruct($contact));
            }
        } else { $returnArray = false; }
        return $returnArray;
    }

    /**
     * Search for contacts that have been updated since a specific date
     * @param string  $date
     * @param ContactList $List
     * @return array
     */
    public function searchContactsByLastUpdate($List, $date){
        $listTypes = array('Active', 'Removed', 'Do Not Mail');
        $ext = '';
        try{
            $ext .= str_replace('https://api.constantcontact.com', '',$this->uri).'?updatedsince='.$date;
            if(in_array($List->name, $listTypes)){$ext .= '&listtype='.strtolower(str_replace(' ', '-', $List->name));}
            else{$ext .= '&listid='.substr(strrchr($List->id, "/"), 1);}
        } catch (CTCTException $e){
            $e->generateError();
        }
        return $this->getContacts($ext);
    }
}

class CampaignsCollection extends Collection{
    public $CTCTRequest;
    public $uri;

    /**
     * CampaingCollection constructor
     * @param CTCTReques $CTCTRequest
     */
    public function __construct($CTCTRequest){
        parent::__construct($CTCTRequest, '/ws/customers/'.$CTCTRequest->username.'/campaigns');
    }

    /**
     * Get a page of Campaigns
     * @param string $page - url to page of campaigns, default is first page
     * @return array - array of Campaign objects and a link to the next page if one exists
     */
    public function getCampaigns($page=null){
        $page = ($page) ? $this->CTCTRequest->baseUri.$page : $this->uri;
        $campaignsCollection = array('campaigns' => array(), 'nextLink' => '');
        $response = $this->CTCTRequest->makeRequest($page, 'GET');
        $parsedResponse = simplexml_load_string($response['xml']);
        foreach ($parsedResponse->entry as $entry){
            $campaignsCollection['campaigns'][] = new Campaign(Campaign::createOverviewStruct($entry));
        }
        $campaignsCollection['nextLink'] = Utility::findNextLink($parsedResponse);
        return $campaignsCollection;
    }

    /**
     * Get detailed Campaign object
     * @param string $url - url of a Campiagn
     * @return Campaign
     */
    public function getCampaignDetails($url){
        $response = $this->CTCTRequest->makeRequest($url, 'GET');
        $parsedReturn = simplexml_load_string($response['xml']);
        return new Campaign(Campaign::createStruct($parsedReturn));
    }

    /**
     * Update a campaign with it's current properties
     * @param Campaign $Campaign - Campaign to be updated
     * @return bool - true if success, else false
     */
    public function updateCampaign(Campaign $Campaign){
        $response =  $this->CTCTRequest->makeRequest($this->CTCTRequest->baseUri.$Campaign->link, 'PUT', $Campaign->createXml());
        return ($response['info']['http_code'] == 204) ? true : false;
    }

    /**
     * Get a page of campaigns by status
     * @param string $status - draft, running, sent, scheduled
     * @return array  - array of campaigns a link to the next page if one exists
     */
    public function getCampaignsByStatus($status, $page = null){
        $statusList = array('sent', 'draft', 'running', 'scheduled');
        $status = strtolower($status);
        $campaignsCollection = array('campaigns' => array(), 'nextLink' => '');
        try{
            if(!in_array($status, $statusList)){throw new CTCTException("Campaign status '".$status."' is not a valid option");}
			$url = ($page) ? $this->CTCTRequest->baseUri.$page : $this->uri.'?status='.$status; 
            $response = $this->CTCTRequest->makeRequest($url, 'GET');
            $parsedResponse = simplexml_load_string($response['xml']);
            foreach ($parsedResponse->entry as $entry){
                $campaignsCollection['campaigns'][] = new Campaign(Campaign::createOverviewStruct($entry));
            }
            $campaignsCollection['nextLink'] = Utility::findNextLink($parsedResponse);
        } catch (CTCTException $e) {
            $e->generateError();
        }
        return ($campaignsCollection['campaigns']) ? $campaignsCollection : false;
    }

    /**
     * Schedule an email for delivery
     * @param Campaign $Campaign - Campaign to be scheduled
     * @param string $scheduleTime - Date/Time for the email to be delivered
     * @return bool - true if success, else false
     */
    public function scheduleCampaign(Campaign $Campaign, $time){
        $Schedule = new Schedule();
        $Schedule->campaign = $Campaign;
        $Schedule->time = $time;
        $schedXml = $Schedule->createXml();
        $response =  $this->CTCTRequest->makeRequest($this->CTCTRequest->baseUri.$Campaign->link.'/schedules', 'POST', $schedXml);
        $parsedResponse = simplexml_load_string($response['xml']);
        return new Schedule(Schedule::createStruct($parsedResponse));
    }

    /**
     * Get a schedule for an email
     * @param Campaign $Campaign - Email to obtain schedule for
     * @return bool|Schedule - Returns schedule if found, else false
     */
    public function getSchedule(Campaign $Campaign){
        $response = $this->CTCTRequest->makeRequest($this->CTCTRequest->baseUri.$Campaign->link.'/schedules', 'GET');
        $parsedResponse = simplexml_load_string($response['xml']);
        if($parsedResponse->entry){return new Schedule(Schedule::createStruct($parsedResponse->entry));}
        else{ return false; }
    }

    /**
     * Delete a schedule from a campaign (prevent from sending)
     * @param Campaign $Campaign - Campaign to delete schedule for
     * @return bool - true is successful, else false
     */
    public function deleteSchedule(Campaign $Campaign){
        $response = $this->CTCTRequest->makeRequest($this->CTCTRequest->baseUri.$Campaign->link.'/schedules/1', 'DELETE');
        if($response['info']['http_code'] == 204){ return true; }
        else{ return false; }
    }

    /**
     * @param string url - url to use to access events from
     * @param string $eventType - Sends, Forwards, Bounces, OptOuts, Opens
     * @return array - Found CampaignEvents
     */
    public function getCampaignEvents($url, $eventType){
        $response = $this->CTCTRequest->makeRequest($url, 'GET');
        $parsedResponse = simplexml_load_string($response['xml']);
        $events = array();
        foreach($parsedResponse->entry as $entry){
            $events[] = new CampaignEvent(CampaignEvent::createStruct($entry, $eventType));
        }
        return $events;
    }

    /**
     * Delete a Campaign
     * @param string $url - url to a campaign
     * @return bool - true is successful, else false
     */
    public function deleteCampaign($url){
        $response = $this->CTCTRequest->makeRequest($url, 'DELETE');
        return ($response['info']['http_code'] == 204) ? true : false;
    }

    /**
     * Add a new Campaign
     * @throws CTCTException - If campaign subject/name are not set
     * @param Campaign $Campaign
     * @return Campaign
     */
    public function addCampaign(Campaign $Campaign){
        $campaignXml = $Campaign->createXml();
        $response = $this->CTCTRequest->makeRequest($this->uri, 'POST', $campaignXml);
        $parsedResponse = simplexml_load_string($response['xml']);
        return new Campaign(Campaign::createStruct($parsedResponse));
    }
}

class LibraryCollection extends Collection{
    public $CTCTRequest;
    public $uri;

    /**
     * LibraryCollection constructor
     * @param  $CTCTRequest
     */
    public function __construct($CTCTRequest){
        parent::__construct($CTCTRequest, '/ws/customers/'.$CTCTRequest->username.'/library');
    }

    /**
     * Add a new Folder
     * @param string $name - Folder name
     * @return Folder
     */
    public function addFolder(Folder $Folder){
        $listXml = $Folder->createXml();
        $response = $this->CTCTRequest->makeRequest($this->uri.'/folders', 'POST', $listXml);
        $parsedResponse = simplexml_load_string($response['xml'], null, null, "http://www.w3.org/2005/Atom");
        return new Folder(Folder::createStruct($parsedResponse));
    }

    /**
     * Get a page of Folders
     * @param string $page - url to a page of folders, default is first page
     * @return array - Folder objects and a link to the next page if one exists
     */
    public function getFolders($page=null){
        $page = ($page) ? $this->CTCTRequest->baseUri.$page : $this->uri.'/folders';
        $foldersCollection = array('folders' => array(), 'nextLink' => '');
        $response = $this->CTCTRequest->makeRequest($page, 'GET');
        $parsedResponse = simplexml_load_string($response['xml'], null, null, "http://www.w3.org/2005/Atom");
        foreach ($parsedResponse->entry as $folder){
            $foldersCollection['folders'][] = new Folder(Folder::createStruct($folder));
        }
        $foldersCollection['nextLink'] = Utility::findNextLink($parsedResponse);
        return $foldersCollection;
    }

    /**
     * Gets a page of images from a folder
     * @param string $url - url to a page of images, default is first page
     * @return array - image objects and a link to the next page if one exists
     */
    public function getImagesFromFolder($url){
        $response = $this->CTCTRequest->makeRequest($url, 'GET');
        $imageCollection = array();
        $parsedResponse = simplexml_load_string($response['xml'], null, null, "http://www.w3.org/2005/Atom");
        foreach($parsedResponse->entry as $entry){
            $imageCollection[] = new Image(Image::createStruct($entry));
        }
        return $imageCollection;
    }

    /**
     * Get details for an image
     * @param string $url - url to an image
     * @return Image
     */
    public function getImageDetails($url){
        $response = $this->CTCTRequest->makeRequest($url, 'GET');
        $parsedResponse = simplexml_load_string($response['xml'], null, null, 'http://www.w3.org/2005/Atom');
        $image = new Image(Image::createStruct($parsedResponse));
        return $image;
    }

    /**
     * Delete image from an account
     * @param string $url - url to an image
     * @return bool - true if successful, else false
     */
    public function deleteImage($url){
        $response = $this->CTCTRequest->makeRequest($url, 'DELETE');
        return ($response['info']['http_code'] == 204) ? true : false;
    }

    /**
     * Delete all the images from a folder
     * @param string $url - url to a folder
     * @return bool - true if successful, else false
     */
    public function deleteImagesFromFolder($url){
        $response = $this->CTCTRequest->makeRequest($url, 'DELETE');
        return ($response['info']['http_code'] == 204) ? true : false;
    }

    /**
     * Upload an image to a folder
     * @throws CTCTException - If file cannot me found or md5Hash cannot be created
     * @param  $imageLocation- Location of an image on disk
     * @param  $folderUrl - url of a folder to upload an image to
     * @return Image
     */
    public function uploadImage($imageLocation, $folderUrl){
        $imageName = substr(strrchr($imageLocation, '\\'), 1);
        if($imageName == false) { $imageName = $imageLocation; }
        $imageFormat = substr(strrchr($imageName, '.'), 1);
        $boundary = "---------------------".substr(md5(rand(0,32000)), 0, 10);
        try{
            $file = file_get_contents($imageLocation);
            $md5Hash = md5_file($imageLocation);
            if(empty($file)){ throw new CTCTException('Unable to get file contents from: '.$imageLocation);}
            if(empty($md5Hash)){ throw new CTCTException('Unable to create md5Hash from: '.$imageLocation);}
        } catch (CTCTException $e){
            $e->generateError('Error uploading image:');
        }
        switch($imageFormat){
            case 'JPG':
                $imageType = 'image/jpg';
                break;
            case 'GIF':
                $imageType = 'image/gif';
                break;
            case 'PNG':
                $imageType = 'image/png';
                break;
            default:
                $imageType='image/jpeg';
        }

	    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>
            <atom:entry xmlns:atom=\"http://www.w3.org/2005/Atom\">
                <atom:content>
                    <Image>
                    <FileName>".$imageName."</FileName>
                    <MD5Hash>".$md5Hash."</MD5Hash>
                    <Description />
                    </Image>
                </atom:content>
            </atom:entry>";

        $data = '--'.$boundary."\n";
        $data .= "Content-Disposition: form-data; name=\"part1\"\n";
        $data .= "Content-Type: application/atom+xml\n";
        $data .= "Accept: application/atom+xml\n\n".$xml."\n";
        $data .= "--".$boundary."\n";

        $data .= "Content-Disposition: form-data; name=\"part2\"; filename=\"".$imageName."\"\n";
        $data .= "Content-Type: ".$imageType."g\n";
        $data .= "Content-Transfer-Encoding: binary\n\n";
        $data .= $file."\n";
        $data .= "--".$boundary."--\n";

        $response = $this->CTCTRequest->makeRequest($folderUrl.'/images', 'POST', $data, "multipart/form-data; boundary=".$boundary);
        $parsedResponse = simplexml_load_string($response['xml'], null, null, "http://www.w3.org/2005/Atom");
        return new Image(Image::createStruct($parsedResponse));
    }

}

class SettingsCollection extends Collection{
    public $CTCTRequest;
    public $uri;

    /**
     * SettingsCollection constructor
     * @param  $CTCTRequest
     */
    public function __construct($CTCTRequest){
        parent::__construct($CTCTRequest, '/ws/customers/'.$CTCTRequest->username.'/settings');
    }

    /**
     * Get a list of verified email addresses for an account
     * @param string  $page - page of verified emails, default is first page
     * @return array - page of verified addresses and a link to the next page if one exists
     */
    public function getAddresses($page=null){
        $url = ($page) ? $this->CTCTRequest->baseUri.$page : $this->uri.'/emailaddresses';
        $addressCollection = array('addresses' => array(), 'nextLink' => '');
        $response = $this->CTCTRequest->makeRequest($url, 'GET');
        $parsedResponse = simplexml_load_string($response['xml']);
        foreach($parsedResponse->entry as $entry){
            $addressCollection['addresses'][] = new VerifiedAddress(VerifiedAddress::createStruct($entry));
        }
        $addressCollection['nextLink'] = Utility::findNextLink($parsedResponse);
        return $addressCollection;
    }
}

class EventsCollection extends Collection{
    public $CTCTRequest;
    public $uri;

    /**
     * EventsCollection constructor
     * @param CTCTRequest $CTCTRequest
     */
    public function __construct($CTCTRequest){
        parent::__construct($CTCTRequest, '/ws/customers/'.$CTCTRequest->username.'/events');
    }

    /**
     * Get a page of Events
     * @param string $page - url to a page of events, default is first page
     * @return array - Event objects and link to the next page if one exists
     */
    public function getEvents($page=null){
        $url = ($page) ? $this->CTCTRequest->baseUri.$page : $this->uri;
        $eventsCollection = array('events' => array(), 'nextLink' => '');
        $response = $this->CTCTRequest->makeRequest($url, 'GET');
        $parsedResponse = simplexml_load_string($response['xml'], null, null, "http://www.w3.org/2005/Atom");
        foreach($parsedResponse->entry as $entry){
            $eventsCollection['events'][] = new Event(Event::createStruct($entry));
        }
        $eventsCollection['nextLink'] = Utility::findNextLink($parsedResponse);
        return $eventsCollection;
    }

    /**
     * Get full details for an Event object
     * @param string $url - url to an Event
     * @return Event
     */
    public function getEventDetails($url){
        $response = $this->CTCTRequest->makeRequest($url, 'GET');
        $parsedResponse = simplexml_load_string($response['xml'], null, null, "http://www.w3.org/2005/Atom");
        return new Event(Event::createStruct($parsedResponse));
    }

    /**
     * Get a page of Registrants for an Event
     * @param  $url - url to an event
     * @return array - Registrants and a link to the next page if one exists
     */
    public function getRegistrants($url){
        $response = $this->CTCTRequest->makeRequest($url, 'GET');
        $registrantsArr = array('registrants' => array(), 'nextLink' => '');
        $parsedResponse = simplexml_load_string($response['xml'], null, null, "http://www.w3.org/2005/Atom");
        foreach($parsedResponse->entry as $reg){
            $registrantsArr['registrants'][] = new Registrant(Registrant::createStruct($reg));
        }
        $registrantsArr['nextLink'] = Utility::findNextLink($parsedResponse);
        return $registrantsArr;
    }

    /**
     * Get details for an Event Registrant
     * @param string $url - url to a Regitrant
     * @return Registrant
     */
    public function getRegistrantDetails($url){
        $response = $this->CTCTRequest->makeRequest($url, 'GET');
        $parsedResponse = simplexml_load_string($response['xml'], null, null, "http://www.w3.org/2005/Atom");
        return new Registrant(Registrant::createStruct($parsedResponse));
    }
}