<?php

// ==============================================================================
// SHORTCODE REGISTRATION FILE
// ==============================================================================
// This file contains all your custom shortcodes. 
// It is included by functions.php.

/**
 * 1. SIMPLE SHORTCODE
 * Usage: [simple_test]
 * Description: Returns a static string or HTML. Great for simple alerts or banners.
 */
add_shortcode('simple_test', function () {
    return '<div class="alert bg-blue-100 text-blue-800 p-4 rounded mb-4">This is a <strong>Simple Shortcode</strong> output!</div>';
});

/**
 * 2. ATTRIBUTE SHORTCODE
 * Usage: [greet name="John" color="red"]
 * Description: Accepts parameters (attributes) to customize the output.
 */
add_shortcode('greet', function ($atts) {
    // 1. Define defaults and merge with user attributes
    $atts = shortcode_atts([
        'name' => 'Guest',
        'color' => 'blue'
    ], $atts);

    // 2. Logic based on attributes
    $colorClass = match ($atts['color']) {
        'red' => 'text-red-600',
        'green' => 'text-green-600',
        'blue' => 'text-blue-600',
        default => 'text-gray-600'
    };

    // 3. Return the HTML
    return "<div class='text-lg font-semibold {$colorClass} mb-4'>Hello, " . htmlspecialchars($atts['name']) . "!</div>";
});

/**
 * 3. TEMPLATE PART SHORTCODE (ADVANCED)
 * Usage: [theme_card title="My Card" link="/about"]
 * Description: Loads a separate Blade file (partial) to keep HTML clean.
 */
add_shortcode('theme_card', function ($atts) {
    $atts = shortcode_atts([
        'title' => 'Default Card Title',
        'content' => 'This is the default card content.',
        'link' => '#'
    ], $atts);

    // Start Output Buffering to capture the view output
    ob_start();

    // Load the Blade view 'partials/shortcode-card.blade.php' and pass attributes
    get_template_part('partials.shortcode-card', null, $atts);

    // Return the captured content
    return ob_get_clean();
});
