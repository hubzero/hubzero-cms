<?php

/**
 * @title ProjectXMLRenderer
 *
 * @abstract
 *    A class that knows how to convert a Project domain object into a
 * chunk of XML compatible with htdocs/api/NEES.xsd's projectMetadataType
 *
 * @author
 *    Adam Birnbaum
 *
 */

require_once "lib/render/Renderer.php";
require_once "lib/data/Acknowledgement.php";

class ProjectXMLRenderer implements Renderer {
  function render( BaseObject $obj, $title = null ) {
    if (!$obj instanceof Project) {
      throw new Exception("ProjectArrayRenderer cannot render object of type " . get_class($obj));
    }

    $server_prefix = "https://" . $_SERVER['SERVER_NAME'];
    $experiments = $obj->getExperiments();

    $output = "<project>\n";
    $output .= "  <id>" . $obj->getId() . "</id>\n";
    //  $output .= "  <path></path>\n";  // TODO -- what is a project's path?
    $output .= "  <name>" . xmlEncode($obj->getName()) . "</name>\n";
    $output .= "  <title>" . xmlEncode($obj->getTitle()) . "</title>\n";
    $output .= "  <description>" . xmlEncode($obj->getDescription()) . "</description>\n";
    $output .= "  <acknowledgement>" . xmlEncode($obj->getProjectAcknowledgement()) . "</acknowledgement>\n";
    $output .= "  <contact-name>" . $obj->getContactName() . "</contact-name>\n";
    $output .= "  <contact-email>" . $obj->getContactEmail() . "</contact-email>\n";
    $output .= "  <start-date>" . $obj->getStartDate() . "</start-date>\n";
    $output .= "  <end-date>" . $obj->getEndDate() . "</end-date>\n";
    $output .= "  <sysadmin-name>" . $obj->getSysadminName() . "</sysadmin-name>\n";
    $output .= "  <sysadmin-email>" . $obj->getSysadminEmail() . "</sysadmin-email>\n";
    $output .= "  <status>" . $obj->getStatus() . "</status>\n";
    $output .= "  <nickname>" . $obj->getNickname() . "</nickname>\n";
    $output .= "  <fundorg>" . $obj->getFundorg() . "</fundorg>\n";
    $output .= "  <fundorg-project-id>" . $obj->getFundorgProjID() . "</fundorg-project-id>\n";
    $output .= "</project>\n";
    return $output;
  }
}
?>