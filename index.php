<?php

date_default_timezone_set('Asia/Jakarta');

require 'core/router.php';
require 'core/functions.php';

config('source', 'config.php');

get('/index', function () {
    $page = from($_GET, 'page');
    $page = $page ? (int) $page : 1;
    $posts = get_posts($page);

    if (empty($posts) || $page < 1) {
        not_found();
    }

    $has_pagination = has_pagination($page);
    render('main', compact('page', 'posts', 'has_pagination'));
});

get('/static/:name', function ($name) {
    $name = preg_replace('/[^a-zA-Z_-]/', '', $name);
    $name = __DIR__."/data/static/$name.md";

    if (! is_file($name)) {
        not_found();
    }

    $data = parse_markdown_file($name);
    $title = ucwords(basename($name, '.md'));
    render('static', compact('data', 'title'));
});

get('/:year/:month/:name', function ($year, $month, $name) {
    $post = find_post($year, $month, $name);

    if (! $post) {
        not_found();
    }

    $title = $post->title.' - '.config('blog_title');
    render('post', compact('post', 'title'));
});

get('/api/json', function () {
    header('Content-type: application/json');
    echo generate_json(get_posts(1, 10));
});

get('.*', function () {
    not_found();
});

listen();
