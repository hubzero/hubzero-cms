<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers;

use Hubzero\Base\Obj;
use Hubzero\Utility\Str;
use Hubzero\Facades\App;

/**
 */
class Document extends Obj
{
    /**
     * Document
     *
     * @var  string|null  The page source to parse
     */
    public $document     = null;

    /**
     * Group
     *
     * @var  \Hubzero\User\Group
     */
    public $group;

    /**
     * Content
     *
     * @var  string|null  What parsing and rendering produced
     */
    public $content      = null;

    /**
     * Page
     *
     * @var  \Components\Groups\Models\Page|null
     */
    public $page         = null;

    /**
     * Tab
     *
     * @var  string|null
     */
    public $tab          = null;

    /**
     * Allowed tags
     *
     * @var  array<mixed>
     */
    public $allowed_tags = ['module','modules','stylesheet','script'];

    /**
     * Tags
     *
     * @var  array<mixed>
     */
    private $tags       = [];

    /**
     * Parse Document Content
     * @return  $this
     */
    public function parse()
    {
        // check to make sure we have content
        if (!$this->get('document')) {
            App::abort(406, '\Components\Groups\Helpers\Document: Requires document to parse');
        }

        // parse content
        // get all group includes
        if (preg_match_all('#<group:include([^>]*)/>#', $this->get('document'), $matches)) {
            // get number of matches
            $count = count($matches[1]);

            //loop through each match
            for ($i = 0; $i < $count; $i++) {
                $attribs = Str::parseAttributes($matches[1][$i]);

                $type   = (isset($attribs['type'])) ? strtolower(trim($attribs['type'])) : null;
                $name   = (isset($attribs['name'])) ? $attribs['name'] : $type;

                unset($attribs['type']);
                $params = $attribs;

                $this->tags[$matches[0][$i]] = [
                    'type'   => $type,
                    'name'   => $name,
                    'params' => $params,
                ];
            }
        }

        // return this
        return $this;
    }

    /**
     * Render Document
     *
     * @return  string
     */
    public function render()
    {
        // vars to hold replace values
        $replace = [];
        $with    = [];

        // loop through all includes and render
        foreach ($this->tags as $tag => $props) {
            $replace[] = $tag;
            $with[]    = $this->getBuffer($tag, $props['type'], $props['name'], $props['params']);
        }

        // replace group includes
        $this->set('document', str_replace($replace, $with, $this->get('document')));

        // reset tags
        $this->tags = [];

        //return this for chainability
        return $this;
    }

    /**
     * Return or echo Content
     *
     * @param   boolean  $echo  Echo document or return?
     * @return  mixed
     */
    public function output($echo = false)
    {
        if ($echo) {
            echo $this->get('document');
        } else {
            return $this->get('document');
        }
    }

    /**
     * Get Template Buffer
     *
     * @param   int|string  $tag
     * @param   mixed  $type
     * @param   mixed  $name
     * @param   mixed  $params
     * @return  string|null
     */
    private function getBuffer($tag, $type = null, $name = null, $params = [])
    {
        // make sure we have type
        // make sure this type is in allowed tags
        if ($type === null || !in_array($type, $this->get('allowed_tags'))) {
            return "<!-- [[{$tag}]]: Include Invalid or Not Allowed Here -->";
        }

        // class name for renderer
        $renderClass = '\\Components\\Groups\\Helpers\\Document\\Renderer\\'
            . ucfirst($type);

        // if we dont have a class return null
        if (!is_a($renderClass, Document\Renderer::class, true)) {
            return null;
        }

        // instantiate renderer and call render
        $renderer = new $renderClass();
        $renderer->group   = $this->get('group');
        $renderer->page    = $this->get('page');
        $renderer->content = $this->get('content');
        $renderer->allMods = $this->get('allMods', 0);
        $renderer->name    = $name;
        $renderer->params  = (object) $params;
        return $renderer->render();
    }
}
