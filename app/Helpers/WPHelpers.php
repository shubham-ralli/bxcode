<?php

use Illuminate\Support\Facades\View;
use App\Services\ShortcodeService;

require_once __DIR__ . '/plugin_helpers.php';



if (!function_exists('add_shortcode')) {
    /**
     * Add a new shortcode.
     * @param string $tag
     * @param callable $callback
     */
    function add_shortcode($tag, $callback)
    {
        ShortcodeService::add($tag, $callback);
    }
}

if (!function_exists('do_shortcode')) {
    /**
     * Parse shortcodes in content.
     * @param string $content
     * @return string
     */
    function do_shortcode($content)
    {
        return ShortcodeService::parse($content);
    }
}

if (!function_exists('load_theme_functions')) {
    /**
     * Explicitly load the active theme's functions.blade.php
     */
    function load_theme_functions()
    {
        $activeTheme = get_active_theme();

        // Check for functions.php (Standard PHP)
        $phpPath = resource_path("views/themes/{$activeTheme}/functions.php");
        if (file_exists($phpPath)) {
            require_once $phpPath;
            return;
        }

        // Check for functions.blade.php (Blade supported)
        $bladePath = resource_path("views/themes/{$activeTheme}/functions.blade.php");
        if (file_exists($bladePath)) {
            require_once $bladePath;
        }
    }
}

if (!function_exists('get_template_part')) {
    /**
     * Load a template part into a template.
     * @param string|null $name The name of the specialized template.
     * @param array $args Additional arguments to pass to the template.
     */
    function get_template_part($slug, $name = null, $args = [])
    {
        $theme = get_active_theme();

        // Construct view name: themes.bxcode-theme.slug-name or themes.bxcode-theme.slug
        $viewBase = "themes.{$theme}.{$slug}";

        if ($name) {
            $viewName = "{$viewBase}-{$name}";
            // Fallback to base if specific doesn't exist? 
            // WP logic: try slug-name.php, then slug.php.
            // Blade logic: check if view exists.
            if (View::exists($viewName)) {
                echo View::make($viewName, $args)->render();
                return;
            }
        }

        // Try generic slug
        if (View::exists($viewBase)) {
            echo View::make($viewBase, $args)->render();
        }
    }
}

// Helper needed for shortcode regex (borrowed from WP)
if (!function_exists('get_shortcode_regex')) {
    function get_shortcode_regex($tagnames = null)
    {
        if (empty($tagnames)) {
            return ''; // Simplified for now
        }
        $tagregexp = join('|', array_map('preg_quote', $tagnames));

        return
            '\\['                              // Opening bracket
            . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
            . "($tagregexp)"                     // 2: Shortcode name
            . '(?![\\w-])'                       // Not followed by word character or hyphen
            . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
            . '[^\\]\\/]*'                   // Not a closing bracket or forward slash
            . '(?:'
            . '\\/(?!\\])'               // A forward slash not followed by a closing bracket
            . '[^\\]\\/]*'               // Not a closing bracket or forward slash
            . ')*?'
            . ')'
            . '(?:'
            . '(\\/)'                        // 4: Self closing tag ...
            . '\\]'                          // ... and closing bracket
            . '|'
            . '\\]'                          // Closing bracket
            . '(?:'
            . '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
            . '[^\\[]*+'             // Not an opening bracket
            . '(?:'
            . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
            . '[^\\[]*+'         // Not an opening bracket
            . ')*+'
            . ')'
            . '\\[\\/\\2\\]'             // Closing shortcode tag
            . ')?'
            . ')'
            . '(\\]?)';                          // 6: Optional second closing bracket for escaping shortcodes: [[tag]]
    }
}

