# oioioi
Super simple markdown blog that powers my website.
It's super simple, so don't epect much :)


## Requirements
*  PHP 5.4 or newer



## Installation
  - Download the file from
[release page](https://github.com/esyede/oioioi/releases)
and drop to your webserver.
  - Copy `sample.htaccess` file to `.htaccess`
  - Edit the `config.php` file if needed. That's it!


## Demo

Demo available here: [https://blog.esyede.my.id/](https://blog.esyede.my.id/)


## Adding Posts

To add a new post, just create a new file inside the `data/articles` directory
with this format: `yyyy-mm-dd_post-title.md`
for example `2021-07-03_welcome-to-my-blog.md`

And the content should be started with `#` (markdown's H1 tag) as it's used
to determine the post title.


## Adding Static Pages

To add a new static page, just create a new file inside the `data/static` directory
with this format: `yyyy-mm-dd_pagename.md`
for example `2021-07-03_about.md`

There is no need to starte your content with `#` as static page will directly parsed.

## Customising the UI

You can edit the template files to customize the UI, it's located in `ui` folder



### License

This code is licensed under the [MIT License](http://opensource.org/licenses/MIT)
