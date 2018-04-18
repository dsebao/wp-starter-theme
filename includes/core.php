<?php

/* Add jQuery */
function agregar_js() {
	if (!is_admin()) {
		wp_deregister_script('jquery');
		wp_register_script('jquery', 'https://code.jquery.com/jquery-3.2.1.min.js');
		wp_enqueue_script('jquery');
	}
}
add_action('wp_enqueue_scripts', 'agregar_js');


/* Enqueue Scripts */
function foot_js(){
        echo '
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        ';
}
add_action('wp_footer', 'foot_js'); 


/* fix for browsers */
function wpfme_IEhtml5_shim () {
    global $is_IE;
    if ($is_IE)
    echo '<!--[if lt IE 9]><script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv.min.js"></script><![endif]-->';
    echo '<!--[if (gte IE 6)&(lte IE 8)]><script src="//cdnjs.cloudflare.com/ajax/libs/selectivizr/1.0.2/selectivizr-min.js"></script><![endif]-->';
}
add_action('wp_head', 'wpfme_IEhtml5_shim');


/* Cleaner Dashboard */
function disable_default_dashboard_widgets() {
	remove_meta_box('dashboard_recent_comments', 'dashboard', 'core');
	remove_meta_box('dashboard_incoming_links', 'dashboard', 'core');
	remove_meta_box('dashboard_plugins', 'dashboard', 'core');
	remove_meta_box('dashboard_quick_press', 'dashboard', 'core');
	remove_meta_box('dashboard_primary', 'dashboard', 'core');
	remove_meta_box('dashboard_secondary', 'dashboard', 'core');
	remove_meta_box('dashboard_recent_drafts', 'dashboard', 'core');
	//remove_meta_box('dashboard_right_now', 'dashboard', 'core');
}
add_action('admin_menu', 'disable_default_dashboard_widgets');


/* Remove wp tag in header */  
remove_action('wp_head', 'wp_generator');


/* Remove links in menu */
function remove_menus () {
    global $menu;
	$restricted = array(
        __('Links'),
	);  
	end ($menu);
	while (prev($menu)){
		$value = explode(' ',$menu[key($menu)][0]);
		if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
	}
}
add_action('admin_menu', 'remove_menus');


/* Custom Excerpt */
class Excerpt {
    public static $length = 55;// Default length (by WordPress)
    public static $types = array(// So you can call: my_excerpt('short');
        'short' => 25,
        'regular' => 55,
        'long' => 100
    );
    public static function length($new_length = 55) {
        Excerpt::$length = $new_length;
        add_filter('excerpt_length', 'Excerpt::new_length');
        Excerpt::output();
    }
    public static function new_length() {
        if( isset(Excerpt::$types[Excerpt::$length]))
            return Excerpt::$types[Excerpt::$length];
        else
            return Excerpt::$length;
    }
    public static function output(){
        the_excerpt();
    }
}

function my_excerpt($length = 55) {
    Excerpt::length($length);
}

//conteo de vistas
function getPostViews($postID){
    $count_key = '_views';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0";
    }
    return ' ('.$count.')';
}

function setPostViews($postID) {
    $count_key = '_views';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    } else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

// Add it to a column in WP-Admin
add_filter('manage_posts_columns', 'posts_column_views');
add_action('manage_posts_custom_column', 'posts_custom_column_views',5,2);
function posts_column_views($defaults){
    $defaults['_views'] = 'Vistas';
    return $defaults;
}
function posts_custom_column_views($column_name, $id){
  if($column_name === 'ec_views'){
    echo getPostViews(get_the_ID());
  }
}


