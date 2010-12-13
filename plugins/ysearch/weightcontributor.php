<?php

class plgYSearchWeightContributor
{
  public static function onYSearchWeightAll($terms, $res)
  {
    $pos_terms = $terms->get_positive_chunks(); 

    foreach (array_map('strtolower', $res->get_contributors()) as $contributor)
      foreach ($pos_terms as $term)
        if (strpos($contributor, $term) !== false)
          return 1.0;
    return 0.5;
  }
}
