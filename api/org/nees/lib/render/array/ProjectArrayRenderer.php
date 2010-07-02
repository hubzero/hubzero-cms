<?php

/**
 * @title ProjectArrayRenderer
 *
 * @abstract
 *    A class that knows how to convert a Project domain object into an
 * associative array with keys that are expected by "the old code"
 *
 * @author
 *    Adam Birnbaum
 *
 */

require_once "lib/render/Renderer.php";

class ProjectArrayRenderer implements Renderer {
  function render( BaseObject $obj, $title = null ) {
    if (!is_a($obj, "Project")) {
      throw new Exception("ProjectArrayRenderer cannot render object of type " . get_class($obj));
    }

    $array["projid"] = $obj->getId();
    $array["Name"] = $obj->getName();
    $array["Title"] = $obj->getTitle();
    $array["Description"] = $obj->getDescription();
    $array["ContactName"] = $obj->getContactName();
    $array["ContactEmail"] = $obj->getContactEmail();
    $array["SysAdminName"] = $obj->getSysadminName();
    $array["SysAdminEmail"] = $obj->getSysadminEmail();
    $array["StartDate"] = $obj->getStartDate();
    $array["EndDate"] = $obj->getEndDate();
    $array["status"] = $obj->getStatus();
    $array["hasPublishedExperiments"] = $obj->hasPublishedExperiments();
    $array["ProjectTypeId"] = $obj->getProjectTypeId();
    $array["NEES"] = $obj->getNEES();
    $array["VIEW"] = $obj->getView();
    $array["DELETED"] = $obj->getDeleted();
    $array["Nickname"] = $obj->getNickname();
    $array["Fundorg"] = $obj->getFundorg();
    $array["FundorgProjID"] = $obj->getFundorgProjID();
    return $array;
  }
}

?>