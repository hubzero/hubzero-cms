<?php
/**
 * @package     hubzero-cms
 * @author      Sam Ling <ling21@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */
defined('_JEXEC') or die( 'Restricted access' );


/**
 * Resource map generator in XML+RDFa format
 *
 * Generates resource map in XML+RDFa format. Inserts link into header
 * and provides download for the end user.
 *
 * @package     hubzero-cms
 * @subpackage  com_resources
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 * @version     Release: @package_version@
 * @since       Class available since Release 1.5.0
 */
class ResourceMapGenerator
{
    // Add namespaces as needed
    private $xmlNamespace = array(
            'xmlns:dc' => 'http://purl.org/dc/elements/1.1/',
            'xmlns:dcterms' => 'http://purl.org/dc/terms/',
            'xmlns:ore' => 'http://www.openarchives.org/ore/terms/',
            'xmlns:rdf'=> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
            'xmlns:exif' => 'http://www.w3.org/2003/12/exif/ns#',
            'xmlns:foaf' => 'http://xmlns.com/foaf/0.1/'
    );

    // URL of the component
    private $componentURL = "";

    // URL of the publication (e.g. /publication/1234)
    private $resourceURL = "";

    // Path to file system that stores resource
    private $resourceSite = "";

    // Variables of this resource for composing the RDF file
    private $id = -1;
    private $version_id = -1;
    private $author_id;
    private $type;
    private $title;
    private $intro;
    private $created_by;
    private $date_created;
    private $date_modified;
    private $date_published;

    // List of children this resource has
    private $children = array();

    // Aggregates which resources; compiled from $children
    private $aggregates = array();

    // Parents of this resources
    private $aggregatedBy = array();

    // List of all types of the resource
    private $types;

    /**
     * Sets the ID of this resource map. Do not supply with alias.
     */
    public function setPaths()
    {
        $this->componentURL = JURI::base() . 'publications/';
        $this->resourceURL = $this->componentURL . $this->id;

        $database = JFactory::getDBO();
        $pub = new Publication( $database );
        $publication = $pub->getPublication($this->id);
        $this->resourceSite = JPATH_BASE . '/site/publications/' .
                \Hubzero\Utility\String::pad($this->id) . DS .
                \Hubzero\Utility\String::pad($publication->version_id) . DS .
                $publication->secret . DS;
    }

    /**
     * Checks if the ID exists in the database.
     *
     * @param int $id   ID to check
     * @return boolean  True if exists, false otherwise.
     */
    public static function idExists($id)
    {
        if (!empty($id) && $id != -1)
        {
            // Grabs the database object
            $database = JFactory::getDBO();
            // Attempts to load $id on this database object.
            $resource = new PublicationVersion($database);

            return $resource->getLastPubRelease($id);
        }

        return false;
    }

    /**
     * Formats $date into ISO 8601 format
     *
     * @param $date Date to be formatted
     * @return Formatted date
     */
    private function formatDate($date)
    {
        $date_ary = date_parse($date);
        return $date_ary['year'] . '-'
                . str_pad($date_ary['month'], 2, "0", STR_PAD_LEFT). '-'
                . str_pad($date_ary['day'], 2, "0", STR_PAD_LEFT) . 'T'
                . str_pad($date_ary['hour'], 2, "0", STR_PAD_LEFT) . ':'
                . str_pad($date_ary['minute'], 2, "0", STR_PAD_LEFT) . ':'
                . str_pad($date_ary['second'], 2, "0", STR_PAD_LEFT);
    }

