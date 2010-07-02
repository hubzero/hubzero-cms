<?php

/**
 * @title ProjectTerseHTMLRenderer
 *
 * @abstract
 *    A class that knows how to convert a domain object into a terse
 *    HTML fragment.
 *
 * @author
 *    Adam Birnbaum
 *
 */
class TerseHTMLRenderer implements Renderer {

  function __construct($arrayRenderer) {
  }

  /**
   * Determine whether the method is one that we want to present in the output.
   */
  protected function includeMethod(ReflectionMethod $method, ReflectionClass $class) {
    $methodName = $method->getName();
    return (($method->getDeclaringClass() == $class) &&   // not inherited.
            ($method->isPublic()) &&                      // is a public method.
            (preg_match("/^get/", $methodName)) &&        // is a getter.
            (!preg_match("/s\$|[Ii]d\$/", $methodName)) &&   // doesn't return a collection, and isn't an Id field.
            ($method->getNumberOfParameters() == 0));     // doesn't require input arguments.
  }

  protected function includeProperty(Property $property) {
    return (($property->getMetadataType() != Property::COLLECTION ) &&
            ($property->getMutatorSuffix() != "Id"));
  }

  protected function getObjectUrl(BaseObject $obj) {
    return "howdy";
    return null;
  }

  protected function renderField($label, $value) {
    return "\t$label: <b>$value</b><br/>\n";
  }

  protected function renderMethodCalls(DomainObject $obj) {
    $class = new ReflectionClass(get_class($obj));

    // Get a list of methods that we may call for the rendering.
    if ($class->isSubclassOf("DomainObjectMetadata")) {
      $properties = $obj->getProperties();
      foreach ($properties as $property) {
        if ($this->includeProperty($property)) {
          $methods[] = $property->getGetter();
        }
      }
    } else {
      $reflectionMethods = $class->getMethods();
      foreach ($reflectionMethods as $rm) {
        if ($this->includeMethod($rm, $class)) {
          $methods[] = $rm->getName();
        }
      }
    }

    // Call each of the methods (if it supposed to be included.)
    $retval="";
    foreach ($methods as $method) {
      $label = preg_replace("/get/", "", $method);
      $value = $obj->$method();
      if ($value && !is_array($value) && !is_object($value)) {
        $retval .= $this->renderField($label, $value);
      }
    }

    return $retval;
  }

  function render( BaseObject $obj, $linkclass = null) {
    $url = $this->getObjectUrl($obj);

    $retval = "<!-- Rendering of " . get_class($obj) . " -->\n";

    if ($url) {
      // Note that we are using the built-in "title" parameter to instead specify the
      // CSS class for the link.
      $retval .= "<a class=\"$linkclass\" style=\"font-weight: normal\" href=\"$url\">\n";
    }

    $retval .= $this->renderMethodCalls($obj);

    if ($url) {
      $retval .= "</a>";
    }

    $retval .= "\n";
    return $retval;
  }

}


?>
