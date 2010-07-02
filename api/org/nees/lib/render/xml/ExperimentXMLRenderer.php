<?php

/**
 * @title ExperimentXMLRenderer
 *
 * @abstract
 *    A class that knows how to convert a Experiment domain object into a
 * chunk of XML compatible with htdocs/api/NEES.xsd's experimentMetadataType
 *
 * @author
 *    Jinghong Gao
 *
 */

require_once "lib/render/Renderer.php";
require_once "lib/data/Acknowledgement.php";
require_once "lib/data/Person.php";

class ExperimentXMLRenderer implements Renderer {
  function render( BaseObject $obj, $title = null ) {
    if (!$obj instanceof Experiment) {
      throw new Exception("ExperimentArrayRenderer cannot render object of type " . get_class($obj));
    }

    $domain = $obj->getExperimentDomain();
    $domName = $domain ? $domain->getName():"";

    // Hack for Special case: Lelli's Demo Experiment 
    if($obj->getId() == 607) {
      $person = PersonPeer::findByUserName('lellivde');
      $people = array($person);
    }
    else {
      $people = PersonPeer::findAllInEntity($obj);
    }
           
    $trials = $obj->getTrials();

    $ack = AcknowledgementPeer::findByExperiment($obj->getId()
    );

    $output = "<experiment>\n";
    $output .= "  <id>" . $obj->getId() . "</id>\n";
    $output .= "  <path></path>\n";  // TODO -- experiment's path?
    // can be multiple participating organizations
    $orgs=$obj->getOrganizations();
    if (!is_null($orgs)) {
      foreach ($orgs as $org) {
        $output .= "  <organization>".$org->getName()."</organization>\n";
      }
    }
    // can be multiple facilities
    $facilities=$obj->getFacilities();
    if (!is_null($facilities)) {
      foreach ($facilities as $facility) {
        $output .= "  <facility>".xmlEncode($facility->getName())."</facility>\n";
      }
    }
    $output .= "  <type>$domName</type>\n";
    $output .= "  <name>" . xmlEncode($obj->getName()) . "</name>\n";
    $output .= "  <title>" . xmlEncode($obj->getTitle()) . "</title>\n";
    $output .= "  <objective>" . xmlEncode($obj->getObjective()) ."</objective>\n";
    $output .= "  <description>" . xmlEncode($obj->getDescription()) . "</description>\n";
    if (!is_null($ack)) {
      $content=$ack->getSponsor();
      if (! $content) $content = $ack->getHowToCite();
      $output .= "  <acknowledgement>" . xmlEncode($content) . "</acknowledgement>\n"; // TODO -- how to do this?
    }
    $output .= "  <start-date>" . $obj->getStartDate() . "</start-date>\n";
    $output .= "  <end-date>" . $obj->getEndDate() . "</end-date>\n";   
    
    if (!is_null($people)) {
      $output .= "\t<associated-people>\n";
      foreach($people as $person) {
        
  
        
        $output .= "\t\t<person>\n";
        $output .= "\t\t\t<first-name>".$person->getFirstName()."</first-name>\n";
        $output .= "\t\t\t<last-name>".$person->getLastName()."</last-name>\n";
        $output .= "\t\t\t<email>".$person->getEMail()."</email>\n";
        if($roles = $person->getRolesForEntity($obj)) {
          foreach($roles as $r) $output .= "\t\t\t<role>".$r->getName()."</role>\n";
        }
        $output .= "\t\t</person>\n";
      }
      $output .= "\t</associated-people>\n";
    }

    if (!is_null($trials)) {
      foreach ($trials as $t) {
        $output .= "  <trial>https://" . $_SERVER['SERVER_NAME'] . "/trial/get/" .
        $obj->getProject()->getId() . "/" . $obj->getId() . "/" . $t->getId() . "/XML</trial>\n";
      }
    }
    $output .= "</experiment>\n";

    return $output;
  }
}
?>