    /**
     * Sets the fields in this class with database data.
     *
     * @return boolean  True if succss and False otherwise
     */
    private function populateRDFData()
    {
        // Grabs database object
        $database = JFactory::getDBO();
        $resource = new PublicationVersion( $database );
        $resource = $resource->getLastPubRelease($this->id);

        if (!$resource)
        {
            // Return if ID does not exist
            JError::raiseError( 404, JText::_('COM_PUBLICATIONS_FILE_NOT_FOUND') );
            return false;
        }

        $this->setPaths();

        // Gets the author name
        $this->author_id = $resource->created_by;
        $this->created_by = JUser::getInstance($this->author_id)->name;

        // Set created date
        $this->date_created = $this->formatDate($resource->created);

        // Set description
        $this->intro = $resource->description;

        // Set title
        $this->title = $resource->title;

        // Set last modified date
        $this->date_modified = $this->formatDate($resource->modified);

        // Set published date
        $this->date_published = $this->formatDate($resource->published_up);

        // Set version ID
        $this->version_id = $resource->id;

        // Load the $types variable. In the form of array([type_id] => [type_name]).
        $resource = new Publication($database);
        $pub = $resource->getPublication($this->id);

        $type_id = $pub->category;
        $typesData = new PublicationCategory($database);
        $allTypes = $typesData->getCategories(array(
                'state' => 'all'
        ));

        $this->types = array();
        foreach ($allTypes as $type)
        {
            $types[$type->id] = $type->name;
        }

        // Get the type name of this resource
        $this->type = $types[$type_id];

        // Get attachments
        $pubAttach = new PublicationAttachment($database);
        $attachments = $pubAttach->getAttachments($this->version_id);

        foreach ($attachments as $child)
        {
            $this->aggregates[$child->id]['parent_id'] = $this->id;
            $this->aggregates[$child->id]['title'] = basename($child->path);
            $this->aggregates[$child->id]['created'] = $this->formatDate($child->created);
            $this->aggregates[$child->id]['standalone'] = 0;
            $this->aggregates[$child->id]['path'] = $child->path;
            $this->aggregates[$child->id]['url'] = $this->componentURL . $this->id . '/serve?a=' . $child->id;
        }

        return true;
    }

    /**
     * Inserts data infomation for each datatype.
     *
     * @param array $aggregation    The aggregation that contains the data to be inserted
     * @param XMLWriter $xmlwriter  The XMLWriter object for writing to buffer
     */
    private function dataTypeDescriptor($aggregation, $xmlwriter)
    {
        $datatype = mime_content_type($this->resourceSite . $aggregation['path']);

        if (isset($datatype) && !empty($datatype))
        {
            $xmlwriter->writeElement('dc:format', $datatype);
        }

        $dataCategory = explode('/', $datatype);

        switch ($dataCategory[0])
        {
            case 'image':
                // SVG types do not have height/width
                if (stripos($datatype, 'svg') !== FALSE) {
                    break;
                }

                $imageData = getimagesize($this->resourceSite . $aggregation['path']);
                $xmlwriter->writeElement('exif:width', $imageData[0]);
                $xmlwriter->writeElement('exif:height', $imageData[1]);
                break;
            default:
        }
    }

