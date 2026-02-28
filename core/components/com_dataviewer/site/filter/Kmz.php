<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site\Filter;

class Kmz
{
    public static function filter($res, &$dd)
    {
        $data = $res['data'];

        header('Content-Description: File Transfer');
        header('Content-Type: ' . 'application/vnd.google-earth.kmz');
        header('Content-Disposition: attachment; filename=' . preg_replace('/\W/', '_', $dd['title']) . '.kmz');

        $kml = '';
        $kml .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $kml .= '<kml xmlns="http://earth.google.com/kml/2.2">';
        $kml .= '<Document>';
        $kml .= '<name>' . $dd['title'] . '</name>';
        $kml .= '<description><![CDATA[' . $dd['title'] . ']]></description>';
        $kml .= '<Style id="style0">';
        $iconUrl = 'http://maps.gstatic.com/intl/en_us/mapfiles/ms/micons/blue-dot.png';
        $kml .= '<IconStyle><Icon><href>' . $iconUrl . '</href></Icon></IconStyle></Style>';

        if (!isset($dd['maps'])) {
            return;
        }

        foreach ($data as $rec) {
            if ($rec[$dd['maps'][0]['lat']] == null || $rec[$dd['maps'][0]['lng']] == null) {
                continue;
            }

            $lat = $rec[$dd['maps'][0]['lat']];
            $lng = $rec[$dd['maps'][0]['lng']];
            $cood = '';
            if (!isset($dd['maps'][0]['cood_type']) || $dd['maps'][0]['cood_type'] != 'dms') {
                $cood = "$lng,$lat,0.000000";
            } else {
                $cood = self::dms2dc($lng) . ',' . self::dms2dc($lat) . ',0.000000';
            }

            $pm = '<Placemark>';
            $pm .= '<name>' . $rec[$dd['maps'][0]['title']] . '</name>';
            $pm .= '<description><![CDATA[<div dir="ltr">' . $rec[$dd['maps'][0]['title']];
            if (isset($dd['maps'][0]['info'])) {
                $info_str = $dd['maps'][0]['info'];
                foreach ($rec as $key => $val) {
                    $info_str = str_replace('{' . $key . '}', $rec[$key], $info_str);
                    $info_str = str_replace('{' . $key . '|html}', $rec[$key], $info_str);
                }
                $pm .= "<br />$info_str";
            }
            $pm .= '</div>]]></description>';
            $pm .= '<styleUrl>#style0</styleUrl>';
            $pm .= '<Point>';
            $pm .= '<coordinates>' . $cood . '</coordinates>';
            $pm .= '</Point>';
            $pm .= '</Placemark>';

            $kml .= $pm;
        }

        $kml .= '</Document>';
        $kml .= '</kml>';

        $tmp = tempnam("/tmp", "kmz");
        $z = new \ZipArchive();
        $z->open($tmp, \ZIPARCHIVE::OVERWRITE);
        $z->addFromString('doc.kml', $kml);
        $z->close();
        ob_end_flush();
        readfile($tmp);
        unlink($tmp);
        exit();
    }

    public static function dms2dc($cood)
    {
        $cood = explode('° ', $cood);
        $d = $cood[0];
        $cood = explode('\' ', $cood[1]);
        $m = $cood[0];
        $cood = explode('" ', $cood[1]);
        $s = $cood[0];
        $dir = $cood[1];

        $dc = $d + ($m / 60) + ($s / (60 * 60));

        if ($dir == "S" || $dir == "W") {
            $dc = $dc * -1;
        }
        return $dc;
    }
}
