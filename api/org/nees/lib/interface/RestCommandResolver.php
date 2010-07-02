<?php
require_once 'lib/util/PeerMap.php';
require_once "lib/filesystem/FileCommandAPI.php";

class RestCommandResolver {
  // Classes that have access control rights associated with them,
  // in order of preference for permissions checking.
  private static $accessControlledClasses = array('Experiment', 'Project', 'Organization', 'Facility', 'SensorModel');

  // Harder to explain, these classes can only be created/edited/deleted if the user has access to create/edit/delete ANY Organization.
  private static $organizationAccessClasses = array('SensorModel', 'SensorManifest');

  // These are all valid root classes.
  private static $rootClasses = array('Project', 'Material', 'MaterialType', 'SensorManifest', 'CoordinateDimension', 'CoordinateSystem', 'MeasurementUnitConversion', 'EquipmentClass', 'Organization', 'Facility', 'Attribute', 'ExperimentDomain', 'DocumentFormat', 'MeasurementUnitCategory', 'SimilitudeLawGroup', 'SensorModel', 'SensorType','Acknowledgement');
  function __construct() {
  }

  public function getTarget($URI, $method) {
    // Parse the root API path out of the uri.
    $uri = preg_replace('@/REST/@', '', $URI);

    $tokens = preg_split('@/@',$uri, -1, PREG_SPLIT_NO_EMPTY);
    // Quick sanity check.
    // POST requires an odd number of args,
    // and DELETE/PUT require an even number.
    $method = strtoupper($method);
    if( $tokens[0] != 'File' ) {
      if( count($tokens) % 2 ) {
        if( in_array($method, array('DELETE', 'PUT') ) ) {
          return false;
        }
      }
      else if( $method == 'POST' ) {
        return false;
      }
    }
    return RestCommandResolver::parse_uri($method, null, $tokens, 0);
  }

  public function getObjFromURI($URI) {
    $URL = parse_url($URI);
    $uri = $URL['path'];
    // Parse the root API path out of the uri.
    $uri = preg_replace('@/REST/@', '', $uri);

    $tokens = preg_split('@/@',$uri, -1, PREG_SPLIT_NO_EMPTY);

    return RestCommandResolver::parse_uri('GET', null, $tokens, 0);
  }

  function hasAccess($uri, $cmd) {
    $perm = '';
    switch( $cmd ) {
      case 'retrieve':
        $perm = 'canView';
        break;
      case 'create':
        $perm = 'canCreate';
        break;
      case 'update':
        $perm = 'canEdit';
        break;
      case 'delete':
        $perm = 'canDelete';
        break;
    }

    $authorizer = Authorizer::getInstance();
    $userManager = UserManager::getInstance();

    // File access control.
    if( preg_match('@^/REST/File/@', $uri) ) {
      $path = preg_replace('@^/REST/File/@', '', $uri);
      $path = preg_replace('@/content$@', '', $path);
      $path = FileCommandAPI::set_directory($path);
      $public = DataFile::isInPublicDir($path);
      $entity = DataFile::getOwner($path);

      // All Organization and Facility files are world-readable.
      if( $perm == 'canView' ) {
        if( get_class($entity) == 'Organization' || get_class($entity) == 'Facility' ) {
          return true;
        }
      }

      if(($authorizer->$perm($entity) || ($public))) {
        return true;
      }
      return false;
    }

    // Figure out whether we've got an access controlled DO
    // mentioned in our URI.
    foreach( RestCommandResolver::$accessControlledClasses as $accClass ) {
      if( preg_match("@/$accClass/\d+@", $uri) ) {
        // SPECIAL CASE: All Facility and Organization and SensorModel level items are wold-readable.
        if( $perm == 'canView' ) {
          if( $accClass == 'Facility' || $accClass == 'Organization' || $accClass == 'SensorModel') {
            return true;
          }
        }
        $id = preg_replace("@.*/$accClass/(\d+).*@", '$1', $uri);

        $do = PeerMap::getPeer($accClass)->find($id);

        if( !$do ) {
          return false;
        }

        // SPECIAL CASE: SensorModel permissions are based on Facility permissions
        if ( $accClass == 'SensorModel' )
          return SensorModel::getPermission($perm);

        return $authorizer->$perm($do);
      }
    }

    // If they didn't mention an access controlled obj in the
    // URI, they're probably trying to make a new root object?
    // Allow them to make new projects, at least.
    if( $perm != 'canView' ) {
      // SPECIAL CASE for items that can be edited by people with org access.
      foreach( RestCommandResolver::$organizationAccessClasses as $orgclass ) {
        if( preg_match("@^/REST/$orgclass/@", $uri) || preg_match("@^/REST/$orgclass$@", $uri) ) {
          $orgs = OrganizationPeer::findAll();
          foreach( $orgs as $org ) {
            if( $authorizer->$perm($org) ) {
              return true;
            }
          }
        }
      }
      if( !preg_match("@^/REST/Project/?$@", $uri) )  {

        //  Or they're trying to make a new SensorModel
        if( !preg_match("@^/REST/SensorModel/?$@", $uri) )  {
          return false;
        }else{
          return SensorModel::getPermission($perm);
        }

      }
    }
    return true;
  }

