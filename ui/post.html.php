<div class="container">
    <section class="articles">
        <div class="column is-10 is-offset-1">
            <div class="card article">
                <div class="card-content">
                    <div class="media">
                        <div class="media-center">
                            <img src="<?php echo site_url('ui/assets/images/author.svg'); ?>" class="author-image" alt="Author">
                        </div>
                        <div class="media-content has-text-centered">
                            <p class="title article-title">
                                <a href="<?php echo $post->url; ?>"><?php echo $post->title; ?></a>
                            </p>
                            <div class="tags has-addons level-item">
                                <span class="tag is-rounded is-info">Posted</span>
                                <span class="tag is-rounded"><?php echo date('d F Y', $post->date);?></span>
                            </div>
                        </div>
                    </div>
                    <div class="content article-body">
                        <?php echo $post->body; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php if ('' !== trim(config('disqus_shortname'))):?>
    <div class="container">
        <div class="column is-10 is-offset-1">
        <div id="disqus_thread"></div>
        <script>
            var disqus_config = function () {
                this.page.url = '<?php echo $post->url; ?>';
                this.page.identifier = '<?php echo $post->title; ?>';
            };
            (function() {
                var d = document, s = d.createElement('script');
                s.src = 'https://<?php echo config('disqus_shortname'); ?>.disqus.com/embed.js';
                 s.setAttribute('data-timestamp', +new Date());
                (d.head || d.body).appendChild(s);
            })();
        </script>
        <noscript>
            Please enable JavaScript to view the
            <a href="https://disqus.com/?ref_noscript" rel="nofollow">
                comments powered by Disqus.
            </a>
        </noscript>
    </div>
    </div>
<?php endif;?>