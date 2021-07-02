<?php

function _log($message)
{
    if (config('debug.enable') == true && php_sapi_name() !== 'cli') {
        $file = config('debug.log');
        $type = $file ? 3 : 0;
        error_log("{$message}\n", $type, $file);
    }
}

function site_url($url = '')
{
    $base = ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on') ? 'https' : 'http');
    $base .= '://'.$_SERVER['HTTP_HOST'];
    $base .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

    if (empty($base)) {
        error(500, 'Site URL cannot be empty');
    }

    return rtrim($base, '/').'/'.(ltrim($url, '/'));
}

function site_path()
{
    static $_path;

    $base = site_url();

    if (empty($base)) {
        error(500, 'Site URL cannot be empty');
    }

    if (! $_path) {
        $_path = rtrim(parse_url($base, PHP_URL_PATH), '/');
    }

    return $_path;
}

function error($code, $message)
{
    @header("HTTP/1.0 {$code} {$message}", true, $code);
    die($message);
}

function config($key, $value = null)
{
    static $_config = [];

    if ($key === 'source' && is_file($value)) {
        $_config = require $value;
    } elseif ($value == null) {
        return (isset($_config[$key]) ? $_config[$key] : null);
    } else {
        $_config[$key] = $value;
    }
}

function to_b64($str)
{
    $str = base64_encode($str);
    $str = preg_replace(['/\//', '/\+/', '/\=/'], ['_', '.', '-'], $str);

    return trim($str, '-');
}

function from_b64($str)
{
    $str = preg_replace(['/\_/', '/\./', '/\-/'], ['/', '+', '='], $str);
    $str = base64_decode($str);

    return $str;
}

function set_cookie($name, $value, $expire = 31536000, $path = '/')
{
    setcookie($name, $value, time() + $expire, $path);
}

function get_cookie($name)
{
    $value = from($_COOKIE, $name);
    return $value;
}

function delete_cookie()
{
    $cookies = func_get_args();

    foreach ($cookies as $ck) {
        setcookie($ck, '', -10, '/');
    }
}

if (extension_loaded('apc')) {
    function cache($key, $func, $ttl = 0)
    {
        if (($data = apc_fetch($key)) === false) {
            $data = call_user_func($func);

            if ($data !== null) {
                apc_store($key, $data, $ttl);
            }
        }

        return $data;
    }

    function cache_invalidate()
    {
        foreach (func_get_args() as $key) {
            apc_delete($key);
        }
    }
}

function warn($name = null, $message = null)
{
    static $warnings = [];

    if ($name == '*') {
        return $warnings;
    }

    if (! $name) {
        return count(array_keys($warnings));
    }

    if (! $message) {
        return isset($warnings[$name]) ? $warnings[$name] : null ;
    }

    $warnings[$name] = $message;
}

function _u($str)
{
    return urlencode($str);
}

function _h($str, $enc = 'UTF-8', $flags = ENT_QUOTES)
{
    return htmlentities($str, $flags, $enc);
}

function from($source, $name)
{
    if (is_array($name)) {
        $data = [];

        foreach ($name as $k) {
            $data[$k] = isset($source[$k]) ? $source[$k] : null ;
        }

        return $data;
    }

    return isset($source[$name]) ? $source[$name] : null ;
}

function stash($name, $value = null)
{
    static $_stash = [];

    if ($value === null) {
        return isset($_stash[$name]) ? $_stash[$name] : null;
    }

    $_stash[$name] = $value;

    return $value;
}

