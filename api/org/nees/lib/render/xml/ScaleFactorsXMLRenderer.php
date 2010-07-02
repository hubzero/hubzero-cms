<?php
/**
 * @title ScaleFactorsXMLRenderer
 *
 * @abstract
 *    A class that knows how to convert a Material domain objecti and it's subordinate objects into a
 * chunk of XML
 *
 * @author
 *    Ben Steinberg (with much thanks to Jessica)
 *
 */

require_once "lib/render/Renderer.php";
require_once "lib/data/SimilitudeLawValue.php";
require_once "lib/data/SimilitudeLawGroup.php";

class ScaleFactorsXMLRenderer implements Renderer {
    function render( BaseObject $obj, $title = null ) {
    if (!$obj instanceof Experiment) {
      throw new Exception("ScaleFactorArrayRenderer cannot render object of type " . get_class($obj));
    }

    $A = new IndependentScaleFactorXMLRenderer;
    $output = $A->render($obj);
    $A = new DependentScaleFactorXMLRenderer;
    $output .= $A->render($obj);

    return($output);
  }
}


class ScaleFactorXMLBase {

  protected function getSimLawValue(SimilitudeLaw $law, $simValues) {

    foreach( $simValues as $value ) {
      if( $value->getSimilitudeLaw()->getId() == $law->getId() ) {
        return $value;
      }
    }
    return null;
  }

  protected function getDerivedValue($law, $coreValues) {
    return number_format($law->compute($coreValues), 4);
  }
}


class DependentScaleFactorXMLRenderer extends ScaleFactorXMLBase implements Renderer {
  function render( BaseObject $obj, $title = null ) {
	if ($obj instanceof Exeperiment) {
      throw new Exception("ScaleFactorArrayRenderer cannot render object of type " . get_class($obj));
    }

    $simValues = SimilitudeLawValuePeer::findByExperiment($obj->getId());
    $dependentGroups = SimilitudeLawGroupPeer::findByDependence($obj->getExperimentDomain(), 'dependent');

    // Generate core values list.
    $coreValues = array();
    foreach( $simValues as $val ) {
      if( $val->getSimilitudeLaw()->getDependence() == 'dependent' ) {
        $coreValues[] = $val;
      }
    }

    $output = "<dependentscalingfactors>\n";
    $output .= "\t<expid>". $obj->getId() . "</expid>\n";
    foreach( $dependentGroups as $group ) {
      $output .= "\t<scalingfactorgroup name=\"".$group->getName()."\">\n";
      $laws = $group->getSimilitudeLaws();
      foreach( $laws as $law ) {
        $output .= "\t\t<scalingfactor>\n";
        $output .= "\t\t\t<name>".$law->getName()."</name>\n";
        $output .= "\t\t\t<symbol>".xmlEncode($law->getSymbol())."</symbol>\n";
        $output .= "\t\t\t<dimension>".xmlEncode($law->getUnitDescription())."</dimension>\n";
        $output .= "\t\t\t<scalefactor>".xmlEncode($law->getDisplayEquation())."</scalefactor>\n";
        $output .= "\t\t\t<derived>".$this->getDerivedValue($law, $coreValues)."</derived>\n";

        $override = $this->getSimLawValue($law, $simValues);

        $output .= "\t\t\t<actual>";
        if($override) $output .= $override->getValue();
        $output .= "</actual>\n";

        $output .= "\t\t\t<comment>";
        if($override) $output .= $override->getComment();
        $output .= "</comment>\n";

        $output .= "\t\t</scalingfactor>\n";
      }

      $output .= "\t</scalingfactorgroup>\n";
    }

    $output .= "</dependentscalingfactors>\n";

    return $output;
  }
}

class IndependentScaleFactorXMLRenderer extends ScaleFactorXMLBase implements Renderer {
  function render( BaseObject $obj, $title = null ) {
	if (!$obj instanceof Experiment) {
      throw new Exception("ScaleFactorArrayRenderer cannot render object of type " . get_class($obj));
    }

    $simValues = SimilitudeLawValuePeer::findByExperiment($obj->getId());
    $independentGroups = SimilitudeLawGroupPeer::findByDependence($obj->getExperimentDomain(), 'independent');

   // Generate core values list.
    $coreValues = array();
    foreach( $simValues as $val ) {
      if( $val->getSimilitudeLaw()->getDependence() == 'independent' ) {
        $coreValues[] = $val;
      }
    }

    $output = "<independentscalingfactors>\n";
    $output .= "\t<expid>". $obj->getId() . "</expid>\n";
    foreach( $independentGroups as $group ) {
      $output .= "\t<scalingfactorgroup name=\"".$group->getName()."\">\n";
      $laws = $group->getSimilitudeLaws();
      foreach( $laws as $law ) {
        $output .= "\t\t<scalingfactor>\n";
        $output .= "\t\t\t<name>".$law->getName()."</name>\n";
        $output .= "\t\t\t<symbol>".xmlEncode($law->getSymbol())."</symbol>\n";
        $output .= "\t\t\t<dimension>".xmlEncode($law->getUnitDescription())."</dimension>\n";
        $output .= "\t\t\t<scalefactor>".xmlEncode($law->getDisplayEquation())."</scalefactor>\n";
        $output .= "\t\t\t<value>";

        $override = $this->getSimLawValue($law, $simValues);
        if($override)  $output .= $override->getValue();
        else $output .= $this->getDerivedValue($law, $coreValues);

        $output .= "\t\t\t</value>\n";
        $output .= "\t\t</scalingfactor>\n";
      }
      $output .= "\t</scalingfactorgroup>\n";
    }
    $output .= "</independentscalingfactors>\n";

    return $output;
  }
}

?>