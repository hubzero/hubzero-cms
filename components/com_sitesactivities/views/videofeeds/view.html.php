<?php
/**
 * @package		NEEShub 
 * @author		David Benham (dbenha@purdue.edu)
 * @copyright           Copyright 2010 by NEES
*/
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
/**
 * 
 * 
 */
 
class sitesactivitiesViewvideofeeds extends JView
{
    function display($tpl = null)
    {
    	// Get the tabs for the top of the page
        $tabs = SitesActivitiesHelper:: getSitesActivitiesTabs(3);
        $this->assignRef('tabs', $tabs);

        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();
        $pathway   =& $mainframe->getPathway();
        $document->setTitle('Site Video Feeds');


    	// Get the site
        $facilityID = JRequest::getVar('id');

        if($facilityID == null)
        {
            $facilityID = 228; //hardcode the second one (UCLA has no feeds)
            JRequest::setVar('id', '228');
        }

        $this->assign('facilityID', $facilityID);
    	$facility = FacilityPeer::find($facilityID);

        // Breadcrum additions
        $pathway->addItem( 'Site Video Feeds', JRoute::_('index.php?option=com_sitesactivities&view=videofeeds'));
        $pathway->addItem( $facility->getName() . ' live video feeds', JRoute::_('/index.php?option=com_sitesactivities&id=' . $facilityID . '&view=videofeeds'));


        // If a facility is defined for this page
    	if($facility)
        {
            $facilityName = $facility->getName();
            $flexURL =    $facility->getFlexTpsUrl();

            preg_match("/^(https?:\/\/)?([a-zA-Z0-9\\-\\.\\/]+)(\/?site\/?|\/?feeds\/?|\/?collaboration\/?|\/?portal\/?|\/?dvr\/?)?$/Ui",
                $flexURL, $matches);

            $cleanflexURL = isset($matches[2]) ? "http://" . rtrim($matches[2], '/') : "";
            $flexURL = rtrim($matches[0], '/');
            $xmlresult = @file_get_contents("$cleanflexURL/feeds");

            preg_match_all("/<stream\s+id=\"([^\"]*)\"\s+xlink:href=\"([^\"]*)\">(.*)<\/stream>/Us",
                $xmlresult, $matches, PREG_SET_ORDER);
            
            $href_thumbs = "";

            $feed_count = count($matches);
            $this->assignRef('feed_count', $feed_count);

            $faccount = 1;
            foreach ($matches as $key => $streams ) {
                preg_match("/<max-connection-length>([\d]+)<\/max-connection-length>/U", $streams[3], $submatches);
                $timeout = $submatches[1];
                $name_stream = $streams[1];
                $stream = $streams[2];
                $splitURLS = explode("/", $stream);
                $name = str_replace("_", " ", $splitURLS[sizeof($splitURLS)-2]);

                $name_stream_trunc = $name . ' : ' . $name_stream;
                $name_stream_trunc = rtrim(substr($name_stream_trunc, 0, 30)) . (strlen($name_stream_trunc) > 30 ? '...' : '' );

                if (empty($first_href)) {
                    $first_href = $stream;
                    $first_timeout = $timeout;
                    $first_root_name = $name;
                    $first_href_name = $name_stream;
                    $first_name = $first_root_name.": ".$first_href_name;
                    $this->assignRef('first_name', $first_name);
                }

                if($stream === $first_href){
                    $stream_class = "nawi_camview_caption-active";
                }
                else{
                    $stream_class = "nawi_camview_caption";
                }

                if($faccount == 1)
                    $activeItemClassName = 'feed-active';
                else
                    $activeItemClassName = 'feed-inactive';

                $href_thumbs .= <<<ENDHTML
                    <div>

                        <div style="width:110px; height:125px; float:left; margin-bottom:10px" id="tn_$key" name="tn_$key">
                            <a class="imagelink-no-underline" id="l_$key" name="l_$key" title="$name: $name_stream" href="javascript:void(0);" onclick="updateStream('$key', '$name: $name_stream', '$stream/jpeg', '$timeout')">

                                <img id="i_$key" name="i_$key" title="$name: $name_stream" class="$activeItemClassName" src="$stream/jpeg" width="87" height="72" alt="" />

                                <div style="font-size:x-small; text-decoration:none; width:90px; filter: alpha(opacity=85);" align="center"" id="t_$key" name="t_$key" class="$stream_class truncate">
                                    $name_stream_trunc
                                </div>
                            </a>
                        </div>
                    </div>

ENDHTML;

                $faccount++;
            } // end foreach loop



        }

        $this->assignRef('first_href', $first_href);
        $this->assignRef('href_thumbs', $href_thumbs);
        $this->assignRef('facilityName', $facilityName);


        parent::display($tpl);

    }
    
}
