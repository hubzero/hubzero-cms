<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$yearFormat = "%Y";
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$yearFormat = "Y";
	$tz = false;
}

$abstract = stripslashes($this->publication->abstract);
if ($this->publication->abstract) {
	$document =& JFactory::getDocument();
	$document->setDescription(PublicationsHtml::encode_html(strip_tags($abstract)));
}

$description = '';
if ($this->publication->description) 
{	
	// No HTML, need to parse
	if ($this->publication->description == strip_tags($this->publication->description)) 
	{
	    $description = $this->parser->parse( stripslashes($this->publication->description), $this->wikiconfig );	
	}
}

// Process metadata
$metadata = PublicationsHtml::processMetadata($this->publication->metadata, $this->publication->_category, 1, $this->publication->id, $this->option, $this->parser, $this->wikiconfig);

// Start display
$html  = '<div class="subject abouttab">'."\n";	

// Get gallery path
$webpath = $this->config->get('webpath');
$gallery_path = $this->helper->buildPath($this->publication->id, $this->publication->version_id, $webpath, 'gallery');

// Show gallery images
$pScreenshot = new PublicationScreenshot($this->database);
$gallery = $pScreenshot->getScreenshots( $this->publication->version_id );	
$shots = PublicationsHtml::showGallery($gallery, $gallery_path);
if($shots) 
{
	$html .= ' <div class="sscontainer">'."\n";					
	$html .= $shots;
	$html .= ' </div><!-- / .sscontainer -->'."\n";
}		

// Show description and metadata
$html .= "\t".'<table class="resource">'."\n";
$html .= "\t\t".'<tbody>'."\n";
$html .= PublicationsHtml::tableRow( JText::_('COM_PUBLICATIONS_ABSTRACT'), $description );
$html .= $metadata['html'] ? $metadata['html'] : '';
$citations = $metadata['citations'];

// Show citations
if ($this->params->get('show_citation') && $this->publication->state == 1) 
{
	if ($this->params->get('show_citation') == 1 || $this->params->get('show_citation') == 2) 
	{
		// Build our citation object
		$cite = new stdClass();
		$cite->title = $this->publication->title;
		$cite->year = JHTML::_('date', $this->publication->published_up, $yearFormat, $tz);
	
		$cite->location = '';
		$cite->date = '';
		
		// Get hub config
		$jconfig =& JFactory::getConfig();
		$site = trim( $jconfig->getValue('config.live_site'), DS);
		
		$cite->url 		= $site . DS . 'publications' . DS . $this->publication->id . '?v='.$this->version;
		$cite->type 	= '';
		$cite->pages 	= '';
		$cite->author 	= $this->helper->getUnlinkedContributors( $this->authors);
		$cite->doi 		= $this->publication->doi ? $this->publication->doi : '';
		
		if ($this->params->get('show_citation') == 2) {
			$citations = '';
		}
	} else {
		$cite = null;
	}

	$citeinstruct  = PublicationsHtml::citation( $option, $cite, $this->publication, $citations, $this->version );
	//$citeinstruct .= PublicationsHtml::citationCOins($cite, $this->publication, $this->config, $this->helper);
	$html .= PublicationsHtml::tableRow( '<a name="citethis"></a>'.JText::_('COM_PUBLICATIONS_CITE_THIS'), $citeinstruct );
}

// Show tags
if ($this->params->get('show_tags')) {
	$this->helper->getTagCloud( $this->authorized );
	if ($this->helper->tagCloud) {
		$html .= PublicationsHtml::tableRow( JText::_('COM_PUBLICATIONS_TAGS'),$this->helper->tagCloud);
	}
}


// Show version notes
if($this->publication->release_notes) {
	$notes = $this->parser->parse( stripslashes($this->publication->release_notes), $this->wikiconfig );
	$html .= PublicationsHtml::tableRow( JText::_('COM_PUBLICATIONS_VERSION')
	. ' ' . $this->publication->version_label . ' ' . JText::_('COM_PUBLICATIONS_NOTES'),$notes);	
}

// Page end
$html .= "\t".' </tbody>'."\n";
$html .= "\t".'</table>'."\n";
$html .= '</div><!-- / .subject -->'."\n";
$html .= '<div class="clear"></div>'."\n";

$html .= '<input type="hidden" name="rid" id="rid" value="'.$this->publication->id.'" />'."\n";
echo $html;
	
?>
