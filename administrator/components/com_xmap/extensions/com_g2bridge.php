<?php 
/**
* $Id: com_g2bridge.php 52 2009-10-24 22:35:11Z guilleva $
* @author Guillermo Vargas, http://joomla.vargas.co.cr
* @email guille@vargas.co.cr
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for Gallery2 Brige component
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class xmap_com_g2bridge
{
   /*
   * This function is called before a menu item is printed. We use it to set the
   * proper uniqueid for the item
   */
   function prepareMenuItem(&$node)
   {
       $menu =& JSite::getMenu();
       $g2params = $menu->getParams($node->id);

       $rootAlbum = $g2params->get("alb_id", -1);

       if($rootAlbum != -1) {
            $node->uid = 'com_g2bridgea'.$rootAlbum;
            $node->expandible=true;
       }
   }

   function getTree( &$xmap, &$parent, $params )
   {
      if ( !file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_g2bridge'.DS.'helpers'.DS.'g2bridgecore.class.php') ) {
          return false;
      }
      require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_g2bridge'.DS.'helpers'.DS.'g2bridgecore.class.php' );

      $link_query = parse_url( $parent->link );
      parse_str( html_entity_decode($link_query['query']), $link_vars);
      $view = xmap_com_g2bridge::getParam($link_vars,'view','gallery');
      $rootAlbum = xmap_com_g2bridge::getParam($link_vars,'g2_itemId',-1);


      if(!G2BridgeCore::loadSettings())
      {
           return false;
      }
      global $gallery;

      $urlGenerator =& $gallery->getUrlGenerator();

      $ret = GalleryInitSecondPass();
      if ($ret) {
          return false;
      }

      $menu =& JSite::getMenu();
      $g2params = $menu->getParams($parent->id);

      if ( $rootAlbum == -1 ) { // If the album id is not in the url of the parent item, then look at the menu params
          // ItemID of the root album
          $rootAlbum = $g2params->get("alb_id", -1);
      }
      if($rootAlbum != -1)
          $rootId = $rootAlbum;
      else 
          $rootId = 7;

      // Fetch all items contained in the root album
      list ($ret, $rootItems) = 
         GalleryCoreApi::fetchChildItemIdsWithPermission($rootId, 'core.view');

      if ( $ret )
         return null;

      //  init params
      $include_items = xmap_com_g2bridge::getParam($params,'include_items',1);
      $include_items = ( $include_items == 1
                                  || ( $include_items == 2 && $xmap->view == 'xml')
                                  || ( $include_items == 3 && $xmap->view == 'html')
								  			||   $xmap->view == 'navigator');
      $params['include_items'] = $include_items;

      $priority = xmap_com_g2bridge::getParam($params,'cat_priority',$parent->priority);
      $changefreq = xmap_com_g2bridge::getParam($params,'cat_changefreq',$parent->changefreq);
      if ($priority  == '-1')
          $priority = $parent->priority;
      if ($changefreq  == '-1')
          $changefreq = $parent->changefreq;

      $params['cat_priority'] = $priority;
      $params['cat_changefreq'] = $changefreq;

      $priority = xmap_com_g2bridge::getParam($params,'item_priority',$parent->priority);
      $changefreq = xmap_com_g2bridge::getParam($params,'item_changefreq',$parent->changefreq);
      if ($priority  == '-1')
          $priority = $parent->priority;
      if ($changefreq  == '-1')
          $changefreq = $parent->changefreq;

      $params['item_priority'] = $priority;
      $params['item_changefreq'] = $changefreq;

      // Recurse through the whole album tree
      xmap_com_g2bridge::getG2Tree( $xmap,$parent,$params,$rootItems,$urlGenerator );
   }


   function getG2Tree( &$xmap,&$parent,$params,&$items,&$urlGenerator )
   {
      if( !$items )
         return null;

      $xmap->changeLevel(1);
      $media = array();
      foreach( $items as $itemId ) {

         // Fetch the details for this item
         list ($ret, $entity) = GalleryCoreApi::loadEntitiesById($itemId);

         if ( $ret ){
            // error, skip and continue, catch this error in next component version
            continue;
         }

         $node = new stdClass();
         $node->id    = $entity->getId();
         $node->uid   = $parent->uid.'a'.$entity->getId();
         $node->name  = $entity->getTitle();
         $node->pid   = $entity->getParentId();
         $node->modified = $entity->getModificationTimestamp();
         $node->link = $urlGenerator->generateUrl (
               array('view' => 'core.ShowItem', 'itemId' => $node->id),
               array('forceSessionId' => false, 'forceFullUrl' => false)
         );

	 // Fix for the navigator view
         if ( $xmap->view == 'navigator' ) {
               $node->link = str_replace('/administrator/index.php','',$node->link);
         }

         // If it is an album
         if ( $entity->getCanContainChildren() ) {
            $node->priority = $params['cat_priority'];
            $node->changefreq = $params['cat_changefreq'];
				$node->expandible=true;
            // Get all child items contained in this album and add them to the tree
            list ($ret, $childIds) =
               GalleryCoreApi::fetchChildItemIdsWithPermission($node->id, 'core.view');

            if ($ret) {
               // error, skip and continue, catch this error in next component version
               continue;   
            }

            if ($xmap->printNode($node) !== false) {
                xmap_com_g2bridge::getG2Tree( $xmap,$parent,$params,$childIds,$urlGenerator );
            }
         } elseif ($params['include_items']) {
            $node->priority = $params['item_priority'];
            $node->changefreq = $params['item_changefreq'];
            $node->uid = $parent->uid.'p'.$entity->getId();
            $node->expandible=false;
            $media[] = $node;
         }
      }

      foreach ($media as $pic ) {
          $xmap->printNode($pic);
      }
      $xmap->changeLevel(-1);

   }

   function getParam($arr, $name, $def)
   {
        return JArrayHelper::getValue( $arr, $name, $def, '' );
   }
}
