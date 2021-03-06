<?php 

/** SHAILAN THEME FRAMEWORK 
 File 		: shailan-templates.php
 Author		: Matt Say
 Author URL	: http://shailan.com
 Version	: 1.0
 Contact	: metinsaylan (at) gmail (dot) com
*/

global $stf;
global $theme_data;

/**
 * Returns theme info if exists. 
 *
 * @since 1.0.0
 * @uses get_theme_data() to get theme information.
 */
function themeinfo($key){
	global $theme_data;
	
	if(empty($theme_data)){
		$theme_data = get_theme_data( get_stylesheet_directory() . '/style.css' );	
	}
	
	if(array_key_exists($key, $theme_data)){
		return $theme_data[$key];
	} else {
		trigger_error("Key '" . $key . "' for themeinfo doesn't exist"  , E_USER_ERROR);
		return FALSE;
	}
}

function stf_css( $name, $args = null, $echo = true ){ stf_style( $name, $args, $echo ); }
function stf_style( $name, $args = null, $echo = true ){
	$defaults = array(
		'id' => '',
		'media' => 'all'
	);
	$args = wp_parse_args( $args, $defaults );
	extract($args);
	
	if($echo){
		echo "<link rel=\"stylesheet\" id=\"$id\"  href=\"" . STF_URL . "css/$name.css\" type=\"text/css\" media=\"$media\" />\n ";
	} else {
		return "<link rel=\"stylesheet\" id=\"$id\"  href=\"" . STF_URL . "css/$name.css\" type=\"text/css\" media=\"$media\" />\n ";
	}
}

/**
 * An extension for dynamic_sidebar(). If no widgets exist it shows default widgets
 * given by an array or a callback.
 *
 * @since 1.0.0
 * @uses the_widget() to show widgets.
 */
function stf_widgets( $id, $default_widgets = array(), $callback = null ){
	if(!dynamic_sidebar($id)){ // If widget area has no widgets
		if(is_array($default_widgets)){
			foreach($default_widgets as $widget_callback)
				the_widget($widget_callback);
		} elseif (!empty($default_widgets)){
			the_widget($default_widgets);
		} elseif (null != $callback){
			call_user_func($callback);
		}
	}
}

function stf_entry_title(){

	if( is_single() || is_page() ){ 
		$titleblock = "h1";
	} elseif( is_home() ) {
		$titleblock = "h2";
	} elseif( is_archive() ){
		$titleblock = "h3";
	} else { 
		$titleblock = "h2";
	}
	
	?><!-- Entry Title -->
	<<?php echo $titleblock; ?> class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'stf' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark" <?php if( 0 ): ?> xref="post#<?php the_ID(); ?>" class="ajax"<?php endif; ?>><?php the_title(); ?></a></<?php echo $titleblock; ?>>
	<!-- [End] Entry Title -->
	
	<?php

}

/**
 * Returns entry header. Entry header can be changed via options.
 *
 * @since 1.0.0
 * @uses do_shortcode() to parse shortcodes in header.
 */
function stf_entry_header_meta(){
	$meta = stf_get_setting('stf_entry_header_meta');
	if(FALSE !== $meta){
		echo "\n\t<!-- Entry Header Meta -->";
		echo "\n\t<div class=\"entry-meta entry-meta-header\">\n\t\t";
			echo do_shortcode( stripslashes($meta) );
		echo "\n\t</div>";
		echo "\n\t<!-- [End] Entry Header Meta -->\n";
	}
}

/**
 * Returns entry footer. Entry footer can be changed via options.
 *
 * @since 1.0.0
 * @uses do_shortcode() to parse shortcodes in the footer.
 */
function stf_entry_footer_meta(){
	$meta = stf_get_setting('stf_entry_footer_meta');
	if(FALSE !== $meta){
		/*echo "\n\t<!-- Entry Footer Meta -->";
		echo "\n\t<div class=\"entry-meta entry-meta-footer\">\n\t\t";*/
		echo do_shortcode( stripslashes($meta) );
		/*echo "\n\t</div>";
		echo "\n\t<!-- [End] Entry Footer Meta -->\n";*/
	}
}


/**
 * Returns entry footer for short post formats. (aside, link, quote)
 *
 * @since 1.0.0
 * @uses do_shortcode() to parse shortcodes in the footer.
 */
function stf_entry_short_meta(){
	$meta = stf_get_setting('stf_entry_short_meta');
	if(FALSE !== $meta){
		echo do_shortcode( stripslashes($meta) );
	}
}

/**
 * Returns entry thumbnail. Size can be changed via options panel.
 *
 * @since 1.0.0
 * @uses get_the_title(), has_post_thumbnail(), get_permalink(), the_title_attribute(), get_the_post_thumbnail()
 */
