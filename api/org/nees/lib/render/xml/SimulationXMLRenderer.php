<?php

/**
 * @title SimulationXMLRenderer
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

class SimulationXMLRenderer implements Renderer {
  function render( BaseObject $simulation, $title = null ) {
    if (!$simulation instanceof Simulation) {
      throw new Exception("SimulationArrayRenderer cannot render object of type " . get_class($simulation));
    }
    $domName = "Simulation";

    // Hack for Special case: Lelli's Demo Simulation 
    if($simulation->getId() == 1114) {
      $person = PersonPeer::findByUserName('lellivde');
      $people = array($person);
    }
    else {
      $people = PersonPeer::findAllInEntity($simulation);
    }

    $runs = $simulation->getSimulationRuns();

    $ack = AcknowledgementPeer::findByExperiment($simulation->getId());

    $output = "<simulation>\n";
    $output .= "  <id>" . $simulation->getId() . "</id>\n";
    $output .= "  <path></path>\n";  // TODO -- experiment's path?
    // can be multiple participating organizations
    $orgs=$simulation->getOrganizations();
    if (!is_null($orgs)) {
      foreach ($orgs as $org) {
        $output .= "  <organization>".$org->getName()."</organization>\n";
      }
    }
    // can be multiple facilities
    $facilities=$simulation->getFacilities();
    if (!is_null($facilities)) {
      foreach ($facilities as $facility) {
        $output .= "  <facility>".$facility->getName()."</facility>\n";
      }
    }
    $output .= "  <name>" . $simulation->getName() . "</name>\n";
    $output .= "  <title>" . $simulation->getTitle() . "</title>\n";
    $output .= "  <objective>" . $simulation->getObjective() ."</objective>\n";
    $output .= "  <description>" . $simulation->getDescription() . "</description>\n";
    if (!is_null($ack)) {

      $content=$ack->getSponsor();
      if (! $content) $content = $ack->getHowToCite();
      $output .= "  <acknowledgement>" . $content . "</acknowledgement>\n"; // TODO -- how to do this?

    }
    $output .= "  <start-date>" . $simulation->getStartDate() . "</start-date>\n";
    $output .= "  <end-date>" . $simulation->getEndDate() . "</end-date>\n";

    if (!is_null($people)) {
      $output .= "\t<associated-people>\n";
      foreach($people as $person) {
        $output .= "\t\t<person>\n";
        $output .= "\t\t\t<first-name>".$person->getFirstName()."</first-name>\n";
        $output .= "\t\t\t<last-name>".$person->getLastName()."</last-name>\n";
        $output .= "\t\t\t<email>".$person->getEMail()."</email>\n";
        if($roles = $person->getRolesForEntity($simulation)) {
          foreach($roles as $r) $output .= "\t\t\t<role>".$r->getName()."</role>\n";
        }
        $output .= "\t\t</person>\n";
      }
      $output .= "\t</associated-people>\n";
    }

    if (!is_null($runs)) {
      foreach ($runs as $r) {
        $output .= "  <run>https://" . $_SERVER['SERVER_NAME'] . "/run/get/" .
        $simulation->getProject()->getId() . "/" . $simulation->getId() . "/" . $r->getId() . "/XML</run>\n";
      }
    }
    $output .= "</simulation>\n";

    return $output;
  }
}
?>
