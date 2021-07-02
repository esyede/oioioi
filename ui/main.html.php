<div class="container">
	<section class="articles">
		<div class="column is-10 is-offset-1">
			<?php foreach ($posts as $post):?>
			<div class="card article">
				<div class="card-content">
					<div class="media">
						<div class="media-center">
							<img src="<?php echo site_url('ui/assets/images/author.svg'); ?>" class="author-image has-background-white" alt="Author">
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
						<?php echo read_more($post->body); ?>
					</div>
				</div>
				<footer class="card-footer">
					<a class="card-footer-item is-uppercase has-text-weight-bold" href="<?php echo $post->url; ?>">Read more &rarr;</a>
				</footer>
			</div>
			<?php endforeach;?>
		</div>
	</section>
</div>
<nav class="pagination is-rounded" role="navigation" aria-label="pagination">
	<div class="column is-10 is-offset-1">
		<?php if ($has_pagination['prev']):?>
		<a class="pagination button is-info is-rounded is-pulled-left" href="?page=<?php echo $page - 1;?>">&larr; Newer</a>
		<?php endif;?>
		<?php if ($has_pagination['next']):?>
		<a class="pagination button is-info is-rounded is-pulled-right" href="?page=<?php echo $page + 1;?>">Older &rarr;</a>
		<?php endif;?>
	</div>
</nav>