if (!function_exists('shortcode_parse_atts')) {
    function shortcode_parse_atts($text)
    {
        $atts = array();
        $pattern = '/([\\w-]+)\\s*=\\s*"([^"]*)"(?:\\s|$)|([\\w-]+)\\s*=\\s*\'([^\']*)\'(?:\\s|$)|([\\w-]+)\\s*=\\s*([^\\s"\']+)(?:\\s|$)|"([^"]*)"(?:\\s|$)|(\S+)(?:\\s|$)/';
        $text = preg_replace("/[\\x{00a0}\\x{00b0}\\x{00c0}-\\x{00c6}\\x{00c0}-\\x{00d6}\\x{00d8}-\\x{00f0}\\x{00f8}-\\x{00ff}]/u", ' ', $text);

        if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
            foreach ($match as $m) {
                if (!empty($m[1]))
                    $atts[strtolower($m[1])] = stripcslashes($m[2]);
                elseif (!empty($m[3]))
                    $atts[strtolower($m[3])] = stripcslashes($m[4]);
                elseif (!empty($m[5]))
                    $atts[strtolower($m[5])] = stripcslashes($m[6]);
                elseif (isset($m[7]) && strlen($m[7]))
                    $atts[] = stripcslashes($m[7]);
                elseif (isset($m[8]))
                    $atts[] = stripcslashes($m[8]);
            }
        } else {
            $atts = ltrim($text);
        }
        return $atts;
    }
}