function stf_entry_thumbnail( $size = 'thumbnail', $args = null ){
	global $post;
	
	extract( wp_parse_args( $args, array(
		'title' => the_title_attribute( array('echo' => 0 ) ), 
		'alt' => the_title_attribute( array('echo' => 0 ) ),
		'class' => 'entry-thumbnail',
		'default' => ''
	) ) ) ;
	
	$thumb_attr = array(
		'alt'	=> trim( strip_tags( $title ) ),
		'title'	=> trim( strip_tags( $title ) ),
	);
	
	if( function_exists('has_post_thumbnail') && has_post_thumbnail( $post->ID ) ) {
		echo '<div class="' . $class . ' thumb-wrap-' . $size . '"><a href="'.get_permalink( $post->ID ).'" title="' . the_title_attribute( array('echo' => 0 ) ) . '">';
		echo 	get_the_post_thumbnail( $post->ID, $size, $thumb_attr );
		echo '</a></div>';
	} else {
		echo '<!-- no thumbnail -->';
	}
}

/**
 * Returns entry pages navigation.
 *
 * @since 1.0.0
 * @uses wp_link_pages()
 */
function stf_entry_pages_navigation(){
	wp_link_pages( array(
		'before'		=> '<div class="entry-pages-wrap"><div class="entry-pages"><span class="label">' . __('Pages:') . '</span>',
		'after'			=> '</div></div>',
		'link_before'	=> '<span class="page-number">',
		'link_after'	=> '</span>'
	) ); 
}
function stf_entry_pages(){  stf_entry_pages_navigation(); }

function stf_posts( $number_of_posts = 0, $template = '',  $reset = false, $pagination = false ){
	global $wp_query, $posts_displayed, $post_index;
	
	$posts_displayed = (array) $posts_displayed;
	
	// Reset to default query if needed
	if($reset){ wp_reset_query(); }
	
	// Change number of posts if needed
	if( 0 != $number_of_posts ){
		query_posts(
			array_merge(
				array('posts_per_page' => $number_of_posts),
				$wp_query->query
			)
		); }
		
	if( strpos ( $template , 'loop' ) ){
		trigger_error ( 'Old template file include' );
	}
	
	// The Loop
	if ( have_posts() ) : 
		$post_index = 1; 
		while ( have_posts() ) : the_post();
			$posts_displayed[] = get_the_ID();
			get_template_part( 'app/templates/' . $template , get_post_format() );
			$post_index = $post_index + 1; 
		endwhile;
		if ($pagination) stf_pagination();
	endif;
	
}

function stf_random_posts( $number_of_posts = 5, $template = '' ){
	global $posts_displayed;

	$posts_displayed = (array) $posts_displayed;
	$sticky = get_option('sticky_posts');	
	
	// Change query
	$query_arguments = array(
		'ignore_sticky_posts'=>1,
		'post__not_in' => array_merge( $sticky, $posts_displayed ),
		'posts_per_page' => $number_of_posts,
		'orderby'=>'rand'
	);
	
	// Display them
	stf_custom_posts( $query_arguments, $template );
}

function stf_most_viewed_posts( $number_of_posts = 5, $template = '' ){
	global $posts_displayed;
	$posts_displayed = (array) $posts_displayed;

	if(function_exists('the_views')){
	// Change query
	$query_arguments = array(
	   'ignore_sticky_posts'=>1,
	   'post__not_in' => $posts_displayed,
	   'posts_per_page' => $number_of_posts,
	   'v_sortby' => 'views',
	   'v_orderby' => 'desc'
	   );
	
	// Display them
	stf_custom_posts( $query_arguments, $template ); 
	} else { stf_random_posts( $number_of_posts, $template ); }
}

function stf_most_commented_posts( $number_of_posts = 5, $template = '' ){
	global $posts_displayed;
	
	// Change query
	$query_arguments = array(
		'ignore_sticky_posts'=>1,
		'post__not_in' => $posts_displayed,
		'orderby' => 'comment_count',
		'posts_per_page' => $number_of_posts
	);
	
	// Display them
	stf_custom_posts( $query_arguments, $template );
}

function stf_custom_posts( $query_arguments, $template = '' ){

	query_posts( $query_arguments );
	// Display them
	stf_posts( 0, $template );
	// Reset to default query
	wp_reset_query();	
}



function stf_theme_footer(){
	$meta = stf_get_setting('stf_theme_footer');
	if(FALSE !== $meta){
		echo do_shortcode(stripslashes($meta));
	}
}

