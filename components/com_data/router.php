<?php 

function DataBuildRoute( &$query ){
  $segments = array();
  if(isset($query['view'])){
    $segments[] = $query['view'];
    unset( $query['view'] );
  }
       
  if(isset($query['index'])){
    $segments[] = $query['index'];
    unset( $query['index'] );
  };
  
  if(isset($query['limit'])){
    $segments[] = $query['limit'];
    unset( $query['limit'] );
  };
  return $segments;
}

function DataParseRoute( $segments ){
  $vars = array();
  //print_r($segments);
  switch($segments[0]){
    case 'get':
      $vars['task'] = array_shift($segments);
      $vars['path'] = implode("/", $segments);
      break;
    case 'show':
      $vars['task'] = array_shift($segments);
      $vars['path'] = implode("/", $segments);
      break;  
    /*
    case 'put':
      $vars['view'] = 'project';
      $id = explode( ':', $segments[1] );
      $vars['projid'] = (int) $id[0];
      break;
    case 'view':
      $vars['view'] = 'experiment';
      $id = explode( ':', $segments[1] );
      $vars['expid'] = (int) $id[0];
      $vars['projectId'] = (int) $segments[3];
      break;  
    */
  }
  return $vars;
}


?>