    /**
     * Builds the resource map in XML+RDFa format
     *
     * @return String containig the XML; NULL on error
     */
    public function getResourceMap()
    {
        // First populate all the data
        if (!$this->populateRDFData())
        {
            return null;
        }

        // Creates new XMLWriter
        $writer = new XMLWriter;

        // Writes XML to buffer
        $writer->openMemory();

        // Sets document formatting
        $writer->setIndent(true);
        $writer->setIndentString("  ");

        $writer->startDocument("1.0", "UTF-8");

        $writer->startElement("rdf:RDF");

        // Creates object with all the required name spaces
        foreach ($this->xmlNamespace as $ns => $url)
        {
            $writer->writeAttribute($ns, $url);
        }

        // Start main description
        $writer->startElement('rdf:Description');
            $writer->writeAttribute('rdf:about', $this->resourceURL);
            $writer->writeElement('dc:identifier', $this->id);
            $writer->writeElement('dcterms:title', $this->title);

            // Check if the author is an actual human
            if ($this->author_id >= 1000)
            {
                $writer->startElement('dcterms:creator');
                    $writer->startElement('foaf:person');
                        $writer->writeElement('foaf:name', $this->created_by);
                    $writer->endElement();
                $writer->endElement();
            }

            $writer->writeElement('dcterms:created', $this->date_created);
            $writer->writeElement('dcterms:modified', $this->date_modified);
            $writer->writeElement('dc:type', $this->type);
            $writer->writeElement('dc:description', $this->intro);

            // Writes aggregation
            foreach ($this->aggregates as $child_id => $aggregation)
            {
                $writer->startElement('ore:aggregates');
                    $writer->writeAttribute('rdf:resource', $aggregation['url']);
                $writer->endElement();
            }

            // Writes describer (should be RDF in this case)
            $writer->startElement('ore:isDescribedBy');
                $writer->writeAttribute('rdf:resource', $this->resourceURL . '.rdf');
            $writer->endElement();

            // Writes parents
            foreach ($this->aggregatedBy as $parent_id => $aggregation)
            {
                $writer->startElement('ore:isAggregatedBy');
                    $writer->writeAttribute('rdf:resource', $this->componentURL . $parent_id);
                $writer->endElement();
            }

            $writer->writeElement('dcterms:issued', $this->date_published);
        // End rdf:description of main object
        $writer->endElement();

        // Describe each aggregation briefly
        foreach ($this->aggregates as $aggregation)
        {
            // If the child is also an aggregation, do not need to describe it as it has its own RDF descriptor
            if ($aggregation['standalone'] == 1)
            {
                continue;
            }

            $writer->startElement('rdf:Description');
                $writer->writeAttribute('rdf:about', $aggregation['url']);
                // Detects and describe the data accordingly
                $this->dataTypeDescriptor($aggregation, $writer);
            $writer->endElement();
        }

        // Describe this rdf briefly
        $writer->startElement('rdf:Description');
            $writer->writeAttribute('rdf:about', $this->resourceURL . '.rdf');
            $curtime = date("Y-m-d\TH:i:sP",$_SERVER['REQUEST_TIME']);
            $writer->startElement('dcterms:creator');
                $writer->writeAttribute('rdf:resource', JURI::base());
            $writer->endElement();
            $writer->writeElement('dcterms:created', $curtime);
            $writer->writeElement('dcterms:modified', $curtime);
            $writer->startElement('ore:describes');
                $writer->writeAttribute('rdf:resource', $this->resourceURL);
            $writer->endElement();
            $writer->startElement('rdf:type');
                $writer->writeAttribute('rdf:resource', 'http://www.openarchives.org/ore/terms/ResourceMap/');
            $writer->endElement();
        $writer->endElement();

        $writer->endDocument();

        // Flush the memory and return it
        return $writer->flush();
    }

    /**
     * Creates a download handle to the client; exits if there were problem getting the XML
     */
    public function pushDownload()
    {
        $alias = JRequest::getVar( 'alias', '' );

        $this->id = "";
        if (substr($alias, -4) == ".rdf")
        {
            $lastSlash = strrpos($alias, "/");
            $lastDot = strrpos($alias, ".rdf");
            $this->id = substr($alias, $lastSlash, $lastDot);
        }

        $rdfa = $this->getResourceMap();

        if ($rdfa == null)
        {
            JError::raiseError( 404, JText::_('COM_PUBLICATIONS_FILE_NOT_FOUND') );
            return false;
        }

        // Creates download headers
        header('Content-Type: application/rdf+xml');
        header('Content-Disposition: attachment; filename=' . $alias);
        header('Content-Length: ' . strlen($rdfa));

        // Push the data for download
        echo $rdfa;
        exit;
    }

    /**
     * Inserts the link to rdf download into the page. This is called by view task to insert link into head.
     *
     * @param resource $id  The integer id of the resource
     */
    public static function putRDF($id)
    {
        if (empty($id))
        {
            return;
        }
        else if (!ResourceMapGenerator::idExists($id))
        {
            return;
        }

        $rdfURL = "/publications/" . $id . ".rdf";
        $doc = JFactory::getDocument();
        $doc->addCustomTag('<link rel="resourcemap" type="application/rdf+xml" href="' . $rdfURL . '" />');
    }
}