if (!function_exists('bx_nav_menu')) {
    /**
     * Display a navigation menu.
     * @param array $args
     */
    function bx_nav_menu($args = [])
    {
        $defaults = [
            'menu' => '', // Menu name, slug, or ID
            'theme_location' => '', // 'primary', 'footer', etc.
            'container' => 'div',
            'container_class' => '',
            'container_id' => '',
            'menu_class' => 'menu',
            'menu_id' => '',
            'echo' => true,
            'fallback_cb' => false,
            'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        ];

        $args = array_merge($defaults, $args);
        $menu = null;

        // 1. Try finding menu by Theme Location Setting
        if (!empty($args['theme_location'])) {
            $locationId = \App\Models\Setting::get("menu_location_" . $args['theme_location']);
            if ($locationId) {
                $menu = \App\Models\Menu::where('id', $locationId)
                    ->with([
                        'items' => function ($q) {
                            $q->orderBy('order');
                        }
                    ])->first();
            }
        }

        // 2. Fallback to direct 'menu' arg if no location found or not set
        if (!$menu && !empty($args['menu'])) {
            $menu = \App\Models\Menu::where('id', $args['menu'])
                ->orWhere('slug', $args['menu'])
                ->orWhere('name', $args['menu'])
                ->with([
                    'items' => function ($q) {
                        $q->orderBy('order');
                    }
                ])->first();
        }

        // 3. Last resort: Get first menu? Only if specifically requested or maybe just fail silently
        if (!$menu && empty($args['theme_location']) && empty($args['menu'])) {
            $menu = \App\Models\Menu::with([
                'items' => function ($q) {
                    $q->orderBy('order');
                }
            ])->first();
        }

        if (!$menu) {
            return;
        }

        // Build tree
        $items = $menu->items;
        $tree = build_menu_tree($items);

        $output = '';
        if ($args['container']) {
            $class = $args['container_class'] ? ' class="' . esc_attr($args['container_class']) . '"' : '';
            $id = $args['container_id'] ? ' id="' . esc_attr($args['container_id']) . '"' : '';
            $output .= '<' . $args['container'] . $id . $class . '>';
        }

        $items_html = walk_nav_menu_tree($tree, $args);

        $wrap_id = $args['menu_id'] ? $args['menu_id'] : 'menu-' . $menu->slug;
        $wrap_class = $args['menu_class'] ? $args['menu_class'] : 'menu';

        $output .= sprintf($args['items_wrap'], esc_attr($wrap_id), esc_attr($wrap_class), $items_html);

        if ($args['container']) {
            $output .= '</' . $args['container'] . '>';
        }

        if ($args['echo']) {
            echo $output;
        } else {
            return $output;
        }
    }
}

function build_menu_tree($items, $parentId = null)
{
    $branch = [];
    foreach ($items as $item) {
        if ($item->parent_id == $parentId) {
            $children = build_menu_tree($items, $item->id);
            if ($children) {
                $item->children = $children;
            }
            $branch[] = $item;
        }
    }
    return $branch;
}

function walk_nav_menu_tree($items, $args)
{
    $html = '';
    foreach ($items as $item) {
        $classes = [];
        $classes[] = 'menu-item';
        $classes[] = 'nav-item'; // Added standard class

        $classes[] = 'menu-item-' . $item->id;
        $classes[] = 'menu-item-type-' . ($item->type ?? 'custom');

        if (!empty($item->css_class)) {
            $classes[] = $item->css_class;
        }

        if (count($item->children) > 0) {
            $classes[] = 'menu-item-has-children';

            // Add 'dropdown' if it's a top-level parent (parent_id is null/0)
            if (empty($item->parent_id)) {
                $classes[] = 'dropdown';
            } else {
                // For nested dropdowns
                $classes[] = 'dropdown-submenu';
                $classes[] = 'dropdown';
            }
        }

        // Add user-provided item_class from args if exists
        if (!empty($args['item_class'])) {
            // Note: This applies to ALL items. User might want it on <a> or <li>. 
        }

        $class_names = join(' ', array_filter($classes));
        $id_attr = 'menu-item-' . $item->id;

        $html .= '<li id="' . esc_attr($id_attr) . '" class="' . esc_attr($class_names) . '">';

        $url = $item->url;

        // Handle target attribute
        $target = !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';

        // Build Link Classes
        $link_classes = ['nav-link']; // Always add standard nav-link class
        if (!empty($args['item_class'])) {
            $link_classes[] = $args['item_class'];
        }

        $link_class_attr = ' class="' . esc_attr(implode(' ', array_unique($link_classes))) . '"';

        $html .= '<a href="' . esc_attr($url) . '"' . $target . $link_class_attr . '>' . e($item->title);

        // Add toggle icon if needed? For now just title.
        // If specific toggle icon is needed (like <span class="dropdown-icon">▾</span>), it can be handled via CSS ::after on .dropdown > a
        $html .= '</a>';

        if (count($item->children) > 0) {
            // Use 'dropdown-menu' class instead of 'sub-menu'
            $html .= '<ul class="dropdown-menu">';
            $html .= walk_nav_menu_tree($item->children, $args);
            $html .= '</ul>';
        }
        $html .= '</li>';
    }
    return $html;
}

if (!function_exists('get_post')) {
    /**
     * Retrieve post data by ID or Slug.
     * @param int|string $id Post ID or Slug
     * @return \App\Models\Post|null
     */
    function get_post($id)
    {
        if (is_numeric($id)) {
            return \App\Models\Post::find($id);
        }
        return \App\Models\Post::where('slug', $id)->first();
    }
}

if (!function_exists('get_post_field')) {
    /**
     * Get a specific field from a post by ID or Slug.
     * Returns empty string if not found, making it safe for templates.
     * 
     * @param int|string $id
     * @param string $field
     * @return mixed
     */
    function get_post_field($id, $field = 'title')
    {
        $post = get_post($id);
        return $post ? $post->$field : '';
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('get_stylesheet_directory_uri')) {
    function get_stylesheet_directory_uri()
    {
        return get_theme_file_uri();
    }
}

if (!function_exists('bx_style')) {
    function bx_style($handle, $src = '', $deps = [], $ver = false, $media = 'all')
    {
        \App\Services\AssetService::enqueueStyle($handle, $src, $deps, $ver, $media);
    }
}

if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style($handle, $src = '', $deps = [], $ver = false, $media = 'all')
    {
        bx_style($handle, $src, $deps, $ver, $media);
    }
}

if (!function_exists('bx_script')) {
    function bx_script($handle, $src = '', $deps = [], $ver = false, $in_footer = false)
    {
        \App\Services\AssetService::enqueueScript($handle, $src, $deps, $ver, $in_footer);
    }
}

if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script($handle, $src = '', $deps = [], $ver = false, $in_footer = false)
    {
        bx_script($handle, $src, $deps, $ver, $in_footer);
    }
}

