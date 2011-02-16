<?php 

function WarehouseBuildRoute( &$query ){
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
  
  if(isset($query['keywords'])){
    $segments[] = $query['keywords'];
    unset( $query['keywords'] );
  };
  
  if(isset($query['limit'])){
    $segments[] = $query['limit'];
    unset( $query['limit'] );
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

function WarehouseParseRoute( $segments ){
  $vars = array();
  //print_r($segments);
  if(sizeof($segments)>0){
	  switch($segments[0]){
	    case 'projects':  //view
	      $vars['view'] = 'projects';
	      $id = explode( ':', $segments[1] );
	      $vars['index'] = (int) $segments[2];
	      $vars['limit'] = (int) $segments[4];
	      break;
	    case 'project':  //view
	      $vars['view'] = 'project';
	      $id = explode( ':', $segments[1] );
	      $vars['id'] = (int) $id[0];
	      break;
            case 'find':  //task
	      $vars['task'] = 'find';
	      break;
            case 'filter':  //task
	      $vars['task'] = 'filter';
	      break;
            case 'searchfilter':  //view
	      $vars['view'] = 'searchfilter';
	      break;
	    case 'download':  //task
	      $vars['task'] = 'download';
	      break;
            case 'downloadsize':  //task
	      $vars['task'] = 'downloadsize';
	      break;
	    case 'trialdropdown':  //task
	      $vars['task'] = 'trialdropdown';
	      $vars['projid'] = $segments[2];
	      $vars['expid'] = $segments[4];
	      break;
	    case 'repetitiondropdown':  //task
	      $vars['task'] = 'repetitiondropdown';
	      $vars['projid'] = $segments[2];
	      $vars['expid'] = $segments[4];
	      $vars['trialid'] = $segments[6];
	      break;      
	    case 'experiments':  //view
	      $vars['view'] = 'experiments';
	      $id = explode( ':', $segments[1] );
	      $vars['projid'] = (int) $id[0];
	      break;
	    case 'images':  //view
	      $vars['view'] = 'images';
	      $id = explode( ':', $segments[1] );
	      $vars['projid'] = (int) $id[0];
	      break;
            case 'filebrowser':  //view
	      $vars['view'] = $segments[0];
              $id = explode( ':', $segments[1] );
	      $vars['projid'] = (int) $id[0];
	      break;
	    case 'more':  //view
	      $vars['view'] = $segments[0];
              $id = explode( ':', $segments[1] );
	      $vars['projid'] = (int) $id[0];
	      break;
            case 'moredocs':  //view
	      $vars['view'] = $segments[0];
              $id = explode( ':', $segments[1] );
	      $vars['projid'] = (int) $id[0];
	      break;
            case 'moreanalysis':  //view
	      $vars['view'] = $segments[0];
              $id = explode( ':', $segments[1] );
	      $vars['projid'] = (int) $id[0];
	      break;
            case 'searchfiles':  //view
	      $vars['view'] = 'searchfiles';
	      break;
	    case 'experiment':  //view
	      $vars['view'] = 'experiment';
	      $id = explode( ':', $segments[1] );
	      $vars['id'] = (int) $id[0];
	      $vars['projid'] = (int) $segments[3]; 
	      break;
	    case 'trial':  //view
	      $vars['view'] = 'trial';
	      $id = explode( ':', $segments[1] );
	      $vars['id'] = (int) $id[0];
	      break;
	    case 'repetitions':  //view
	      $vars['view'] = 'repetitions';
	      $id = explode( ':', $segments[1] );
	      $vars['id'] = (int) $id[0];
	      break;
	    case 'data':  //view
	      $vars['view'] = 'data';
	      if(isset($segments[1])){
	        $id = explode( ':', $segments[1] );
	        $vars['projid'] = (int) $id[0];
	      }
	      if(isset($segments[2])){
	      	$vars['subtab'] = $segments[2];
	      }
	      break;  
              
            case 'datafiles':  //view
              $vars['view'] = 'datafiles';
              if(isset($segments[1])){
                $id = explode( ':', $segments[1] );
                $vars['id'] = (int) $id[0];
              }
              break;

	    case 'tools':  //view
	      $vars['view'] = 'tools';
	      if(isset($segments[1])){
	        $id = explode( ':', $segments[1] );
	        $vars['id'] = (int) $id[0];
	      }
	      break;  
	    case 'members':  //view
	      $vars['view'] = 'members';
	      $id = explode( ':', $segments[1] );
	      $vars['projid'] = (int) $id[0];
	      break;
	    case 'materials':  //view
	      $vars['view'] = 'materials';
	      $vars['projectId'] = (int) $segments[2];
	      $vars['experimentId'] = (int) $segments[4];
	      if(sizeof($segments)==7){
	      	$vars['materialId'] = (int) $segments[6];
	      }
	      break;
	    case 'drawings':  //view
	      $vars['view'] = 'drawings';
	      $vars['projectId'] = (int) $segments[2];
	      $vars['experimentId'] = (int) $segments[4];
	      break; 
	    case 'photos':  //view
	      $vars['view'] = 'photos';
	      $vars['projectId'] = (int) $segments[2];
	      $vars['experimentId'] = (int) $segments[4];
	      break;
           case 'projectphotos':  //view
	      $vars['view'] = 'projectphotos';
	      $vars['projectId'] = (int) $segments[2];
	      break;
            case 'publications':  //view
	      $vars['view'] = 'publications';
	      $vars['projectId'] = (int) $segments[2];
	      break;
	    case 'featured':  //view
	      $vars['view'] = 'featured';
	      break;
            case 'testme':  //view
	      $vars['view'] = 'testme';
	      break;
	    case 'search':  //view
	      $vars['view'] = 'search';
	      break;
            case 'advancedsearch':  //view
	      $vars['view'] = 'advancedsearch';
	      break;
	    case 'sensors':  //view
	      $vars['view'] = 'sensors';
	      $id = explode( ':', $segments[1] );
	      $vars['locationPlanId'] = (int) $id[0];
	      $vars['projectId'] = (int) $segments[3];
	      $vars['experimentId'] = (int) $segments[5];
	      break;  
	    case 'get':  //task
	      $vars['task'] = 'get';
	      
	      /*
	       * After the view, each segment element has 
	       * a piece of the absolute file path.  Put 
	       * the pieces together as a single string.
	       * Don't forget to lead the path off with 
	       * a leading "/".
	       */
	      $strPathArray = array();
	      foreach($segments as $iIndex=>$strSegment){
	      	if($iIndex > 0){
	      	  array_push($strPathArray, $strSegment);
	      	}
	      }
	      $vars['path'] = "/".implode("/",$strPathArray);
	      break;                   
	  }
  }  
    
  return $vars;
}


?>