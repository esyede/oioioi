<?php

require __DIR__.'/markdown.php';

function get_post_names()
{
    static $_cache = [];

    if (empty($_cache)) {
        $_cache = array_reverse(glob('data/articles/*.md'));
    }

    return $_cache;
}

function get_posts($page = 1, $perpage = 0)
{
    if ($perpage === 0) {
        $perpage = config('posts_perpage');
    }

    $posts = get_post_names();
    $posts = array_slice($posts, ($page - 1) * $perpage, $perpage);

    $tmp = [];

    foreach ($posts as $k => $v) {
        $post = new \stdClass();
        $arr = explode('_', $v);

        $post->date = strtotime(str_replace('data/articles/', '', $arr[0]));
        $post->url = site_url(date('Y/m', $post->date).'/'.str_replace('.md', '', $arr[1]));

        $content = parse_markdown_file($v);
        $arr = explode('</h1>', $content);

        $post->title = str_replace('<h1>', '', $arr[0]);
        $post->body = $arr[1];

        $tmp[] = $post;
    }

    return $tmp;
}

function find_post($year, $month, $name)
{
    foreach (get_post_names() as $index => $v) {
        if (strpos($v, "$year-$month") !== false && strpos($v, $name.'.md') !== false) {
            $arr = get_posts($index + 1, 1);

            return $arr[0];
        }
    }

    return false;
}

function has_pagination($page = 1)
{
    $total = count(get_post_names());
    $prev = ($page > 1);
    $next = ($total > ($page * config('posts_perpage')));

    return compact('prev', 'next');
}

function not_found()
{
    error(404, render('404', null, false));
}

function generate_json($posts)
{
    return json_encode($posts);
}

function parse_markdown_file($path)
{
    return \Markdown::render($path);
}

function get_static_pages()
{
    $names = glob('data/static/*.md');
    $names = is_array($names) ? $names : [];
    $names = array_map(function ($name) {
        return basename($name, '.md');
    }, $names);

    return $names;
}

function read_more($input, $length = 600, $ellipses = true, $strip_html = false)
{
    $input = $strip_html ? strip_tags($input) : $input;

    if (strlen($input) <= $length) {
        return $input;
    }

    $last_space = strrpos(substr($input, 0, $length), ' ');
    $trimmed_text = substr($input, 0, $last_space);

    if ($ellipses) {
        $trimmed_text .= '...';
    }

    return $trimmed_text;
}
