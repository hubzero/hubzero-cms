<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Redirect\Site\Controllers;

use Hubzero\Component\SiteController;


use Component;
use Exception;
use Document;
use Pathway;
use Request;
use Plugin;
use Notify;
use Route;
use Event;
use Lang;
use User;
use App;

/**
 * Primary component controller
 */
class Redirect extends SiteController
{
    private function extractBaseDomain($host)
    {
        $host = preg_replace('/^www\./', '', $host);
        if (strpos($host, "proxy") !== false){
            return "Proxy Service";
        } else if (preg_match('/([a-z0-9\-]+)\.(?:[a-z]{2,63})$/i', $host, $matches)) {
            return $matches[1]; // e.g., google.com → google
        } else if (preg_match('/([a-z0-9\-]+)\.[a-z0-9\-]+\.[a-z]{2,63}$/i', $host, $matches)) {
            return $matches[1]; // e.g., yahoo.co.uk → yahoo
        }
        return $host;
    }

    public function displayTask()
    {   
        $url =  Request::getString("id", ""); //base64_encode('https://www.google.com/')

        $params = Component::params('com_redirect');
        $whitelist = isset($params["delay_whitelist"]) ? explode(",", $params["delay_whitelist"]) : array();
        $blacklist = isset($params["delay_blacklist"]) ? explode(",", $params["delay_blacklist"]) : array();
        $enabled = isset($params["delay_enabled"]) ? $params["delay_enabled"] : "DISABLED";
        $time  = isset($params["delay_seconds"]) ? $params["delay_seconds"] : 10;
        $url = base64_decode($url, true); 
        if ($url === false) {
            App::redirect("https://" . $_SERVER['HTTP_HOST']);
        }
        if ($enabled == "DISABLED"){
            App::redirect($url);
            return;
        }
        $host = parse_url($url);            

        if (!$host || !isset($host['host'])){
            if (!empty($_SERVER['HTTP_REFERER'])) {
                $referrer = $_SERVER['HTTP_REFERER'];
                $refParsed = parse_url($referrer);
                $host = isset($refParsed['host']) ? $refParsed['host'] : '';
                $path = isset($refParsed['path']) ? $refParsed['path'] : '';
                $url = 'https://' . $host . "/". $path . $url;
            } else if (!empty($_SERVER['HTTP_HOST'])){
                $url = 'https://' . $_SERVER['HTTP_HOST'] . $url;
            } else {
                $url = 'https://' . $url;
            }
            App::redirect($url);
        } else if (strpos($host['host'], $_SERVER['HTTP_HOST']) !== false && strpos($host['host'], "proxy") === false ) {
            App::redirect($url);
        } else if (in_array($host['host'], $whitelist) !== false) {
            App::redirect($url);
        } else if (in_array($host['host'], $blacklist) !== false) {
            App::redirect('https://' . $_SERVER['HTTP_HOST']);
        } else {
            if ($params) {
                if (isset($params["delay_enabled"]) && $params["delay_enabled"] == "ENABLED" && isset($params["delay_seconds"])) {
                    $time = intval($params["delay_seconds"]);
                }
            }
            $this->view->domain = $this->extractBaseDomain($host['host']);
            $this->view->url = $url;
            $this->view->time = $time;
            $this->view
                ->set('option', $this->_option)
                ->set('task', $this->task)
                ->set('config', $this->config)
                ->setName('display')
                ->setLayout('display')
                ->display();
        }
    }
}