/* Pagination Boostrap */
function wp_bootstrap_pagination( $args = array() ) {
    $defaults = array(
        'range'           => 4,
        'custom_query'    => FALSE,
        'previous_string' => 'Anterior',
        'next_string'     => 'Siguiente',
        'before_output'   => '<nav aria-label="Paginacion"><ul class="pagination">',
        'after_output'    => '</ul></nav>'
    );
    
    $args = wp_parse_args( 
        $args, 
        apply_filters( 'wp_bootstrap_pagination_defaults', $defaults )
    );
    
    $args['range'] = (int) $args['range'] - 1;
    if ( !$args['custom_query'] )
        $args['custom_query'] = @$GLOBALS['wp_query'];
    $count = (int) $args['custom_query']->max_num_pages;
    $page  = intval( get_query_var( 'paged' ) );
    $ceil  = ceil( $args['range'] / 2 );
    
    if ( $count <= 1 )
        return FALSE;
    
    if ( !$page )
        $page = 1;
    
    if ( $count > $args['range'] ) {
        if ( $page <= $args['range'] ) {
            $min = 1;
            $max = $args['range'] + 1;
        } elseif ( $page >= ($count - $ceil) ) {
            $min = $count - $args['range'];
            $max = $count;
        } elseif ( $page >= $args['range'] && $page < ($count - $ceil) ) {
            $min = $page - $ceil;
            $max = $page + $ceil;
        }
    } else {
        $min = 1;
        $max = $count;
    }
    
    $echo = '';
    $previous = intval($page) - 1;
    $previous = esc_attr( get_pagenum_link($previous) );
    
    $firstpage = esc_attr( get_pagenum_link(1) );
    if ( $firstpage && (1 != $page) )
        $echo .= '<li class="page-item"><a class="page-link" href="' . $firstpage . '">' . __( 'Primera', 'text-domain' ) . '</a></li>';
    if ( $previous && (1 != $page) )
        $echo .= '<li class="page-item"><a class="page-link" href="' . $previous . '" title="' . __( 'Anterior', 'text-domain') . '"><span aria-hidden="true">&laquo;</span><span class="sr-only">Anterior</span></a></li>';
    
    if ( !empty($min) && !empty($max) ) {
        for( $i = $min; $i <= $max; $i++ ) {
            if ($page == $i) {
                $echo .= '<li class="page-item active"><a class="page-link" href="#">' . str_pad( (int)$i, 2, '0', STR_PAD_LEFT ) . '</a></li>';
            } else {
                $echo .= sprintf( '<li class="page-item"><a class="page-link" href="%s">%002d</a></li>', esc_attr( get_pagenum_link($i) ), $i );
            }
        }
    }
    
    $next = intval($page) + 1;
    $next = esc_attr( get_pagenum_link($next) );
    if ($next && ($count != $page) )
        $echo .= '<li class="page-item"><a class="page-link" href="' . $next . '" title="' . __( 'Siguiente', 'text-domain') . '"><span aria-hidden="true">&raquo;</span><span class="sr-only">Siguiente</span></a></li>';
    
    $lastpage = esc_attr( get_pagenum_link($count) );
    if ( $lastpage ) {
        $echo .= '<li class="page-item next"><a class="page-link" href="' . $lastpage . '">' . __( 'Última', 'text-domain' ) . '</a></li>';
    }
    if ( isset($echo) )
        echo $args['before_output'] . $echo . $args['after_output'];
}



/* Remove pages from search*/
function remove_pages_from_search() {
    global $wp_post_types;
    $wp_post_types['page']->exclude_from_search = true;
}
add_action('init', 'remove_pages_from_search');


/* Force medium size image to crop */
if(false === get_option('medium_crop')) {
    add_option('medium_crop', '1');
} else {
    update_option('medium_crop', '1');
}


/* Funtion to call images in a post */
function my_image($postid=0, $size='thumbnail') { //it can be thumbnail or full
    if ($postid<1){
        $postid = get_the_ID();
    }
    if(has_post_thumbnail($postid)){
        $imgpost = wp_get_attachment_image_src(get_post_thumbnail_id($postid), $size);
        return $imgpost[0];
    }
    elseif ($images = get_children(array(
        'post_parent' => $postid,
        'post_type' => 'attachment',
        'numberposts' => '1',
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'post_mime_type' => 'image',)))
    foreach($images as $image) {
        $thumbnail=wp_get_attachment_image_src($image->ID, $size);
        return $thumbnail[0];
    } else {
        global $post, $posts;
        $first_img = '';
        ob_start();
        ob_end_clean();
        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
        $first_img = $matches [1] [0];
        return $first_img;
    }
}


/* Add image sizes and post thumbnail */
add_theme_support('post-thumbnails', array('post','page'));
//add_image_size('customsize', 650, 260, true);


/* Add images in feed rss */
function rss_add_enclosure() {
    global $post;
    if( has_post_thumbnail() ) {
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail' );
        $url = $thumb['0'];
        echo "\n";
    }
}
add_action('rss_item',  'rss_add_enclosure');
add_action('rss2_item', 'rss_add_enclosure');


/* Remove wordpress in mail subject */
function res_fromname($email){
    $wpfrom = get_option('blogname');
    return $wpfrom;
}
add_filter('wp_mail_from_name', 'res_fromname');





/* Custom nav menus*/
function register_my_menus() {
  register_nav_menus(
    array(
      'menu' => 'Menu Principal',
    )
  );
}
add_theme_support( 'menus' );
add_action( 'init', 'register_my_menus' );

/* remove wordpress logo and menu admin bar */
function remove_admin_bar_links() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
    $wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
    $wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
    $wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
    $wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
    $wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
    $wp_admin_bar->remove_menu('updates');          // Remove the updates link
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );


/* Redirect admins to the dashboard and other users elsewhere */
add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );
function my_login_redirect( $redirect_to, $request, $user ) {
    // Is there a user?
    if (isset($user->roles) && is_array( $user->roles ) ) {
        // Is it an administrator?
        if ( in_array( 'administrator', $user->roles ) )
            return home_url( '/wp-admin/' );
        else
            return home_url();
            // return get_permalink( 83 );
    }
}

// Disable use XML-RPC
add_filter( 'xmlrpc_enabled', '__return_false' );

// Disable X-Pingback to header
add_filter( 'wp_headers', 'disable_x_pingback' );
function disable_x_pingback( $headers ) {
    unset( $headers['X-Pingback'] );
return $headers;
}

remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'start_post_rel_link' );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'adjacent_posts_rel_link' );

/* Remove Admin bar */
//show_admin_bar(false);


/* GA */
add_action('wp_footer', 'ga');
function ga() { ?>
<?php }
?>