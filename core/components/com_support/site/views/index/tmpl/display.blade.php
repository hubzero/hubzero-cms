{{--
 * Support portal — introduction page with card-based sections.
 *
 * Variables from controller (displayTask):
 *   $title  — Page title (e.g. "Support")
 *   $option — Component option (com_support)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}
@php
    use Hubzero\Facades\Component;
    use Hubzero\Facades\Route;

    $faqUrl    = Route::url('index.php?option=com_help&component=support&page=faqs');
    $reportUrl = Route::url('index.php?option=com_support&task=new');
    $trackUrl  = Route::url('index.php?option=com_support&task=tickets');

    $hasResources = Component::isEnabled('com_resources');
    $hasTags      = Component::isEnabled('com_tags');
    $hasSearch    = Component::isEnabled('com_search');
    $hasAnswers   = Component::isEnabled('com_answers');
    $hasWishlist  = Component::isEnabled('com_wishlist');
    $hasWiki      = Component::isEnabled('com_wiki');
    $hasKb        = Component::isEnabled('com_kb');
@endphp

<x-page-container :title="$title">

    {{-- Introduction --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div>
            <h3 class="text-lg font-semibold mb-2">Getting Help</h3>
            <p>We offer several ways of finding content and encourage exploring our knowledge base
            and engaging the community for support.</p>
        </div>
        <div>
            <h3 class="text-lg font-semibold mb-2">When All Else Fails</h3>
            <p>Report problems to us directly and track their progress. We will try our best to
            answer your questions and work with you to resolve any issues you may have.</p>
        </div>
        <div>
            <h3 class="text-lg font-semibold mb-2">Quick Links</h3>
            <ul class="menu menu-sm bg-base-200 rounded-box">
                <li><a href="{{ $faqUrl }}">Support FAQ's</a></li>
                @if ($hasKb)
                    <li><a href="{{ Route::url('index.php?option=com_kb') }}">Knowledge Base</a></li>
                @endif
                <li><a href="{{ $reportUrl }}">Report Problems</a></li>
                <li><a href="{{ $trackUrl }}">Track Tickets</a></li>
            </ul>
        </div>
    </div>

    {{-- Finding Content --}}
    @if ($hasResources || $hasTags || $hasSearch)
        <h2 class="text-xl font-bold mb-4">Finding Content</h2>
        <x-card-grid :cols="3" class="mb-8">
            @if ($hasResources)
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">
                            <a href="{{ Route::url('index.php?option=com_resources') }}">Resources</a>
                        </h3>
                        <p>Find the latest cutting-edge research in our resources.</p>
                    </div>
                </div>
            @endif
            @if ($hasTags)
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">
                            <a href="{{ Route::url('index.php?option=com_tags') }}">Tags</a>
                        </h3>
                        <p>Explore all our content through tags or even tag content yourself.</p>
                    </div>
                </div>
            @endif
            @if ($hasSearch)
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">
                            <a href="{{ Route::url('index.php?option=com_search') }}">Search</a>
                        </h3>
                        <p>Try searching for a title, author, tag, phrase, or keywords.</p>
                    </div>
                </div>
            @endif
        </x-card-grid>
    @endif

    {{-- Community Help --}}
    @if ($hasAnswers || $hasWishlist || $hasWiki)
        <h2 class="text-xl font-bold mb-4">Community Help</h2>
        <x-card-grid :cols="3" class="mb-8">
            @if ($hasAnswers)
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">
                            <a href="{{ Route::url('index.php?option=com_answers') }}">Questions &amp; Answers</a>
                        </h3>
                        <p>Get your questions answered and help others find the clue.</p>
                    </div>
                </div>
            @endif
            @if ($hasWishlist)
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">
                            <a href="{{ Route::url('index.php?option=com_wishlist') }}">Wish List</a>
                        </h3>
                        <p>Tell everyone your ideas or features you would like to see.</p>
                    </div>
                </div>
            @endif
            @if ($hasWiki)
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">
                            <a href="{{ Route::url('index.php?option=com_wiki') }}">Wiki</a>
                        </h3>
                        <p>Take a look at our user-generated wiki pages or write your own.</p>
                    </div>
                </div>
            @endif
        </x-card-grid>
    @endif

    {{-- Getting Support --}}
    <h2 class="text-xl font-bold mb-4">Getting Support</h2>
    <x-card-grid :cols="3" class="mb-8">
        @if ($hasKb)
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title">
                        <a href="{{ Route::url('index.php?option=com_kb') }}">Knowledge Base</a>
                    </h3>
                    <p>Find answers to frequently asked questions, helpful tips, and any other
                    information we thought might be useful.</p>
                </div>
            </div>
        @endif
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h3 class="card-title">
                    <a href="{{ $reportUrl }}">Report Problems</a>
                </h3>
                <p>Report problems with our form and have your problem entered into our ticket tracking
                system. We guarantee a response!</p>
            </div>
        </div>
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h3 class="card-title">
                    <a href="{{ $trackUrl }}">Track Tickets</a>
                </h3>
                <p>Have a problem entered into our ticket tracking system? Track its progress,
                add comments and notes, or close resolved issues.</p>
            </div>
        </div>
    </x-card-grid>

</x-page-container>
