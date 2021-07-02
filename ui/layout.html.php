<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo(isset($title) ? _h($title) : 'Home').' | '.config('blog_title');?></title>
    <link rel="stylesheet" href="<?php echo site_url('ui/assets/css/bulma.min.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?php echo site_url('ui/assets/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/Swaagie/prismjs-monokai@master/prism-monokai.css"/>
  </head>
  <body>
    <nav class="navbar">
      <div class="container">
        <div class="navbar-brand">
          <a class="navbar-item is-uppercase has-text-weight-bold" href="<?php echo site_url(); ?>"><?php echo config('blog_title'); ?></a>
          <span class="navbar-burger burger" data-target="navbarMenu">
            <span></span>
            <span></span>
            <span></span>
          </span>
        </div>
        <div id="navbarMenu" class="navbar-menu">
          <div class="navbar-end">
            <a class="navbar-item" href="<?php echo site_url(); ?>">Home</a>
            <?php foreach (get_static_pages() as $name): ?>
            <a class="navbar-item" href="<?php echo site_url("static/$name"); ?>"><?php echo ucwords($name); ?></a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </nav>
    <section class="hero is-info is-medium is-bold">
      <div class="hero-body">
        <div class="container has-text-centered">
          <h1 class="title"><?php echo config('blog_description');?></h1>
        </div>
      </div>
    </section>
    <?php echo content();?>
    <footer class="footer">
      <div class="container">
        <div class="content has-text-centered">
          <p>
            <?php echo config('blog_authorbio');?>
            <br>
            Made with <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="11" height="11" viewBox="0 0 16 16">
              <path fill="#f14668" d="M11.8 1c-1.682 0-3.129 1.368-3.799 2.797-0.671-1.429-2.118-2.797-3.8-2.797-2.318 0-4.2 1.882-4.2 4.2 0 4.716 4.758 5.953 8 10.616 3.065-4.634 8-6.050 8-10.616 0-2.319-1.882-4.2-4.2-4.2z">
              </path>
              </svg> using <a href="https://github.com/esyede/oioioi" target="_blank">oioioi</a>.
            </p>
          </div>
        </div>
      </footer>
      <script src="https://cdn.jsdelivr.net/npm/prismjs@1.20.0/components/prism-core.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/prismjs@1.20.0/plugins/autoloader/prism-autoloader.min.js"></script>
      <script type="text/javascript">
      (function() {
      var burger = document.querySelector('.burger');
      var menu = document.querySelector('#'+burger.dataset.target);
      burger.addEventListener('click', function () {
      burger.classList.toggle('is-active');
      menu.classList.toggle('is-active');
      });
      })();
      </script>
    </body>
  </html>