if (!function_exists('bx_posts_pagination')) {
    /**
     * Display previous/next post pagination buttons with Pretty URLs.
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator|null $posts
     */
    function bx_posts_pagination($posts = null)
    {
        if (!$posts) {
            $posts = \Illuminate\Support\Facades\View::shared('posts');
        }

        if (!$posts instanceof \Illuminate\Pagination\LengthAwarePaginator || !$posts->hasPages()) {
            return;
        }

        echo '<div class="bx-pagination mt-8 flex justify-between items-center">';

        $currentPage = $posts->currentPage();
        $lastPage = $posts->lastPage();

        // Get Base URL (Current URL without query strings and without /page/n)
        $currentUrl = url()->current();

        // If current URL ends with /page/{n}, strip it
        if (preg_match('/(.*)\/page\/\d+$/', $currentUrl, $matches)) {
            $baseUrl = $matches[1];
        } else {
            $baseUrl = $currentUrl;
        }

        // --- Previous Link ---
        if ($posts->onFirstPage()) {
            echo '<span class="px-4 py-2 border border-gray-300 text-gray-400 rounded cursor-not-allowed">Previous</span>';
        } else {
            // Logic: If prev page is 1, link to base URL. Else link to /page/{prev}
            $prevPage = $currentPage - 1;
            if ($prevPage == 1) {
                $prevUrl = $baseUrl;
            } else {
                $prevUrl = $baseUrl . '/page/' . $prevPage;
            }
            echo '<a href="' . $prevUrl . '" class="px-4 py-2 border border-blue-600 text-blue-600 rounded hover:bg-blue-50 transition">Previous</a>';
        }

        // --- Next Link ---
        if ($posts->hasMorePages()) {
            $nextUrl = $baseUrl . '/page/' . ($currentPage + 1);
            echo '<a href="' . $nextUrl . '" class="px-4 py-2 border border-blue-600 text-blue-600 rounded hover:bg-blue-50 transition">Next</a>';
        } else {
            echo '<span class="px-4 py-2 border border-gray-300 text-gray-400 rounded cursor-not-allowed">Next</span>';
        }

        echo '</div>';
    }
}

add_action('bx_head', function () {
    // 1. Trigger Enqueue Hook so themes can register scripts
    do_action('bx_enqueue_scripts');
    do_action('wp_enqueue_scripts'); // Alias support

    // 2. Print Styles
    if (class_exists('App\Services\AssetService')) {
        \App\Services\AssetService::printStyles();
        \App\Services\AssetService::printHeadScripts();
    }
}, 1);

add_action('bx_footer', function () {
    // Print Footer Scripts
    if (class_exists('App\Services\AssetService')) {
        \App\Services\AssetService::printFooterScripts();
    }
}, 20);

if (!function_exists('get_search_form')) {
    /**
     * Display search form.
     * Looks for searchform.blade.php in theme, otherwise defaults.
     */
    function get_search_form($echo = true)
    {
        $theme = get_active_theme();
        $view = "themes.{$theme}.searchform";

        if (View::exists($view)) {
            $form = View::make($view)->render();
        } else {
            // Default HTML5 Search Form
            $action = Route::has('frontend.search') ? route('frontend.search') : url('/search');

            $form = '<form role="search" method="get" class="search-form" action="' . $action . '">
                <label>
                    <span class="screen-reader-text">Search for:</span>
                    <input type="search" class="search-field" placeholder="Search &hellip;" value="' . request()->get('s') . '" name="s" />
                </label>
                <input type="submit" class="search-submit" value="Search" />
            </form>';
        }

        if ($echo) {
            echo $form;
        } else {
            return $form;
        }
    }
}