/**
 * Returns img tag to the image filename given. 
 *
 * @since 1.0.0
 * @uses do_shortcode() to parse shortcodes in the footer.
 * 
 * @param string $filename		filename of the image requested.
 * @param string $dimensions	dimensions of the image with an x in between. Eg: 200x200
 * @param string $classname		class attribute of the image tag
 * @param string $alt 			alternative text attribute of the image.
 */
function get_theme_image($filename, $dimensions=NULL, $classname='', $alt='', $title='' ){
	$img = '<img src="' . trailingslashit(get_bloginfo('stylesheet_directory')) . 'images/' . $filename . '"';
	
	if(!empty($dimensions)){
		$dimensions = explode('x', $dimensions); 
		$img .= ' width="'.$dimensions[0].'" height="'.$dimensions[1].'"';
	}
	
	if(!empty($classname)){ $img .= ' class="'.$classname.'"';}
	if(!empty($alt)){ $img .= ' alt="'.$alt.'"';}
	if(!empty($title)){ $img .= ' title="'.$title.'"';}
	
	$img .= ' />';
	return $img;
}

function theme_image($filename, $dimensions=NULL, $classname='', $alt='', $title = '' ){
	echo get_theme_image($filename, $dimensions, $classname, $alt, $title);
} 

function stf_comments(){
	if( "on" == stf_get_setting('enable_comments_on_home') && comments_open() && is_home() ){
		global $withcomments;
		$withcomments = true;		
		comments_template( '/inline-comments.php', true );
	} elseif ( ( is_single() || is_page() ) && comments_open() ){
		comments_template( '/comments.php', true ); 
	} else {
		//comments_template( '', true ); 
	}
}

function stf_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'comment' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>">
		<div class="arrow"></div>		
		<div class="comment-author-avatar vcard">
			<a href="<?php echo get_comment_author_url( get_comment_ID() ); ?>" rel="external nofollow" title="<?php echo comment_author(); ?>">
				<?php echo get_avatar( $comment, 50 ); ?>
			</a>
		</div><!-- .comment-author .vcard -->
		
		<div class="comment-body">
			<?php if ( $comment->comment_approved == '0' ) : ?>
				<em><?php _e( 'Your comment is awaiting moderation.', 'stf' ); ?></em>
			<br />
			<?php endif; ?>
			<div class="comment-meta commentmetadata">
			  <span class="comment-author"><?php printf( '<cite class="fn">%s</cite>', get_comment_author_link() ); ?></a></span>
			  <span class="comment-date"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><?php
					/* translators: 1: date, 2: time */
					printf( __( '<span title="at %2$s">%1$s</span>' ), get_comment_date('M j, Y'),  get_comment_time() ); ?></a></span>
				<?php edit_comment_link( __( 'edit' ), '<span class="comment-edit-link">', '</span>' );	?>
			</div><!-- .comment-meta .commentmetadata -->
			<div class="comment-text"><?php comment_text(); ?></div>
		</div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
		
		<div class="clear"></div>
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Ping:', 'stf' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'edit' ), '<span class="comment-edit-link">', '</span>' );	?></p>
	<?php
			break;
	endswitch;
}

