<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Blog Module Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the Blog module.
    |
    */

    'posts_per_page' => env('BLOG_POSTS_PER_PAGE', 10),

    'enable_comments' => env('BLOG_ENABLE_COMMENTS', true),

    'enable_categories' => env('BLOG_ENABLE_CATEGORIES', true),
];