function method($verb = null)
{
    if ($verb == null || (strtoupper($verb) == strtoupper($_SERVER['REQUEST_METHOD']))) {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    error(400, 'bad request');
}

function client_ip()
{
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    return $_SERVER['REMOTE_ADDR'];
}

function redirect(/* $code_or_path, $path_or_cond, $cond */)
{
    $argv = func_get_args();
    $argc = count($argv);

    $path = null;
    $code = 302;
    $cond = true;

    switch ($argc) {
    case 3:
      list($code, $path, $cond) = $argv;
      break;

    case 2:
      if (is_string($argv[0]) ? $argv[0] : $argv[1]) {
          $code = 302;
          $path = $argv[0];
          $cond = $argv[1];
      } else {
          $code = $argv[0];
          $path = $argv[1];
      }
      break;

    case 1:
      if (! is_string($argv[0])) {
          error(500, 'bad call to redirect()');
      }
      $path = $argv[0];
      break;

    default:
      error(500, 'bad call to redirect()');
  }

    $cond = (is_callable($cond) ? call_user_func($cond) : $cond);

    if (! $cond) {
        return;
    }

    header('Location: '.$path, true, $code);
    exit;
}

function partial($view, $locals = null)
{
    if (is_array($locals) && count($locals)) {
        extract($locals, EXTR_SKIP);
    }

    if (($view_root = config('views_root')) == null) {
        error(500, '[views_root] is not set');
    }

    $path = basename($view);
    $view = preg_replace('/'.$path.'$/', "_{$path}", $view);
    $view = "{$view_root}/{$view}.html.php";

    if (is_file($view)) {
        ob_start();
        require $view;

        return ob_get_clean();
    }

    error(500, "partial [{$view}] not found");

    return '';
}

function content($value = null)
{
    return stash('$content$', $value);
}

function render($view, $locals = null, $layout = null)
{
    if (is_array($locals) && count($locals)) {
        extract($locals, EXTR_SKIP);
    }

    if (($view_root = config('views_root')) == null) {
        error(500, '[views_root] is not set');
    }

    ob_start();
    include "{$view_root}/{$view}.html.php";
    content(trim(ob_get_clean()));

    if ($layout !== false) {
        if ($layout == null) {
            $layout = config('views_layout');
            $layout = ($layout == null) ? 'layout' : $layout;
        }

        $layout = "{$view_root}/{$layout}.html.php";

        header('Content-type: text/html; charset=utf-8');

        ob_start();
        require $layout;
        echo trim(ob_get_clean());
    } else {
        echo content();
    }
}

function json($obj, $code = 200)
{
    header('Content-type: application/json', true, $code);
    echo json_encode($obj);
    exit;
}

function condition()
{
    static $cb_map = [];

    $argv = func_get_args();
    $argc = count($argv);

    if (! $argc) {
        error(500, 'bad call to condition()');
    }

    $name = array_shift($argv);
    $argc = $argc - 1;

    if (! $argc && is_callable($cb_map[$name])) {
        return call_user_func($cb_map[$name]);
    }

    if (is_callable($argv[0])) {
        return ($cb_map[$name] = $argv[0]);
    }

    if (is_callable($cb_map[$name])) {
        return call_user_func_array($cb_map[$name], $argv);
    }

    error(500, 'condition ['.$name.'] is undefined');
}

function middleware($cb_or_path = null)
{
    static $cb_map = [];

    if ($cb_or_path == null || is_string($cb_or_path)) {
        foreach ($cb_map as $cb) {
            call_user_func($cb, $cb_or_path);
        }
    } else {
        array_push($cb_map, $cb_or_path);
    }
}

function filter($sym, $cb_or_val = null)
{
    static $cb_map = [];

    if (is_callable($cb_or_val)) {
        $cb_map[$sym] = $cb_or_val;
        return;
    }

    if (is_array($sym) && count($sym) > 0) {
        foreach ($sym as $s) {
            $s = substr($s, 1);
            if (isset($cb_map[$s]) && isset($cb_or_val[$s])) {
                call_user_func($cb_map[$s], $cb_or_val[$s]);
            }
        }
        return;
    }

    error(500, 'bad call to filter()');
}

function route_to_regex($route)
{
    $route = preg_replace_callback('@:[\w]+@i', function ($matches) {
        $token = str_replace(':', '', $matches[0]);
        return '(?P<'.$token.'>[a-z0-9_\0-\.]+)';
    }, $route);

    return '@^'.rtrim($route, '/').'$@i';
}

function route($method, $pattern, $callback = null)
{
    static $route_map = ['GET' => [], 'POST' => []];

    $method = strtoupper($method);
    $methods = array_keys($route_map);

    if (! in_array($method, $methods)) {
        error(500, 'Only '.implode(', ', $methods).' are supported');
    }

    if ($callback !== null) {
        $route_map[$method][$pattern] = ['xp' => route_to_regex($pattern), 'cb' => $callback];
    } else {
        foreach ($route_map[$method] as $pat => $obj) {
            if (! preg_match($obj['xp'], $pattern, $vals)) {
                continue;
            }

            middleware($pattern);

            array_shift($vals);
            preg_match_all('@:([\w]+)@', $pat, $keys, PREG_PATTERN_ORDER);

            $keys = array_shift($keys);
            $argv = [];

            foreach ($keys as $index => $id) {
                $id = substr($id, 1);

                if (isset($vals[$id])) {
                    array_push($argv, trim(urldecode($vals[$id])));
                }
            }

            if (count($keys)) {
                filter(array_values($keys), $vals);
            }

            if (is_callable($obj['cb'])) {
                call_user_func_array($obj['cb'], $argv);
            }

            break;
        }
    }
}

function get($path, $cb)
{
    route('GET', $path, $cb);
}

function post($path, $cb)
{
    route('POST', $path, $cb);
}

function flash($key, $msg = null, $now = false)
{
    static $x = [],
         $f = null;

    $f = (config('cookies.flash') ? config('cookies.flash') : '_F');
    $c = get_cookie($f);
    $c = $c ? json_decode($c, true) : [];

    if ($msg == null) {
        if (isset($c[$key])) {
            $x[$key] = $c[$key];
            unset($c[$key]);
            set_cookie($f, json_encode($c));
        }

        return (isset($x[$key]) ? $x[$key] : null);
    }

    if (! $now) {
        $c[$key] = $msg;
        set_cookie($f, json_encode($c));
    }

    $x[$key] = $msg;
}

function listen()
{
    $path = $_SERVER['REQUEST_URI'];

    if (site_url() !== null) {
        $path = preg_replace('@^'.preg_quote(site_path()).'@', '', $path);
    }

    $parts = preg_split('/\?/', $path, -1, PREG_SPLIT_NO_EMPTY);
    $uri = trim($parts[0], '/');

    if ($uri == 'index.php' || $uri == '') {
        $uri = 'index';
    }

    route(method(), "/{$uri}");
}
