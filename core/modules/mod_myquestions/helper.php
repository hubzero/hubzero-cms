<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\MyQuestions;

use Hubzero\Module\Module;
use Hubzero\Facades\Component;
use Hubzero\Facades\Route;
use Hubzero\Facades\Lang;
use Hubzero\Facades\User;

/**
 * Module class for displaying a user's questions
 * Requires com_answers component
 */
class Helper extends Module
{
    protected $assigned;
    protected $banking;
    protected $interests;
    protected $intext;
    protected $limit_assigned;
    protected $limit_interest;
    protected $limit_mine;
    protected $openquestions;
    protected $otherquestions;
    protected $show_assigned;
    protected $show_interests;

    /**
     * Format the tags
     *
     * @param   string   $string  String of comma-separated tags
     * @param   number   $num     Number of tags to display
     * @param   integer  $max     Max character length
     * @return  string   HTML
     */
    private function formatTags($string = '', $num = 3, $max = 25)
    {
        $out = '';
        $tags = explode(',', $string);

        if (count($tags) > 0) {
            $out .= '<span class="taggi">' . "\n";
            $counter = 0;

            for ($i = 0; $i < count($tags); $i++) {
                $counter = $counter + strlen(stripslashes($tags[$i]));
                if ($counter > $max) {
                    $num = $num - 1;
                }
                if ($i < $num) {
                    // display tag
                    $normalized = preg_replace("/[^a-zA-Z0-9]/", '', $tags[$i]);
                    $normalized = strtolower($normalized);
                    $tagUrl = Route::url('index.php?option=com_tags&tag=' . $normalized);
                    $out .= "\t" . '<a href="' . $tagUrl . '">'
                        . stripslashes($tags[$i]) . '</a> ' . "\n";
                }
            }
            if ($i > $num) {
                $out .= ' (&#8230;)';
            }
            $out .= '</span>' . "\n";
        }

        return $out;
    }

    /**
     * Looks up a user's interests (tags)
     *
     * @param   integer  $cloud  Output as tagcloud (defaults to no)
     * @return  string   List of tags as either a tagcloud or comma-delimitated string
     */
    private function getInterests($cloud = 0)
    {
        require_once Component::path('com_members') . '/models/tags.php';

        // Get tags of interest
        $mt = new \Components\Members\Models\Tags(User::get('id'));
        if ($cloud) {
            $tags = $mt->render();
        } else {
            $tags = $mt->render('string');
        }

        return $tags;
    }

