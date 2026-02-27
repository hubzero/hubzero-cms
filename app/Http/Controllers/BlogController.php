<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace App\Http\Controllers;

use App\Models\BlogComment;
use App\Models\BlogEntry;
use App\View\LegacyTemplateRenderer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $entries = BlogEntry::published()
            ->site()
            ->with('creator')
            ->withCount(['comments' => function ($q) {
                $q->where('state', 1);
            }])
            ->latest('created')
            ->get();

        if ($request->query('tmpl') === 'legacy') {
            return $this->legacyIndex($entries);
        }

        return view('blog.index', compact('entries'));
    }

    public function show(Request $request, int $year, int $month, string $alias)
    {
        $entry = BlogEntry::published()
            ->site()
            ->where('alias', $alias)
            ->with([
                'creator',
                'comments' => function ($q) {
                    $q->published()->topLevel()->with('creator');
                },
                'comments.replies.creator',
            ])
            ->firstOrFail();

        if ($request->query('tmpl') === 'legacy') {
            return $this->legacyShow($entry);
        }

        return view('blog.show', compact('entry'));
    }

    // -- Legacy template rendering --

    private function legacyIndex($entries): Response
    {
        $html = '<div class="container">';
        $html .= '<h2>Blog</h2>';

        if ($entries->isEmpty()) {
            $html .= '<p>No blog entries yet.</p>';
        }

        foreach ($entries as $entry) {
            $date = ($entry->publish_up ?? $entry->created)->format('M j, Y');
            $author = htmlspecialchars($entry->creator->name ?? 'Anonymous');
            $title = htmlspecialchars($entry->title);
            $excerpt = htmlspecialchars($entry->excerpt);
            $url = $entry->url . '?tmpl=legacy';
            $count = $entry->comments_count;

            $commentText = $count ? " &middot; {$count} " . ($count === 1 ? 'comment' : 'comments') : '';

            $html .= <<<ENTRY
                <div style="margin-bottom: 2em; padding-bottom: 1.5em; border-bottom: 1px solid #e5e5e5;">
                    <h3 style="margin-bottom: 0.25em;"><a href="{$url}">{$title}</a></h3>
                    <p style="color: #666; font-size: 0.9em; margin-bottom: 0.5em;">
                        {$author} &middot; {$date}{$commentText}
                    </p>
                    <p>{$excerpt}</p>
                </div>
            ENTRY;
        }

        $html .= '</div>';

        return $this->renderLegacy($html, 'Blog');
    }

    private function legacyShow(BlogEntry $entry): Response
    {
        $date = ($entry->publish_up ?? $entry->created)->format('M j, Y \a\t g:ia');
        $author = htmlspecialchars($entry->creator->name ?? 'Anonymous');
        $title = htmlspecialchars($entry->title);

        $html = '<div class="container">';
        $html .= '<p><a href="/blog?tmpl=legacy">&larr; Back to Blog</a></p>';
        $html .= "<h2>{$title}</h2>";
        $html .= "<p style=\"color: #666; font-size: 0.9em;\">{$author} &middot; {$date}</p>";
        $html .= '<div>' . $entry->content . '</div>';

        if ($entry->comments->count()) {
            $count = $entry->comments->count();
            $label = $count === 1 ? '1 Comment' : "{$count} Comments";
            $html .= "<h3 style=\"margin-top: 2em;\">{$label}</h3>";

            foreach ($entry->comments as $comment) {
                $html .= $this->renderComment($comment);
            }
        }

        $html .= '</div>';

        return $this->renderLegacy($html, $entry->title);
    }

    private function renderComment(BlogComment $comment, int $depth = 0): string
    {
        $indent = $depth * 30;
        $author = htmlspecialchars($comment->creator->name ?? 'Anonymous');
        $date = $comment->created->format('M j, Y \a\t g:ia');

        $html = <<<COMMENT
            <div style="margin-left: {$indent}px; margin-top: 1em; padding: 0.75em; background: #f9f9f9; border-radius: 4px;">
                <p style="margin: 0 0 0.25em; font-size: 0.9em; color: #666;">
                    <strong>{$author}</strong> &middot; {$date}
                </p>
                <div>{$comment->content}</div>
            </div>
        COMMENT;

        foreach ($comment->replies as $reply) {
            $html .= $this->renderComment($reply, $depth + 1);
        }

        return $html;
    }

    private function renderLegacy(string $componentHtml, string $title): Response
    {
        $renderer = new LegacyTemplateRenderer(
            'hubzero',
            base_path('app/templates/hubzero')
        );

        return new Response($renderer->render($componentHtml, $title));
    }
}
