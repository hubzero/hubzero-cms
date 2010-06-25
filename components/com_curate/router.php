<?php 

function CurateBuildRoute( &$query ){
  $segments = array();
  if(isset($query['view'])){
    $segments[] = $query['view'];
    unset( $query['view'] );
  }
       
  if(isset($query['projid'])){
    $segments[] = $query['projid'];
    unset( $query['projid'] );
  };
  
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

function CurateParseRoute( $segments ){
  $vars = array();
  switch($segments[0]){
    case 'projects':
      $vars['view'] = 'projects';
      $id = explode( ':', $segments[1] );
      $vars['index'] = (int) $segments[2];
      $vars['limit'] = (int) $segments[4];
      break;
    case 'project':
      $vars['view'] = 'project';
      $id = explode( ':', $segments[1] );
      $vars['projid'] = (int) $id[0];
      break;
    case 'experiment':
      $vars['view'] = 'experiment';
      $id = explode( ':', $segments[1] );
      $vars['expid'] = (int) $id[0];
      $vars['projectId'] = (int) $segments[3];
      break;  
  }
  return $vars;
}


?>