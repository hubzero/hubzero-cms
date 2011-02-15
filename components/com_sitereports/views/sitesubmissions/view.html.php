<?php
/**
 * @package		NEEShub 
 * @author		David Benham (dbenha@purdue.edu)
 * @copyright	Copyright 2010 by NEES
*/
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
/**
 * 
 * 
 */
 
class sitereportsViewsitesubmissions extends JView
{
    function display($tpl = null)
    {

        // Tabs for the page
        $tabs = SiteReportsHelper::getFacilityTabs(SiteReportsHelper::tabSiteSubmissions);
        $this->assignRef('tabs', $tabs);

        $c = new Criteria();
        $c->addAscendingOrderByColumn('Name');
        $sites = SiteReportsSitePeer::doSelectJoinOrganization($c);
        $htmlFileListing = '';

        // For each site
        /* @var $site SiteReportsSite */
        foreach($sites as $site)
        {
            $org = $site->getOrganization();
            $path = $site->getNEESGroupspacePath();

            $htmlFileListing .= '<font size="5">' . $org->getName() . '</font>';
            $htmlFileListing .= '<br/><font size="1"> (' . $path . ')</font><hr />';

            // append path
            $year = JRequest::getvar('year', "2010");
            $reporttype = JRequest::getvar('reporttype', "QAR");
            $period = JRequest::getvar('period', '1');
            $pathAppend = '/' . $year . '/' . $reporttype;

            // Grab a list of report files
            $reportFiles = $this->getFileList($path . $pathAppend, $period);
            //$reportFiles = array();

            if(count($reportFiles) == 0)
            {
                $htmlFileListing .= '<font color="red">No files submitted</font>';
            }
            else
            {
                $htmlFileListing .= '<table style="width:800px; border:0px;">';
                foreach($reportFiles as $reportFile)
                {
                    $rbValue = $year . '|' . $period . '|' . $reporttype . '|' . $reportFile['name'];
                    $htmlFileListing .= '<tr><td><input type="radio" name="s' . $org->getId() . '" value="' . $rbValue . '">' . $reportFile['name'] . '</td><td>' . date('m/d/Y G:i:s', $reportFile['lastmod']) . '</td><td>' . $reportFile['size'] . ' bytes </td></tr>';
                }
                $htmlFileListing .= '</table>';
            }

            $htmlFileListing .= '<br/><br/><br/>';

        }

        $this->assignRef('htmlFileListing', $htmlFileListing);

        parent::display($tpl);
    }



    /*
     * DFS filelisting- Just a the files, dont care about the directory containers
     *
     * regex specifies a search critieria, if specified, only filenames that match the
     * expression will be returned
     *
     */
    function getFileList($dir, $regex='')
    {
        //array to hold return value
        $retval = array();
        
        //add trailing slash if missing
        if(substr($dir, -1) != "/") $dir .= "/";

        //echo $dir . '<br/>';
        if (!file_exists($dir))
            return $retval;

        # open pointer to directory and read list of files
        $d = @dir($dir) or die("getFileList: Failed opening directory $dir for reading");
        
        while(false !== ($entry = $d->read()))
        {
            # skip hidden files
            if($entry[0] == ".") continue;

            if(is_dir("$dir$entry"))
            {
                if(is_readable("$dir$entry/"))
                {
                    $retval = array_merge($retval, $this->getFileList("$dir$entry/", $regex));
                }
            }
            else // yay, an actual file!
            {
                $filepath = "$dir$entry";

                // Do some regexp matching to filter the list of files
                if (preg_match("/" . $regex . "/i", $filepath))
                {
                    $retval[] = array( "name" => $filepath,
                        "type" => mime_content_type("$dir$entry"),
                        "size" => filesize("$dir$entry"),
                        "lastmod" => filemtime("$dir$entry") );
                }

            }

        } // end while

        $d->close();
        return $retval;

    }

    
}