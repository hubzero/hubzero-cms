<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css();

// Build the HTML
$html  = '';
$draftUrl = Route::url(
    'index.php?option=com_resources&task=draft'
);
$html .= "\t\t" . '<ul class="module-nav"><li>'
    . '<a class="icon-plus" href="' . $draftUrl . '">'
    . Lang::txt('MOD_MYCONTRIBUTIONS_START_NEW')
    . '</a></li></ul>' . "\n";

$tools = $this->tools;
if ($this->show_tools && $tools) {
    $html .= '<h4>';

    $pipelineUrl = Route::url(
        'index.php?option=com_tools'
        . '&controller=pipeline&task=pipeline'
    );
    $html .= '<a href="' . $pipelineUrl . '">'
        . Lang::txt('MOD_MYCONTRIBUTIONS_TOOLS') . ' ';
    if (count($tools) > $this->limit_tools) {
        $html .= '<span>'
            . Lang::txt('MOD_MYCONTRIBUTIONS_VIEW_ALL')
            . ' ' . count($tools) . '</span>';
    }
    $html .= '</a></h4>' . "\n";

    $html .= '<ul class="expandedlist">' . "\n";
    for ($i = 0; $i < count($tools); $i++) {
        if ($i <= $this->limit_tools) {
            $class = $tools[$i]->published
                ? 'published' : 'draft';
            $toolState = $this->getState($tools[$i]->state);
            $urgency = (
                $toolState == 'installed'
                || $toolState == 'created'
            )
                ? ' ' . Lang::txt(
                    'MOD_MYCONTRIBUTIONS_ACTION_REQUIRED'
                )
                : '';

            $statusUrl = Route::url(
                'index.php?option=com_tools'
                . '&controller=pipeline&task=status&app='
                . $tools[$i]->toolname
            );
            $html .= '<li class="' . $class . '">' . "\n";
            $html .= '<a href="' . $statusUrl . '">'
                . stripslashes($tools[$i]->toolname)
                . '</a>' . "\n";
            $statusTitle = Lang::txt(
                'MOD_MYCONTRIBUTIONS_TOOL_STATUS',
                $toolState,
                $urgency
            );
            $html .= '<span class="under">'
                . Lang::txt('MOD_MYCONTRIBUTIONS_STATUS')
                . ': <span class="status_' . $toolState
                . '"><a href="' . $statusUrl
                . '" title="' . $statusTitle . '">'
                . $toolState . '</a></span>' . "\n";
            if ($tools[$i]->published) {
                $html .= '<span class="extra">' . "\n";
                $html .= (!$this->show_wishes)
                    ? '<span class="item_empty ">&nbsp;</span>'
                    : '';
                $html .= (!$this->show_tickets)
                    ? '<span class="item_empty ">&nbsp;</span>'
                    : '';
                if ($this->show_questions) {
                    $qUrl = Route::url(
                        'index.php?option=com_resources&id='
                        . $tools[$i]->rid . '&active=answers'
                    );
                    $qKey = 'MOD_MYCONTRIBUTIONS_NUM_QUESTION'
                        . ($tools[$i]->q > 1 ? 'S' : '');
                    $qTitle = Lang::txt(
                        $qKey,
                        $tools[$i]->q,
                        $tools[$i]->q_new
                    );
                    $html .= '<span class="item_q">';
                    $html .= '<a href="' . $qUrl
                        . '" title="' . $qTitle . '">'
                        . $tools[$i]->q . '</a>';
                    $html .= '</span>' . "\n";
                } else {
                    $html .= '<span class="item_empty">'
                        . '&nbsp;</span>';
                }
                if ($this->show_wishes) {
                    $wUrl = Route::url(
                        'index.php?option=com_resources&id='
                        . $tools[$i]->rid . '&active=wishlist'
                    );
                    $wKey = 'MOD_MYCONTRIBUTIONS_NUM_WISH'
                        . ($tools[$i]->w > 1 ? 'S' : '');
                    $wTitle = Lang::txt(
                        $wKey,
                        $tools[$i]->w,
                        $tools[$i]->w_new
                    );
                    $html .= '<span class="item_w">';
                    $html .= '<a href="' . $wUrl
                        . '" title="' . $wTitle . '">'
                        . $tools[$i]->w . '</a>';
                    $html .= '</span>' . "\n";
                }
                if ($this->show_tickets) {
                    $sUrl = Route::url(
                        'index.php?option=com_support'
                        . '&task=tickets&find=group:'
                        . $tools[$i]->devgroup
                    );
                    $sKey = 'MOD_MYCONTRIBUTIONS_NUM_TICKET'
                        . ($tools[$i]->s > 1 ? 'S' : '');
                    $sTitle = Lang::txt(
                        $sKey,
                        $tools[$i]->s,
                        $tools[$i]->s_new
                    );
                    $html .= '<span class="item_s">';
                    $html .= '<a href="' . $sUrl
                        . '" title="' . $sTitle . '">'
                        . $tools[$i]->s . '</a>';
                    $html .= '</span>' . "\n";
                }
                $html .= '</span>' . "\n";
            }
            $html .= '</span>' . "\n";
            $html .= '</li>' . "\n";
        }
    }
    $html .= '</ul>' . "\n";

    $contribUrl = Route::url(
        'index.php?option=com_members&id='
        . User::get('id') . '&active=contributions'
    );
    $html .= '<h4><a href="' . $contribUrl . '">'
        . Lang::txt('MOD_MYCONTRIBUTIONS_OTHERS_IN_PROGRESS');
    if ($this->contributions && count($this->contributions) > $this->limit_other) {
        $html .= '<span>'
            . Lang::txt('MOD_MYCONTRIBUTIONS_VIEW_ALL')
            . '</span>' . "\n";
    }
    $html .= '</a></h4>' . "\n";
}

