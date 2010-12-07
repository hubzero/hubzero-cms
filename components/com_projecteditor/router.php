<?php

function ProjectEditorBuildRoute( &$query ){
  $segments = array();
  if(isset($query['view'])){
    $segments[] = $query['view'];
    unset( $query['view'] );
  }

  $segments = array();
  if(isset($query['task'])){
    $segments[] = $query['task'];
    unset( $query['task'] );
  }
       
  if(isset($query['id'])){
    $segments[] = $query['id'];
    unset( $query['id'] );
  };

  if(isset($query['experiments'])){
    $segments[] = $query['experiments'];
    unset( $query['experiments'] );
  };

  if(isset($query['experiment'])){
    $segments[] = $query['experiment'];
    unset( $query['experiment'] );
  };

  if(isset($query['project'])){
    $segments[] = $query['project'];
    unset( $query['project'] );
  };

  if(isset($query['members'])){
    $segments[] = $query['members'];
    unset( $query['members'] );
  };

  if(isset($query['materials'])){
    $segments[] = $query['materials'];
    unset( $query['materials'] );
  };

  if(isset($query['images'])){
    $segments[] = $query['images'];
    unset( $query['images'] );
  };

  if(isset($query['sensors'])){
    $segments[] = $query['sensors'];
    unset( $query['sensors'] );
  };

  if(isset($query['projid'])){
    $segments[] = $query['projid'];
    unset( $query['projid'] );
  };

  if(isset($query['file'])){
    $segments[] = $query['file'];
    unset( $query['file'] );
  };

  return $segments;
}

/**
 * segments[0]: project or a controller task
 * segments[1]: project identifier
 * segments[2]: experiment
 * segments[3]: experiment identifier or page subtab
 * segments[4]: subtab
 *
 * @param array $segments
 * @return array $vars
 */
function ProjectEditorParseRoute( $segments ){
  $vars = array();

  $iSize = sizeof($segments);
  switch ($iSize) {
      case 1:
          $strRequest = "task";
          if($segments[0]=="project" ||
             $segments[0]=="filebrowser" ||
             $segments[0]=="mkdir" ||
             $segments[0]=="uploadform" ||
             $segments[0]=="editmember" ||
             $segments[0]=="permissions" ||
             $segments[0]=="materialtypes" ||
             $segments[0]=="editlocation" ||
             $segments[0]=="sensorlist" ||
             $segments[0]=="createlocationplan" ||
             $segments[0]=="uploadsensors" ||
             $segments[0]=="deletemember" ||
             $segments[0]=="materialslist" ||
             $segments[0]=="editmaterial" ||
             $segments[0]=="editdrawing" ||
             $segments[0]=="createtrial" ||
             $segments[0]=="createrepetition" ||
             $segments[0]=="editdatafile" ||
             $segments[0]=="editphoto" ||
             $segments[0]=="editvideo" ||
             $segments[0]=="editdocument" ||
             $segments[0]=="editanalysis" ||
             $segments[0]=="editexperimentaccess" ||
             $segments[0]=="multiplefiles" ||
             $segments[0]=="repetitions" ||
             $segments[0]=="sensortypes" ||
             $segments[0]=="sensorrequired" ||
             $segments[0]=="editrole"){
            $strRequest = "view";
          }
          //echo $strRequest;
          $vars[$strRequest] = $segments[0];
          break;
      case 2:
          $vars["view"] = "project";
          $vars["projid"] = $segments[1];
          break;
      case 3:
          $vars["view"] = ($segments[2]=="about") ? $segments[0] : $segments[2];
          $vars["projid"] = $segments[1];

          break;
      case 4:
          $vars["view"] = "experiment";
          $vars["projid"] = $segments[1];
          $strPattern = "/([0-9])+/";
          if(preg_match($strPattern, $segments[3])){
            $vars["experimentId"] = $segments[3];
          }else{
            $strView = ($segments[3]=="about") ? "experiment" : $segments[3];
            $vars["subtab"] = $segments[3];
            $vars["view"] = $strView;
          }
          break;
      case 5:
          $vars["projid"] = $segments[1];
          $vars["experimentId"] = $segments[3];
          $strView = ($segments[4]=="about") ? "experiment" : $segments[4];
          $vars["subtab"] = $segments[4];
          $vars["view"] = $strView;
          break;
      default:
          $vars["view"] = "project";
          break;
  }


  return $vars;
}


?>