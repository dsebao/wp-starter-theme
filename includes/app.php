<?php

//Related config and functions of the Website



function add_scripts(){
    //Add Global CSS
    wp_enqueue_style('bootstrap', get_template_directory_uri() . "/assets/vendor/bootstrap/css/bootstrap.min.css");

    //Add jQuery
    if (!is_admin()){
        wp_deregister_script('jquery');
        wp_register_script('jquery', get_template_directory_uri() . '/assets/vendor/jquery/jquery.min.js');
        wp_enqueue_script('jquery');
    }

    wp_enqueue_script('validator-js', get_template_directory_uri() . "/src/vendor/validator/validator.js");
    wp_enqueue_script('popper-js', get_template_directory_uri() . "/src/vendor/popper/popper.min.js");
    wp_enqueue_script('bootstrap-js', get_template_directory_uri() . "/src/vendor/bootstrap/js/bootstrap.min.js");

    wp_enqueue_script('app-js', get_template_directory_uri() . "/src/js/app.js");

    wp_enqueue_style('app', get_template_directory_uri() . "/src/css/style.css?");

    $paramsLogin = array(
        'ajaxurl' => admin_url('admin-ajax.php'),
    );
    wp_localize_script('app-js','jsvar',$paramsLogin);
}
add_action( 'wp_enqueue_scripts', 'add_scripts' );

/*
Add images sizes
*/
//add_image_size('customsize', 650, 260, true);

/* 
    Add post thumbnail
*/
add_theme_support('post-thumbnails', array('post','page'));


/* 
    Custom nav menu
*/
function register_my_menus() {
  register_nav_menus(
    array(
      'menu' => 'Menu Principal',
    )
  );
}
add_theme_support( 'menus' );
add_action( 'init', 'register_my_menus' );

/**
 * Send Templated notifications
 *
 * @param [string] $subject The main subject line
 * @param [string] $content The content in HTML formated
 * @param [string] $email
 * @return boolean
 */
function sendNotification($subject,$content,$email){
	$thebody = $GLOBALS['emailtemplate'];

    $tpl = str_replace('{{content}}', $content, $thebody);
    $tpl = str_replace('{{subject}}', $subject, $tpl);

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    $headers .= 'From: '.get_bloginfo('name').' <'.$GLOBALS['notificationmail'].'>' . "\r\n" .
    'Reply-To: ' .$GLOBALS['notificationmail'] . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    
    $sent = wp_mail($email, $subject, $tpl, $headers);
    if($sent){
    	return $sent;
    } else {
    	return false;
    }  
}



/**
 *
 * Function that enqueue custom login style
 *
 */
function my_login_stylesheet() {
    wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/assets/css/login.css' );
}
add_action( 'login_enqueue_scripts', 'my_login_stylesheet');




/**
 *  Load Footer scripts
 */
add_action('wp_footer', 'footerScripts');
function footerScripts() { ?>
<?php }
?>