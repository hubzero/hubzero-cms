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

// Set the document description
if ($this->publication->abstract) 
{
	$document = JFactory::getDocument();
	$document->setDescription(PublicationsHtml::encode_html(strip_tags($abstract)));
}

$description = '';
$model = new PublicationsModelPublication($this->publication);	
if ($this->publication->description) 
{
	$description = $model->description('parsed');
}

// Start display
$html  = '<div class="subject abouttab">'."\n";	

// Get gallery path
$webpath = $this->config->get('webpath');
$gallery_path = $this->helper->buildPath($this->publication->id, $this->publication->version_id, $webpath, 'gallery');

// Show gallery images
if ($this->params->get('show_gallery'))
{
	$pScreenshot = new PublicationScreenshot($this->database);
	$gallery = $pScreenshot->getScreenshots( $this->publication->version_id );	
	$shots = PublicationsHtml::showGallery($gallery, $gallery_path);
	if ($shots) 
	{
		$html .= ' <div class="sscontainer">'."\n";					
		$html .= $shots;
		$html .= ' </div><!-- / .sscontainer -->'."\n";
	}		
}

// Show description and metadata
$html .= "\t".'<table class="resource">'."\n";
$html .= "\t\t".'<tbody>'."\n";
$html .= PublicationsHtml::tableRow( JText::_('COM_PUBLICATIONS_ABSTRACT'), $description );

$citations = NULL;
if ($this->params->get('show_metadata')) 
{
	// Process metadata
	$metadata = PublicationsHtml::processMetadata($this->publication->metadata, $this->publication->_category);
	$html .= $metadata['html'] ? $metadata['html'] : '';
	$citations = $metadata['citations'];
}

if ($this->params->get('show_submitter') && $this->publication->submitter) 
{
	$submitter  = $this->publication->submitter->name;
	$submitter .= $this->publication->submitter->organization ? ', ' . $this->publication->submitter->organization : '';

	$html .= PublicationsHtml::tableRow( '<a name="submitter"></a>' . JText::_('COM_PUBLICATIONS_SUBMITTER'), $submitter );
}

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
		$jconfig = JFactory::getConfig();
		$site = trim( $jconfig->getValue('config.live_site'), DS);
		
		$cite->doi 		= $this->publication->doi ? $this->publication->doi : '';
		$cite->url 		= $cite->doi 
							? trim($this->config->get('doi_resolve', 'http://dx.doi.org/'), DS) . DS . $cite->doi
							: NULL;
		$cite->type 	= '';
		$cite->pages 	= '';
		$cite->author 	= $this->helper->getUnlinkedContributors( $this->authors);
		$cite->publisher= $this->config->get('doi_publisher', '' );
		
		if ($this->params->get('show_citation') == 2) {
			$citations = '';
		}
	} else {
		$cite = null;
	}

	$citeinstruct  = PublicationsHtml::citation( $this->option, $cite, $this->publication, $citations, $this->version );
	//$citeinstruct .= PublicationsHtml::citationCOins($cite, $this->publication, $this->config, $this->helper);
	$html .= PublicationsHtml::tableRow( '<a name="citethis"></a>'.JText::_('COM_PUBLICATIONS_CITE_THIS'), $citeinstruct );
}

// Show tags
if ($this->params->get('show_tags')) 
{
	$this->helper->getTagCloud( $this->authorized );
	
	if ($this->helper->tagCloud) {
		$html .= PublicationsHtml::tableRow( JText::_('COM_PUBLICATIONS_TAGS'),$this->helper->tagCloud);
	}
}

// Show version notes
if ($this->params->get('show_notes') && $this->publication->release_notes) 
{
	$notes = $model->notes('parsed');
	$html .= PublicationsHtml::tableRow( JText::_('COM_PUBLICATIONS_NOTES'), $notes );
}

// Page end
$html .= "\t".' </tbody>'."\n";
$html .= "\t".'</table>'."\n";
$html .= '</div><!-- / .subject -->'."\n";
$html .= '<div class="clear"></div>'."\n";

$html .= '<input type="hidden" name="rid" id="rid" value="'.$this->publication->id.'" />'."\n";
echo $html;
	
?>
