<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use App\Models\About;
use App\Models\Ad;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\HomeSectionSetting;
use App\Models\News;
use App\Models\RecivedMail;
use App\Models\SocialCount;
use App\Models\SocialLink;
use App\Models\Subscriber;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NewsExport;

class HomeController extends Controller
{
    public function __construct(protected \App\Services\NewsCacheService $newsCacheService)
    {
        // Construct
    }

    public function index()
    {
        // Require authentication and subscription
        if (!Auth::check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Please login and subscribe to access news content.');
        }

        $user = Auth::user();
        $activeSubscription = $user->activeSubscription;

        if (!$activeSubscription) {
            return redirect()
                ->route('subscription.plans')
                ->with('error', 'You need to subscribe to access news content. Please choose a subscription plan.');
        }

        $currentPackage = $user->currentPackage();
        $subscriptionTier = $currentPackage ? $currentPackage->slug : 'free';
        $lang = getLangauge();

        // Filter news based on subscription tier
        // Cache keys include language and tier
        $cacheSuffix = "{$lang}:{$subscriptionTier}";

        $breakingNews = $this->newsCacheService->getHeadlines("breaking:{$cacheSuffix}", function() use ($subscriptionTier, $user) {
            return News::where(['is_breaking_news' => 1,])
                ->activeEntries()->withLocalize()
                ->forSubscriptionTier($subscriptionTier)
                ->forUserLanguage($user)
                ->orderBy('breaking_order', 'ASC')
                ->orderBy('created_at', 'DESC')
                ->take(10)->get();
        });
            
        $heroSlider = $this->newsCacheService->getHeadlines("slider:{$cacheSuffix}", function() use ($subscriptionTier, $user) {
            return News::with(['category', 'auther'])
                ->where('show_at_slider', 1)
                ->activeEntries()
                ->withLocalize()
                ->forSubscriptionTier($subscriptionTier)
                ->forUserLanguage($user)
                ->orderBy('slider_order', 'ASC')
                ->orderBy('created_at', 'DESC')
                ->take(7)
                ->get();
        });

        $recentNews = $this->newsCacheService->getHeadlines("recent:{$cacheSuffix}", function() use ($subscriptionTier, $user) {
            return News::with(['category', 'auther'])
                ->activeEntries()
                ->withLocalize()
                ->forSubscriptionTier($subscriptionTier)
                ->forUserLanguage($user)
                ->orderBy('order_position', 'ASC')
                ->orderBy('created_at', 'DESC')
                ->take(6)->get();
        });
            
        $popularNews = $this->newsCacheService->getHeadlines("popular:{$cacheSuffix}", function() use ($subscriptionTier, $user) {
            return News::with(['category', 'auther'])
                ->where('show_at_popular', 1)
                ->activeEntries()
                ->withLocalize()
                ->forSubscriptionTier($subscriptionTier)
                ->forUserLanguage($user)
                ->orderBy('popular_order', 'ASC')
                ->orderBy('created_at', 'DESC')
                ->take(4)->get();
        });

        $HomeSectionSetting = HomeSectionSetting::where('language', getLangauge())->first();

        // Note: Category sections could also be cached, but sticking to main ones for now to simple integration
        if($HomeSectionSetting){
            $categorySectionOne = News::where('category_id', $HomeSectionSetting->category_section_one)
                ->activeEntries()->withLocalize()
                ->forSubscriptionTier($subscriptionTier)
                ->forUserLanguage($user)
                ->orderBy('order_position', 'ASC')
                ->orderBy('created_at', 'DESC')
                ->take(8)
                ->get();

        $categorySectionTwo = News::where('category_id', $HomeSectionSetting->category_section_two)
            ->activeEntries()->withLocalize()
            ->forSubscriptionTier($subscriptionTier)
            ->forUserLanguage($user)
            ->orderBy('order_position', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->take(8)
            ->get();

        $categorySectionThree = News::where('category_id', $HomeSectionSetting->category_section_three)
            ->activeEntries()->withLocalize()
            ->forSubscriptionTier($subscriptionTier)
            ->forUserLanguage($user)
            ->orderBy('order_position', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->take(6)
            ->get();

        $categorySectionFour = News::where('category_id', $HomeSectionSetting->category_section_four)
            ->activeEntries()->withLocalize()
            ->forSubscriptionTier($subscriptionTier)
            ->forUserLanguage($user)
            ->orderBy('order_position', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->take(4)
            ->get();
        }else {
            $categorySectionOne = collect();
            $categorySectionTwo = collect();
            $categorySectionThree = collect();
            $categorySectionFour = collect();
        }


        $mostViewedPosts = News::activeEntries()
            ->withLocalize()
            ->forSubscriptionTier($subscriptionTier)
            ->forUserLanguage($user)
            ->orderBy('views', 'DESC')
            ->take(3)
            ->get();

        $socialCounts = SocialCount::where(['status' => 1, 'language' => getLangauge()])->get();

        $mostCommonTags = $this->mostCommonTags();

        $ad = $this->getAdSettings();

        return view('frontend.home', compact(
            'breakingNews',
            'heroSlider',
            'recentNews',
            'popularNews',
            'categorySectionOne',
            'categorySectionTwo',
            'categorySectionThree',
            'categorySectionFour',
            'mostViewedPosts',
            'socialCounts',
            'mostCommonTags',
            'ad'
        ));
    }

    public function ShowNews(string $slug)
    {
        // Require authentication and subscription
        if (!Auth::check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Please login and subscribe to access news content.');
        }

        $user = Auth::user();
        $activeSubscription = $user->activeSubscription;

        if (!$activeSubscription) {
            return redirect()
                ->route('subscription.plans')
                ->with('error', 'You need to subscribe to access news content. Please choose a subscription plan.');
        }

        // Light query to get ID
        $newsId = News::where('slug', $slug)
            ->activeEntries()->withLocalize()
            ->value('id');

        if (!$newsId) {
            abort(404);
        }

        // Fetch full object from cache
        $news = $this->newsCacheService->getArticle($newsId, function() use ($newsId) {
            return News::with(['auther', 'tags', 'comments'])
                ->activeEntries() // Ensure we still apply scopes in the fetch
                ->find($newsId);
        });
        
        // Double check existence (if cache somehow returned null)
        if (!$news) {
            abort(404);
        }

        // Check if user's subscription tier can access this specific news
        if (!$user->canAccessNews($news)) {
            return redirect()
                ->route('subscription.plans')
                ->with('error', 'This content requires a higher subscription tier. Please upgrade your plan to access.');
        }

        $this->countView($news);
        
        // Log user activity - viewing news
        if (Auth::check()) {
            // Activity logging should probably be outside cache logic or handled carefully
            // The model instance from Cache might be generic array if not serializing properly?
            // Laravel Cache serializes objects so methods should work.
            $news->logActivity('viewed', null, ['news_id' => $news->id, 'slug' => $news->slug]);
        }

        $recentNews = News::with(['category', 'auther'])->where('slug','!=', $news->slug)
            ->activeEntries()->withLocalize()->orderBy('id', 'DESC')->take(4)->get();

        $mostCommonTags = $this->mostCommonTags();

        $nextPost = News::where('id', '>', $news->id)
            ->activeEntries()
            ->withLocalize()
            ->orderBy('id', 'asc')->first();

        $previousPost = News::where('id', '<', $news->id)
            ->activeEntries()
            ->withLocalize()
            ->orderBy('id', 'desc')->first();

        $relatedPosts = News::where('slug', '!=', $news->slug)
            ->where('category_id', $news->category_id)
            ->activeEntries()
            ->withLocalize()
            ->take(5)
            ->get();

        $socialCounts = SocialCount::where(['status' => 1, 'language' => getLangauge()])->get();

        $ad = $this->getAdSettings();

        return view('frontend.news-details', compact('news', 'recentNews', 'mostCommonTags', 'nextPost', 'previousPost', 'relatedPosts', 'socialCounts', 'ad'));
    }

    /**
     * Export news article in various formats
     */
    public function exportNews(Request $request, string $slug)
    {
        // Require authentication
        if (!Auth::check()) {
            abort(403, 'Please login to export articles.');
        }

        $user = Auth::user();
        $news = News::with(['auther', 'category', 'tags'])->where('slug', $slug)
            ->activeEntries()->withLocalize()
            ->first();

        if (!$news) {
            abort(404, 'Article not found.');
        }

        // Check subscription access
        if (!$user->canAccessNews($news)) {
            abort(403, 'You need a subscription to export this article.');
        }

        $format = $request->input('format', 'pdf');

        // Log export activity
        try {
            $news->logActivity('exported', null, [
                'news_id' => $news->id,
                'slug' => $news->slug,
                'format' => $format
            ]);
        } catch (\Throwable $th) {
            Log::error("Failed to log export activity: " . $th->getMessage());
        }

        switch ($format) {
            case 'pdf':
                return $this->exportAsPdf($news);
            case 'xls':
            case 'xlsx':
                return $this->exportAsExcel($news, $format);
            case 'xml':
                return $this->exportAsXml($news);
            case 'json':
                return $this->exportAsJson($news);
            case 'txt':
                return $this->exportAsText($news);
            default:
                abort(400, 'Invalid export format.');
        }
    }

    /**
     * Export news as PDF
     */
    protected function exportAsPdf(News $news)
    {
        try {
            $pdf = PDF::loadView('frontend.exports.news-pdf', compact('news'));
            $filename = Str::slug($news->title) . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('PDF export failed: ' . $e->getMessage());
            // Fallback to simple PDF if package not available
            return response($this->generateSimplePdf($news), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . Str::slug($news->title) . '.pdf"');
        }
    }

    /**
     * Export news as Excel
     */
    protected function exportAsExcel(News $news, string $format = 'xlsx')
    {
        try {
            $data = [
                ['Title', $news->title],
                ['Author', $news->auther->name ?? 'N/A'],
                ['Category', $news->category->name ?? 'N/A'],
                ['Published Date', $news->created_at->format('Y-m-d H:i:s')],
                ['Views', $news->views],
                ['Content', strip_tags($news->content)],
            ];

            if ($news->tags->isNotEmpty()) {
                $data[] = ['Tags', $news->tags->pluck('name')->implode(', ')];
            }

            $filename = Str::slug($news->title) . '.' . $format;
            
            return Excel::download(new NewsExport($data, $news->title), $filename);
        } catch (\Exception $e) {
            // Fallback to CSV if Excel package not available
            return $this->exportAsCsv($news);
        }
    }

    /**
     * Export news as XML
     */
    protected function exportAsXml(News $news)
    {
        // Use DOMDocument for proper CDATA support
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        
        $article = $dom->createElement('article');
        $dom->appendChild($article);
        
        // Add title
        $title = $dom->createElement('title', htmlspecialchars($news->title));
        $article->appendChild($title);
        
        // Add author
        $author = $dom->createElement('author', htmlspecialchars($news->auther->name ?? 'N/A'));
        $article->appendChild($author);
        
        // Add category
        $category = $dom->createElement('category', htmlspecialchars($news->category->name ?? 'N/A'));
        $article->appendChild($category);
        
        // Add published date
        $publishedDate = $dom->createElement('published_date', $news->created_at->format('Y-m-d H:i:s'));
        $article->appendChild($publishedDate);
        
        // Add views
        $views = $dom->createElement('views', (string)$news->views);
        $article->appendChild($views);
        
        // Add content with CDATA
        $content = $dom->createElement('content');
        $cdata = $dom->createCDATASection(strip_tags($news->content));
        $content->appendChild($cdata);
        $article->appendChild($content);
        
        // Add tags if available
        if ($news->tags->isNotEmpty()) {
            $tags = $dom->createElement('tags');
            foreach ($news->tags as $tag) {
                $tagElement = $dom->createElement('tag', htmlspecialchars($tag->name));
                $tags->appendChild($tagElement);
            }
            $article->appendChild($tags);
        }

        $filename = Str::slug($news->title) . '.xml';
        
        return response($dom->saveXML(), 200)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export news as JSON
     */
    protected function exportAsJson(News $news)
    {
        $data = [
            'title' => $news->title,
            'author' => $news->auther->name ?? null,
            'category' => $news->category->name ?? null,
            'published_date' => $news->created_at->format('Y-m-d H:i:s'),
            'views' => $news->views,
            'content' => strip_tags($news->content),
            'tags' => $news->tags->pluck('name')->toArray(),
            'meta_title' => $news->meta_title,
            'meta_description' => $news->meta_description,
        ];

        $filename = Str::slug($news->title) . '.json';
        
        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export news as Text
     */
    protected function exportAsText(News $news)
    {
        $content = "Title: {$news->title}\n\n";
        $content .= "Author: " . ($news->auther->name ?? 'N/A') . "\n";
        $content .= "Category: " . ($news->category->name ?? 'N/A') . "\n";
        $content .= "Published Date: " . $news->created_at->format('Y-m-d H:i:s') . "\n";
        $content .= "Views: {$news->views}\n\n";
        $content .= "Content:\n" . strip_tags($news->content) . "\n\n";
        
        if ($news->tags->isNotEmpty()) {
            $content .= "Tags: " . $news->tags->pluck('name')->implode(', ') . "\n";
        }

        $filename = Str::slug($news->title) . '.txt';
        
        return response($content, 200)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export as CSV (fallback for Excel)
     */
    protected function exportAsCsv(News $news)
    {
        $filename = Str::slug($news->title) . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($news) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Field', 'Value']);
            fputcsv($file, ['Title', $news->title]);
            fputcsv($file, ['Author', $news->auther->name ?? 'N/A']);
            fputcsv($file, ['Category', $news->category->name ?? 'N/A']);
            fputcsv($file, ['Published Date', $news->created_at->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Views', $news->views]);
            fputcsv($file, ['Content', strip_tags($news->content)]);
            if ($news->tags->isNotEmpty()) {
                fputcsv($file, ['Tags', $news->tags->pluck('name')->implode(', ')]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate simple PDF (fallback)
     */
    protected function generateSimplePdf(News $news): string
    {
        $html = "<h1>{$news->title}</h1>";
        $html .= "<p><strong>Author:</strong> " . ($news->auther->name ?? 'N/A') . "</p>";
        $html .= "<p><strong>Category:</strong> " . ($news->category->name ?? 'N/A') . "</p>";
        $html .= "<p><strong>Published:</strong> " . $news->created_at->format('Y-m-d H:i:s') . "</p>";
        $html .= "<p><strong>Views:</strong> {$news->views}</p>";
        $html .= "<hr>";
        $html .= "<div>" . strip_tags($news->content) . "</div>";
        
        // This is a basic fallback - in production, use a proper PDF library
        return $html;
    }

    public function news(Request $request)
    {
        // Require authentication and subscription
        if (!Auth::check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Please login and subscribe to access news content.');
        }

        $user = Auth::user();
        $activeSubscription = $user->activeSubscription;

        if (!$activeSubscription) {
            return redirect()
                ->route('subscription.plans')
                ->with('error', 'You need to subscribe to access news content. Please choose a subscription plan.');
        }

        $news = News::query();

        $news->when($request->has('tag'), function($query) use ($request){
            $query->whereHas('tags', function($query) use ($request){
                $query->where('name', $request->tag);
            });
        });

        $news->when($request->has('category') && !empty($request->category), function($query) use ($request) {
            $query->whereHas('category', function($query) use ($request) {
                $query->where('slug', $request->category);
            });
        });

        $news->when($request->has('search'), function($query) use ($request) {
            $query->where(function($query) use ($request){
                $query->where('title', 'like','%'.$request->search.'%')
                    ->orWhere('content', 'like','%'.$request->search.'%');
            })->orWhereHas('category', function($query) use ($request){
                $query->where('name', 'like','%'.$request->search.'%');
            });
        });

        // Filter by subscription tier
        $currentPackage = $user->currentPackage();
        $subscriptionTier = $currentPackage ? $currentPackage->slug : 'free';
            
        $news = $news->activeEntries()
            ->withLocalize()
            ->forSubscriptionTier($subscriptionTier)
            ->forUserLanguage($user)
            ->orderBy('order_position', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->paginate(20);


        $recentNews = News::with(['category', 'auther'])
            ->activeEntries()
            ->withLocalize()
            ->forUserLanguage($user)
            ->orderBy('order_position', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->take(4)->get();
        $mostCommonTags = $this->mostCommonTags();

        // Get categories ordered to match menu: nav categories first by order, then non-nav by order
        $categories = Category::where(['status' => 1, 'language' => getLangauge()])
            ->orderBy('show_at_nav', 'desc') // Show nav categories first (1 before 0)
            ->orderByRaw('COALESCE(`order`, 999999) ASC') // Then by order (lower numbers first)
            ->orderBy('id', 'asc') // Finally by ID as tiebreaker
            ->get();

        $ad = $this->getAdSettings();

        return view('frontend.news', compact('news', 'recentNews', 'mostCommonTags', 'categories', 'ad'));
    }

    public function countView($news)
    {
        if(session()->has('viewed_posts')){
            $postIds = session('viewed_posts');

            if(!in_array($news->id, $postIds)){
                $postIds[] = $news->id;
                $news->increment('views');
            }
            session(['viewed_posts' => $postIds]);

        }else {
            session(['viewed_posts' => [$news->id]]);

            $news->increment('views');

        }
    }

    public function mostCommonTags()
    {
        return Tag::select('name', \DB::raw('COUNT(*) as count'))
            ->where('language', getLangauge())
            ->groupBy('name')
            ->orderByDesc('count')
            ->take(15)
            ->get();
    }

    public function handleComment(Request $request)
    {

        $request->validate([
            'comment' => ['required', 'string', 'max:1000']
        ]);

        $comment = new Comment();
        $comment->news_id = $request->news_id;
        $comment->user_id = Auth::user()->id;
        $comment->parent_id = $request->parent_id;
        $comment->comment = $request->comment;
        $comment->save();
        
        // Log comment activity - the Loggable trait will log 'created', but we also want to log 'commented' on the news
        if ($comment->news_id) {
            $news = News::find($comment->news_id);
            if ($news) {
                $news->logActivity('commented', null, ['comment_id' => $comment->id]);
            }
        }
        
        toast(__('frontend.Comment added successfully!'), 'success');
        return redirect()->back();
    }

    public function handleReplay(Request $request)
    {

        $request->validate([
            'replay' => ['required', 'string', 'max:1000']
        ]);

        $comment = new Comment();
        $comment->news_id = $request->news_id;
        $comment->user_id = Auth::user()->id;
        $comment->parent_id = $request->parent_id;
        $comment->comment = $request->replay;
        $comment->save();
        
        // Log comment activity on the news
        if ($comment->news_id) {
            $news = News::find($comment->news_id);
            if ($news) {
                $news->logActivity('commented', null, ['comment_id' => $comment->id, 'is_reply' => true]);
            }
        }
        
        toast(__('frontend.Comment added successfully!'), 'success');

        return redirect()->back();
    }

    public function commentDestory(Request $request)
    {
        $comment = Comment::findOrFail($request->id);
        if(Auth::user()->id === $comment->user_id){
            $comment->delete();
            return response(['status' => 'success', 'message' => __('frontend.Deleted Successfully!')]);
        }

        return response(['status' => 'error', 'message' => __('frontend.Someting went wrong!')]);
    }

    public function SubscribeNewsLetter(Request $request)
    {
       $request->validate([
        'email' => ['required', 'email', 'max:255']
       ]);

       // Check if user with this email exists
       $user = \App\Models\User::where('email', $request->email)->first();
       
       if ($user) {
           // User exists, ensure email notifications are enabled
           if (!$user->email_notifications_enabled) {
               $user->email_notifications_enabled = true;
               $user->save();
           }
       } else {
           // Create a new user account for newsletter subscription
           $user = \App\Models\User::create([
               'name' => explode('@', $request->email)[0], // Use email prefix as name
               'email' => $request->email,
               'password' => \Hash::make(\Str::random(32)), // Random password
               'email_notifications_enabled' => true,
           ]);
       }
       
       // Ensure unsubscribe token exists
       if (!$user->unsubscribe_token) {
           $user->unsubscribe_token = \Str::random(64);
           $user->save();
       }

       return response(['status' => 'success', 'message' => __('frontend.Subscribed successfully!')]);

    }

    public function about()
    {
        $about = About::where('language', getLangauge())->first();
        return view('frontend.about', compact('about'));
    }

    public function contact()
    {
        $contact = Contact::where('language', getLangauge())->first();
        $socials = SocialLink::where('status', 1)->get();
        return view('frontend.contact', compact('contact', 'socials'));
    }

    public function handleContactFrom(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:500']
        ]);

        try {
            // Get contact email - try current language first, then fallback to 'en'
            $toMail = Contact::where('language', getLangauge())->first();
            if (!$toMail) {
                $toMail = Contact::where('language', 'en')->first();
            }

            // If still no contact found, use a default email or skip sending
            if ($toMail && $toMail->email) {
                /** Send Mail */
                Mail::to($toMail->email)->send(new ContactMail($request->subject, $request->message, $request->email));
            }

            /** Store the mail */
            $mail = new RecivedMail();
            $mail->email = $request->email;
            $mail->subject = $request->subject;
            $mail->message = $request->message;
            $mail->save();

            return redirect()->back()->with('success', __('frontend.Message sent successfully!'));

        } catch (\Exception $e) {
            Log::error('Contact form submission error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', __('frontend.Something went wrong. Please try again later.'));
        }
    }

    /**
     * Get advertisement settings or fall back to safe defaults.
     */
    private function getAdSettings(): Ad
    {
        return Ad::first() ?? new Ad([
            'home_top_bar_ad' => '',
            'home_top_bar_ad_status' => 0,
            'home_top_bar_ad_url' => '#',
            'home_middle_ad' => '',
            'home_middle_ad_status' => 0,
            'home_middle_ad_url' => '#',
            'view_page_ad' => '',
            'view_page_ad_status' => 0,
            'view_page_ad_url' => '#',
            'news_page_ad' => '',
            'news_page_ad_status' => 0,
            'news_page_ad_url' => '#',
            'side_bar_ad' => '',
            'side_bar_ad_status' => 0,
            'side_bar_ad_url' => '#',
        ]);
    }
}
