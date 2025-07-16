<?php
/**
 * Template pour l'affichage du contenu des articles
 *
 * @package Le Margo
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php
		if (is_singular()) :
			the_title('<h1 class="entry-title">', '</h1>');
		else :
			the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
		endif;

		if ('post' === get_post_type()) :
			?>
			<div class="entry-meta">
				<span class="posted-on">
					<?php echo esc_html__('Publié le', 'le-margo'); ?> 
					<time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
				</span>
				<span class="byline">
					<?php echo esc_html__('par', 'le-margo'); ?> 
					<span class="author vcard"><?php the_author(); ?></span>
				</span>
			</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php if (has_post_thumbnail() && !is_singular()) : ?>
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail('medium_large'); ?>
			</a>
		</div>
	<?php endif; ?>

	<div class="entry-content">
		<?php
		if (is_singular()) :
			the_content(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__('Continuer la lecture<span class="screen-reader-text"> "%s"</span>', 'le-margo'),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post(get_the_title())
				)
			);

			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__('Pages:', 'le-margo'),
					'after'  => '</div>',
				)
			);
		else :
			the_excerpt();
			?>
			<a href="<?php the_permalink(); ?>" class="btn btn-secondary"><?php echo esc_html__('Lire la suite', 'le-margo'); ?></a>
		<?php
		endif;
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php if (!is_singular()) : ?>
			<?php
			$categories_list = get_the_category_list(esc_html__(', ', 'le-margo'));
			if ($categories_list) :
				?>
				<span class="cat-links">
					<?php echo esc_html__('Catégories:', 'le-margo'); ?> <?php echo $categories_list; ?>
				</span>
			<?php endif; ?>
			
			<?php
			$tags_list = get_the_tag_list('', esc_html_x(', ', 'list item separator', 'le-margo'));
			if ($tags_list) :
				?>
				<span class="tags-links">
					<?php echo esc_html__('Tags:', 'le-margo'); ?> <?php echo $tags_list; ?>
				</span>
			<?php endif; ?>
		<?php endif; ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> --> 