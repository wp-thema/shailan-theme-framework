<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part('entry', 'header'); ?>
	<div class="entry-content">
			<?php the_content( stf_more( ) ); ?>
			
			<?php if( is_single() ) { stf_related_posts(); } ?>
			
	</div><!-- .entry-content -->
	<?php get_template_part('entry', 'footer'); ?>
	
</div><!-- #post-<?php the_ID(); ?> -->

<?php stf_comments(); ?>