if (!function_exists('shortcode_atts')) {
    /**
     * Combine user attributes with known attributes and fill in defaults when needed.
     *
     * @param array  $pairs     Entire list of supported attributes and their defaults.
     * @param array  $atts      User defined attributes in shortcode tag.
     * @param string $shortcode Optional. The name of the shortcode, provided for convenience.
     * @return array Combined and filtered attribute list.
     */
    function shortcode_atts($pairs, $atts, $shortcode = '')
    {
        $atts = (array) $atts;
        $out = array();
        foreach ($pairs as $name => $default) {
            if (array_key_exists($name, $atts)) {
                $out[$name] = $atts[$name];
            } else {
                $out[$name] = $default;
            }
        }
        return $out;
    }
}

if (!function_exists('get_the_post_thumbnail')) {
    /**
     * Retrieve the post thumbnail.
     *
     * @param int|\WP_Post|object|null $post Optional. Post ID or Post object. Default is global $post (if available) or null.
     * @param string|array $size Optional. Image size. Default 'post-thumbnail'.
     * @param string|array $attr Optional. Query string or array of attributes. Default empty.
     * @return string The post thumbnail image tag.
     */
    function get_the_post_thumbnail($post = null, $size = 'post-thumbnail', $attr = '')
    {
        // Resolve Post
        if (is_null($post)) {
            // Try to get current post from global if available
            if (isset($GLOBALS['post'])) {
                $post = $GLOBALS['post'];
            } else {
                return '';
            }
        }

        if (is_numeric($post)) {
            $post = \App\Models\Post::find($post);
        }

        if (!$post || empty($post->featured_image_url)) {
            return '';
        }

        $media = $post->featuredMedia; // Eloquent relationship

        // Resolve Attributes
        $default_attr = [
            'src' => $post->featured_image_url,
            'class' => "attachment-{$size} size-{$size} wp-post-image bx-image-{$post->id}",
            'alt' => $media ? $media->alt_text : $post->title,
            'title' => $media ? $media->title : $post->title,
            'loading' => 'lazy',
        ];

        if (is_array($attr)) {
            // Handle class merging specifically
            if (isset($attr['class']) && isset($default_attr['class'])) {
                $attr['class'] = $default_attr['class'] . ' ' . $attr['class'];
            }
            $attr = array_merge($default_attr, $attr);
        } else {
            // Handle string attributes if needed (simplified for now)
            $attr = $default_attr;
        }

        // Build HTML
        $html = '<img';
        foreach ($attr as $name => $value) {
            // Skip empty values if necessary, but empty alt is valid
            $html .= ' ' . $name . '="' . htmlspecialchars($value ?? '') . '"';
        }
        $html .= ' />';

        return $html;
    }
}

if (!function_exists('the_post_thumbnail')) {
    /**
     * Display the post thumbnail.
     *
     * Supports standard WP syntax: the_post_thumbnail( $size, $attr )
     * And explicit syntax: the_post_thumbnail( $post, $size, $attr )
     *
     * @param int|\WP_Post|object|string|null $post_or_size Optional. Post ID/Object OR Image size.
     * @param string|array $size_or_attr Optional. Image size OR Attributes.
     * @param string|array $attr Optional. Attributes if 3 args used.
     */
    function the_post_thumbnail($post_or_size = null, $size_or_attr = 'post-thumbnail', $attr = '')
    {
        $post = null;
        $size = 'post-thumbnail';
        $attributes = '';

        // Check if first argument is likely a size (string) or null (default size)
        // AND second argument is array (attributes) or string (attributes)
        // OR simply if it's NOT a post object/id
        $is_first_arg_post = false;

        if (is_object($post_or_size) || (is_numeric($post_or_size) && $post_or_size > 0)) {
            $is_first_arg_post = true;
        }

        if ($is_first_arg_post) {
            // Usage: ($post, $size, $attr)
            $post = $post_or_size;
            $size = $size_or_attr;
            $attributes = $attr;
        } else {
            // Usage: ($size, $attr) - WP Style
            // Use global post implicit in get_the_post_thumbnail
            $post = null;
            $size = $post_or_size ?? 'post-thumbnail';
            $attributes = $size_or_attr;
        }

        echo get_the_post_thumbnail($post, $size, $attributes);
    }
}