    /**
     * Retrieves a user's questions
     *
     * @param   string  $kind       The kind of results to retrieve
     * @param   array   $interests  Array of tags
     * @return  array   Database results
     */
    private function getQuestions($kind = 'open', $interests = array())
    {
        // Get some classes we need
        require_once Component::path('com_answers') . '/models/question.php';
        require_once Component::path('com_answers') . '/helpers/economy.php';

        $limit = intval($this->params->get('limit', 10));
        $tags  = null;
        if (is_string($interests)) {
            $interests = explode(',', $interests);
        }
        if (!is_array($interests)) {
            $interests = array();
        }

        if (is_string($interests)) {
                $interests = explode(',', $interests);
        }
        if (!is_array($interests)) {
                $interests = array();
        }
        $records = \Components\Answers\Models\Question::all()
            ->including(['responses', function ($response) {
                $response
                    ->select('id')
                    ->select('question_id');
            }])
            ->whereEquals('state', 0);

        if ($kind == 'mine') {
            $records->whereEquals('created_by', User::get('id'));
        }

        if ($kind == 'interest') {
            $tags = (!is_array($interests) || count($interests) <= 0) ? $this->getInterests() : $interests;
        }

        if ($kind == 'assigned') {
            require_once Component::path('com_tools') . '/tables/author.php';

            $database = \Hubzero\Facades\App::get('db');

            $TA = new \Components\Tools\Tables\Author($database);
            $tools = $TA->getToolContributions(User::get('id'));
            if ($tools) {
                foreach ($tools as $tool) {
                    $tags .= 'tool' . $tool->toolname . ',';
                }
                $tags = rtrim($tags, ',');
            }
        }

        if ($tags) {
            $cloud = new \Components\Answers\Models\Tags();
            $tags = $cloud->parse($tags);

            $records
                ->select('#__answers_questions.*')
                ->join('#__tags_object', '#__tags_object.objectid', '#__answers_questions.id')
                ->join('#__tags', '#__tags.id', '#__tags_object.tagid')
                ->whereEquals('#__tags_object.tbl', 'answers')
                ->whereIn('#__tags.tag', $tags);
        }

        $data = $records
            ->limit($limit)
            ->ordered()
            ->rows();

        $results = array();
        foreach ($data as $datum) {
            $datum->set('rcount', $datum->responses->count());
            $results[] = $datum;
        }

        if ($this->banking && $results) {
            $database = \Hubzero\Facades\App::get('db');

            $AE = new \Components\Answers\Helpers\Economy($database);

            $awards = array();

            foreach ($results as $result) {
                // Calculate max award
                $result->set('marketvalue', round($AE->calculate_marketvalue($result->get('id'), 'maxaward')));
                $result->set('maxaward', round(2 * ($result->get('marketvalue', 0) / 3)));
                if ($kind != 'mine') {
                    $result->set('maxaward', $result->get('maxaward') + $result->get('reward'));
                }
                $awards[] = $result->get('maxaward', 0);
            }

            // re-sort by max reponses
            array_multisort($awards, SORT_DESC, $results);
        }

        return $results;
    }

    /**
     * Queries the database for user's questions and preps any data for display
     *
     * @return  void
     */
    public function display()
    {
        $this->banking = Component::params('com_members')->get('bankAccounts');

        // show assigned?
        $show_assigned = intval($this->params->get('show_assigned'));
        $show_assigned = $show_assigned ? $show_assigned : 0;
        $this->show_assigned = $show_assigned;

        // show interests?
        $show_interests = intval($this->params->get('show_interests'));
        $show_interests = $show_interests ? $show_interests : 0;
        $this->show_interests = $show_interests;

        // max num of questions
        $max = intval($this->params->get('max_questions'));
        $max = $max ? $max : 12;
        $c = 1;

        // Build the HTML
        //$foundresults = false;
        $assignedcount = 0;
        $othercount = 0;

        // Get Open Questions User Asked
        $this->openquestions = $this->getQuestions('mine');
        $opencount = ($this->openquestions) ? count($this->openquestions) : 0;

        // Get Questions related to user contributions
        if ($this->show_assigned) {
            $c++;
            $this->assigned = $this->getQuestions('assigned');
            $assignedcount = ($this->assigned) ? count($this->assigned) : 0;
        }

        // Get interest tags
        if ($this->show_interests) {
            $c++;
            $this->interests = $this->getInterests();
            if (!$this->interests) {
                $this->intext = Lang::txt('MOD_MYQUESTIONS_NA');
            } else {
                $this->intext = $this->formatTags($this->interests);
            }

            // Get questions of interest
            $this->otherquestions = $this->getQuestions("interest", $this->interests);
            $othercount = ($this->otherquestions) ? count($this->otherquestions) : 0;
        }

        // Limit number of shown questions
        $totalq = $opencount + $assignedcount + $othercount;
        $limit_mine = $max;
        $breaker = $max / $c;
        $breakerThreshold = $breaker * ($c - 1);
        $this->limit_mine = ($totalq - $opencount) >= $breakerThreshold
            ? $breaker
            : $max - ($totalq - $opencount);
        $this->limit_assigned = ($totalq - $assignedcount) >= $breakerThreshold
            ? $breaker
            : $max - ($totalq - $assignedcount);
        $this->limit_interest = ($totalq - $othercount) >= $breakerThreshold
            ? $breaker
            : $max - ($totalq - $othercount);

        require $this->getLayoutPath();
    }
}
