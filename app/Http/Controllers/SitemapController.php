<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SitemapController extends Controller
{
    /**
     * Check if sitemaps are enabled
     */
    private function checkSitemapEnabled()
    {
        $enabled = \App\Models\Setting::get('sitemap_enabled', '1');

        if ($enabled !== '1') {
            abort(404, 'XML Sitemaps are currently disabled.');
        }
    }

    /**
     * Generate sitemap index
     */
    public function index()
    {
        $this->checkSitemapEnabled();
        $sitemaps = Cache::remember('sitemap:index', 86400, function () {
            $sitemaps = [];

            // Post sitemap
            $lastPostMod = Post::where('type', 'post')
                ->where('status', 'publish')
                ->max('updated_at');
            if ($lastPostMod) {
                $sitemaps[] = [
                    'loc' => url('/post-sitemap.xml'),
                    'lastmod' => \Carbon\Carbon::parse($lastPostMod),
                ];
            }

            // Page sitemap
            $lastPageMod = Post::where('type', 'page')
                ->where('status', 'publish')
                ->max('updated_at');
            if ($lastPageMod) {
                $sitemaps[] = [
                    'loc' => url('/page-sitemap.xml'),
                    'lastmod' => \Carbon\Carbon::parse($lastPageMod),
                ];
            }

            // Custom post type sitemaps
            $customTypes = Post::select('type')
                ->whereNotIn('type', ['post', 'page'])
                ->where('status', 'publish')
                ->groupBy('type')
                ->pluck('type');

            foreach ($customTypes as $type) {
                $lastMod = Post::where('type', $type)
                    ->where('status', 'publish')
                    ->max('updated_at');
                if ($lastMod) {
                    $sitemaps[] = [
                        'loc' => url("/{$type}-sitemap.xml"),
                        'lastmod' => \Carbon\Carbon::parse($lastMod),
                    ];
                }
            }

            return $sitemaps;
        });

        return response()
            ->view('seo::sitemaps.index', ['sitemaps' => $sitemaps])
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Generate post sitemap
     */
    public function posts()
    {
        $this->checkSitemapEnabled();
        $urls = Cache::remember('sitemap:posts', 86400, function () {
            return Post::where('type', 'post')
                ->where('status', 'publish')
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($post) {
                    return [
                        'loc' => url('/' . $post->slug),
                        'lastmod' => $post->updated_at,
                        'changefreq' => $this->getChangeFreq($post),
                        'priority' => $this->getPriority($post),
                    ];
                });
        });

        return response()
            ->view('seo::sitemaps.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Generate page sitemap
     */
    public function pages()
    {
        $this->checkSitemapEnabled();
        $urls = Cache::remember('sitemap:pages', 86400, function () {
            return Post::where('type', 'page')
                ->where('status', 'publish')
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($post) {
                    return [
                        'loc' => url('/' . $post->slug),
                        'lastmod' => $post->updated_at,
                        'changefreq' => 'monthly',
                        'priority' => 0.8,
                    ];
                });
        });

        return response()
            ->view('seo::sitemaps.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Generate custom post type sitemap
     */
    public function postType($type)
    {
        $this->checkSitemapEnabled();
        $urls = Cache::remember("sitemap:{$type}", 86400, function () use ($type) {
            return Post::where('type', $type)
                ->where('status', 'publish')
                ->orderBy('updated_at', 'desc')
                ->get()
                ->map(function ($post) {
                    return [
                        'loc' => url('/' . $post->slug),
                        'lastmod' => $post->updated_at,
                        'changefreq' => $this->getChangeFreq($post),
                        'priority' => $this->getPriority($post),
                    ];
                });
        });

        return response()
            ->view('seo::sitemaps.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Determine change frequency based on post age
     */
    private function getChangeFreq($post)
    {
        $daysSinceUpdate = $post->updated_at->diffInDays(now());

        if ($daysSinceUpdate < 7) {
            return 'daily';
        } elseif ($daysSinceUpdate < 30) {
            return 'weekly';
        } elseif ($daysSinceUpdate < 180) {
            return 'monthly';
        } else {
            return 'yearly';
        }
    }

    /**
     * Calculate priority based on post age and type
     */
    private function getPriority($post)
    {
        $daysSincePublish = $post->created_at->diffInDays(now());

        if ($daysSincePublish < 7) {
            return 0.9; // Recent posts
        } elseif ($daysSincePublish < 30) {
            return 0.8; // This month
        } elseif ($daysSincePublish < 180) {
            return 0.7; // Last 6 months
        } else {
            return 0.6; // Older posts
        }
    }
}
