<?php 
/*
	SHAILAN THEME FRAMEWORK 
	DEFAULT SIDEBARS & WIDGET AREAS
	___________________________________________________________________________
	                                         Author : Matt Say ( @metinsaylan )
*/

global $stf_widget_areas;

function stf_widget_area( $id, $class='' ){
	if( is_active_sidebar( $id ) ){ ?>
		<!-- <?php echo $id ?> -->
		<div id="<?php echo $id ?>" class="<?php echo $class ?>">
			<?php stf_widgets($id); ?>
		</div>
		<!-- [End] <?php echo $id ?> -->
	<?php }
}

function stf_add_widget_area( $name, $id, $description='', $default_widgets='' ){
	global $stf_widget_areas;
	
	$widget_area = array(
		'name'=>$name,
		'id'=>$id,
		'description'=>$description,
		'default_widgets'=>$default_widgets
	);
	
	array_push($stf_widget_areas, $widget_area); 
}

function stf_register_widget_area( $args ){
	global $wp_registered_sidebars;
	
	$i = count($wp_registered_sidebars) + 1;
	
	$defaults = array(
		'name' => sprintf(__('Sidebar %d'), $i ),
		'id' => "sidebar-$i",
		'description' => ''
	);
	
	$sidebar = wp_parse_args( $args, $defaults );

	register_sidebar( array(
		'name' => $sidebar['name'],
		'id' => $sidebar['id'],
		'description' => $sidebar['description'],
		'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<span class="widget-title">',
		'after_title' => '</span>',
		) );
}

function stf_register_widget_areas( ){
	global $stf_widget_areas;
	
	if(count($stf_widget_areas)==0){
		stf_default_widget_areas();
	}

	foreach( $stf_widget_areas as $widget_area ){	
	
		$args = array(
			'name' => $widget_area['name'],
			'id' => $widget_area['id'],
			'description' => $widget_area['description']
		);
		
		stf_register_widget_area( $args );
	}
}

function stf_default_widget_areas(){ 
	global $stf_widget_areas;

	$stf_widget_areas = array(
	
		array(
			'name' => 'Header top',
			'id' => 'header-top',
			'description' => 'Widgets over the header area'),
			
		array(
			'name' => 'Header bottom',
			'id' => 'header-bottom',
			'description' => 'Widgets under the header area'),		
			
		array(
			'name' => 'Billboard',
			'id' => 'billboard',
			'description' => ''),
		
		array(
			'name' => 'Content',
			'id' => 'content',
			'description' => 'You should add a "the Loop" widget here to display posts.'),
			
		array(
			'name' => 'Primary Sidebar',
			'id' => 'primary',
			'description' => ''),
			
		array(
			'name' => 'Secondary Sidebar',
			'id' => 'secondary',
			'description' => ''),
			
		array(
			'name' => 'Floating Bar',
			'id' => 'floatingbar',
			'description' => ''),

		array(
			'name' => 'Footer Column 1',
			'id' => 'footer-column-1',
			'description' => ''),
			
		array(
			'name' => 'Footer Column 2',
			'id' => 'footer-column-2',
			'description' => ''),
			
		array(
			'name' => 'Footer Column 3',
			'id' => 'footer-column-3',
			'description' => ''),
		
		array(
			'name' => 'Footer Wide',
			'id' => 'footer-wide',
			'description' => '')
	);
	
	//stf_register_widget_areas( );
	
}

add_action( 'widgets_init', 'stf_register_widget_areas');