function stf_comment_inline( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'comment' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div class="inline-comment">
			<div id="comment-<?php comment_ID(); ?>">
			<div class="arrow"></div>		
			<div class="comment-author-avatar vcard">
				<a href="<?php echo get_comment_author_url( get_comment_ID() ); ?>" rel="external nofollow" title="<?php echo comment_author(); ?>">
					<?php echo get_avatar( $comment, 50 ); ?>
				</a>
			</div><!-- .comment-author .vcard -->
			
			<div class="comment-body-inline">
				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em><?php _e( 'Your comment is awaiting moderation.', 'stf' ); ?></em>
				<?php endif; ?>
				
				<span class="comment-author"><?php printf( '<cite class="fn">%s</cite>', get_comment_author_link() ); ?></span> <span class="comment-date"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><?php
						/* translators: 1: date, 2: time */
						printf( __( '<span title="at %2$s">%1$s</span>' ), get_comment_date('M j, Y'),  get_comment_time() ); ?></a></span>
				
				<?php edit_comment_link( __( 'edit' ), ' <span class="comment-edit-link">', '</span>' );	?>
				
				<div class="comment-text"><?php comment_text(); ?></div>
				<div class="comment-meta commentmetadata">
				  <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				</div><!-- .comment-meta .commentmetadata -->

			</div>
			
			<div class="clear"></div>
			</div>
		</div>

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Ping:', 'stf' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('Edit'), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}

function stf_show_comment_form() {
	global $post, $form_visible;
	$show = ( !isset( $form_visible ) || !$form_visible ) && 'open' == $post->comment_status;
	if ( $show )
		$form_visible = true;
	return $show;
}

function stf_simple_pagination( $args = null ){
	
	extract( wp_parse_args( $args, array(
		'label_next' => __( '<span class="meta-nav">&larr;</span> Older posts' ),
		'label_previous' => __( 'Newer posts <span class="meta-nav">&rarr;</span>' )
	) ) ) ;

	if(function_exists('wp_pagenavi')) {
		wp_pagenavi(); 
	} else { ?>
		<!-- Entries navigation -->
		<div class="entry-navigation clearfix">
			<div class="nav-previous"><?php next_posts_link( $label_next ); ?></div>
			<div class="nav-next"><?php previous_posts_link( $label_previous ); ?></div>
		</div>
		<!-- [End] Entries navigation -->
		<?php
	}
}

function stf_archive_header(){
	if( is_category() ){
	
		echo "<h1 class=\"page-title\">";
		printf( __( 'Posts about <span class="alt">%s</span>' ), '<span>' . single_cat_title( '', false ) . '</span>' );
		echo "</h1>";
		
		$category_description = category_description();
		if ( ! empty( $category_description ) )
			echo '<div class="archive-meta">' . $category_description . '</div>';
			
	} elseif ( is_tag() ){
	
		echo "<h1 class=\"page-title\">";
			printf( __( 'Posts tagged <span class="alt">%s</span>' ), '<span>' . single_tag_title( '', false ) . '</span>' );
		echo "</h1>";
	
		$tag_description = tag_description();
		if ( ! empty( $tag_description ) )
			echo '<div class="archive-meta">' . $tag_description . '</div>';
			
	}
}

function stf_latest_tweet(){
	if( 'on' == stf_get_setting('stf_twitter_enabled') ) { ?>
	<div id="latest-tweet">
		<span>			
			<?php 
				$twitter_username = stf_get_setting( 'stf_twitter_username' ); 
				if($twitter_username == ''){ 
					echo "Enter your twitter username in settings to display your latest tweet.";
				} else {
					echo stf_get_latest_tweet( $twitter_username ); 
				}
			?>
		</span>
	</div>
<?php } 
}

function stf_related_posts( $number = 6 ){
	global $post, $posts_displayed, $sticky;
	
	$posts_displayed = (array) $posts_displayed;
	$sticky = get_option('sticky_posts');
	
	$tags = wp_get_post_tags( $post->ID );
	if ($tags) {
		
		$search = '';
		foreach($tags as $t){
			$search .= $t->term_id . ',';
		}
		$search = substr( $search, 0, -1); // remove last comma
	  $args=array(
		'tag__in' => $search,
		'post__not_in' => array($post->ID),
		'showposts' => $number,
		'ignore_sticky_posts'=>1
	   );
	   
		$my_query = new WP_Query($args);
		if( $my_query->have_posts() ) { 
			echo '<div id="related-posts" class="clearfix"><h4 class="">' . __('Related Posts', 'stf') . '</h4>';
			echo "<ul>";
			while ($my_query->have_posts()) : $my_query->the_post(); ?>
			  <li class="clearfix"><?php the_post_thumbnail( array(40, 40) ); ?><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
			  <?php
			endwhile;
			echo "</ul>";
			echo '</div>';
		} else {
	  
			$args = array(
				'ignore_sticky_posts'=>1,
				'post__not_in' => array_merge( $sticky, $posts_displayed ),
				'posts_per_page' => $number,
				'orderby'=>'rand'
			);
			
			$my_query = new WP_Query($args);

			if( $my_query->have_posts() ) {
				echo '<div id="related-posts" class="clearfix"><h4 class="">' . __('Related Posts', 'stf') . '</h4>';
				echo "<ul>";
				while ($my_query->have_posts()) : $my_query->the_post(); ?>
					<li class="clearfix"><?php the_post_thumbnail( array(40, 40) ); ?><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
					  <?php
				endwhile;
				echo "</ul>";
				echo '</div>';
			}
		}
	}
	
	wp_reset_postdata();
}

function _stf_deprecated_function( $function, $version, $replacement=null ) {

	do_action( 'deprecated_function_run', $function, $replacement, $version );

	// Allow plugin to filter the output error trigger
	if ( WP_DEBUG && apply_filters( 'deprecated_function_trigger_error', true ) ) {
		if ( ! is_null($replacement) )
			trigger_error( sprintf( __('%1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.'), $function, $version, $replacement ) );
		else
			trigger_error( sprintf( __('%1$s is <strong>deprecated</strong> since version %2$s with no alternative available.'), $function, $version ) );
	}
}