  function getFile($path) {
    $path = preg_replace('@/content$@', '', $path);
    return DataFilePeer::findByFullPath($path);
  }

  function parse_file_uri($method, $tokens) {
    $path = join("/", array_slice($tokens, 1));
    $path = FileCommandAPI::set_directory($path);

    $file = RestCommandResolver::getFile($path);
    // /path/to/file.txt/content refers to file content
    // rather than file metadata. so handle stripping off
    // /content if necessary.
    if( !$file ) {
      $path = preg_replace('@/content$@', '', $path);
      $file = RestCommandResolver::getFile($path);
    }

    // If we're supposed to be creating a new file,
    // go ahead and return an empty DataFile.
    if( !$file && $method == 'POST' ) {
      $file = new DataFile(basename($path), dirname($path));
      //$file->setName(basename($path));
      //$file->setPath(dirname($path));
    }
    return $file;
  }

  // Jess' version of walking the DO tree...
  function parse_uri($method, $parent, $tokens, $idx) {
    // File URI's need to be handled differently.
    if( $tokens[0] == 'File' ) {
      return RestCommandResolver::parse_file_uri($method, $tokens);
    }

    // Figure out what kind of object we're looking for.
    // For now, API params must be in the same case as the DO class names.
    $objtype = $tokens[$idx];
    $authorizer = Authorizer::getInstance();

    // Figure out how to get our parent...
    if( $parent ) {
      $getparent = 'get' . get_class($parent);
      $setparent = 'set' . get_class($parent);
    }

    // If this is the end of the URI and we don't
    // have an ID, we'll return a collection, filtered
    // for the parent object.
    if (!$tokens[$idx + 1]) {
      if( $method == 'GET' ) {
        if( $parent ) {
          $finder = 'findBy' . get_class($parent);
          $coll = PeerMap::getPeer($objtype)->$finder($parent);
        }
        else {
          // Likely not ideal, but works for the moment.
          if( in_array($objtype, RestCommandResolver::$rootClasses) ) {
            if( in_array($objtype, RestCommandResolver::$accessControlledClasses) && $objtype != 'Organization' && $objtype != 'Facility' && $objtype != 'SensorModel') {
              // TODO: Need to make sure all root-level peer have findByPerson.
              $coll = PeerMap::getPeer($objtype)->findByPerson($authorizer->getUser());
              // Use authorizer to make sure the user has view access for these objects.
              $remove = array();
              foreach( $coll as $tmp ) {
                if( !$authorizer->canView($tmp) ) {
                  $remove[] = $tmp;
                }
              }
              foreach( $remove as $item ) {
                // @todo Minh, you need to modify this line below
                $coll->remove($item);
              }
              //$coll->rewind();
            }
            else {
              // If this isn't an access controlled class, just get em all.
              $coll = PeerMap::getPeer($objtype)->findAll();
            }
          }
        }

        return $coll;
      }
      // If this is a POST rather than a GET,
      // return a new, empty domain object rather
      // than a collection.
      else {
        // Getting the peer is just a quick trick
        // for including the DO's class files.
        //$mapper = PeerMap::getPeer($objtype);
        $obj = new $objtype();
        // Set our obj's parent before sending it
        // along to be hacked up.
        if( $parent ) {
          $obj->$setparent($parent);
        }
        return $obj;
      }
    }

    // Don't let the user get access to all objects at the root level!
    if( !$parent && !in_array($objtype, RestCommandResolver::$rootClasses) ) {
      return false;
    }

    // Ok, we're looking for a specific object, rather than
    // a collection.  Go hunting.
    $id = RestCommandResolver::parse_index($tokens[$idx + 1]);
    $obj = PeerMap::getPeer($objtype)->find($id);
    if( !$obj ) {
      // Hmm.  that object doesn't exist.
      return false;
    }
    // Make sure this object is a child of the parent...
    if( $parent && $obj->$getparent()->getId() != $parent->getId() ) {
      return false;
    }

    if( $tokens[$idx + 2] ) {
      return RestCommandResolver::parse_uri($method, $obj, $tokens, $idx + 2);
    }
    return $obj;
  }

  public function getCommand($method) {
    switch ( $method ) {
      case 'GET':
        return 'retrieve';
        break;
      case 'POST':
        return 'create';
        break;
      case 'PUT':
        return 'update';
        break;
      case 'DELETE':
        return 'delete';
        break;
    }
  }

  function parse_index ($string) {
    // assert $token is integer >= 0
    if( preg_match('/^\d+$/', $string ) ) {
      return $string;
    }
    return 0;
  }

}


?>
