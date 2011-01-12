<?php

class plgYSearchExpand extends YSearchPlugin
{
        public static function onYSearchExpandTerms(&$terms)
        {
                $map = array();
        	foreach ($terms as $term)
			$map[$term] = 1;
		if (isset($map['earth']) && isset($map['quake']) && !isset($map['earthquake']))
			$terms[] = 'earthquake';
		if (isset($map['earthquake']) && !isset($map['quake']))
			$terms[] = 'quake';
	}
}