$contributions = $this->contributions;
if (!$contributions) {
    $html .= '<p>'
        . Lang::txt('MOD_MYCONTRIBUTIONS_NONE_FOUND')
        . '</p>' . "\n";
} else {
    require_once Component::path('com_members')
        . DS . 'models' . DS . 'member.php';

    $html .= '<ul class="expandedlist">' . "\n";
    for ($i = 0; $i < count($contributions); $i++) {
        if ($i < $this->limit_other) {
            // Determine css class
            switch ($contributions[$i]->published) {
                case 1:
                    $class = 'published';
                    break;
                case 2:
                    $class = 'draft';
                    break;
                case 3:
                    $class = 'pending';
                    break;
            }

            // Get author login
            $author_login = Lang::txt(
                'MOD_MYCONTRIBUTIONS_UNKNOWN'
            );
            $author = Components\Members\Models\Member::oneOrNew(
                $contributions[$i]->created_by
            );
            if ($author->get('id')) {
                $author_login = stripslashes(
                    $author->get('name')
                );
                if (in_array($author->get('access'), User::getAuthorisedViewLevels())) {
                    $author_login = '<a href="'
                        . Route::url($author->link())
                        . '">' . $author_login . '</a>';
                }
            }

            $itemUrl = Route::url(
                'index.php?option=com_resources'
                . '&task=draft&step=1&id='
                . $contributions[$i]->id
            );
            $itemTitle = \Hubzero\Utility\Str::truncate(
                stripslashes($contributions[$i]->title),
                40
            );
            $html .= "\t" . '<li class="'
                . $class . '">' . "\n";
            $html .= "\t\t" . '<a href="' . $itemUrl
                . '">' . $itemTitle . '</a>' . "\n";
            $html .= "\t\t" . '<span class="under">'
                . Lang::txt('MOD_MYCONTRIBUTIONS_TYPE')
                . ': ' . $contributions[$i]->typetitle
                . '<br />'
                . Lang::txt(
                    'MOD_MYCONTRIBUTIONS_SUBMITTED_BY',
                    $author_login
                )
                . '</span>' . "\n";
            $html .= "\t" . '</li>' . "\n";
        }
    }
    $html .= '</ul>' . "\n";
}

// Output final HTML
echo $html;
