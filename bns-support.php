<?php
/*
Plugin Name: BNS Support
Plugin URI: http://buynowshop.com/plugins/
Description: Simple display of useful support information in the sidebar. Easy to copy and paste details, such as: the blog name; WordPress version; and name of Theme installed. Help for those that help. The information is only viewable by logged-in readers; and, by optional default, the blog administator(s) only.
Version: 0.1
Author: Edward Caissie
Author URI: http://edwardcaissie.com/
*/

global $wp_version;
$exit_message = 'BNS Support requires WordPress version 2.8 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please Update!</a>';
if (version_compare($wp_version, "2.8", "<")) {
	exit ($exit_message);
}

/* Add BNS Support style sheet */
add_action( 'wp_head', 'add_BNS_Support_Header_Code' );

function add_BNS_Support_Header_Code() {
  echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('url') . '/wp-content/plugins/bns-support/bns-support-style.css" />' . "\n";
}

/* Add our function to the widgets_init hook. */
add_action( 'widgets_init', 'load_my_bns_support_widget' );

/* Function that registers our widget. */
function load_my_bns_support_widget() {
	register_widget( 'BNS_Support_Widget' );
}

class BNS_Support_Widget extends WP_Widget {

	function BNS_Support_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bns-support', 'description' => __('Widget to display and share common helpful support details.') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 400, 'height' => 350, 'id_base' => 'bns-support' );

		/* Create the widget. */
		$this->WP_Widget( 'bns-support', 'BNS Support', $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );

		/* User-selected settings. */
		$title      = apply_filters('widget_title', $instance['title'] );
		$blog_admin = $instance['blog_admin'];
		
    global $current_user;
    if ( (is_user_logged_in()) ) { /* Must be logged in */
    
      if (( !$blog_admin ) || ( $current_user->user_level == '10' )) {

    		/* Before widget (defined by themes). */
    		echo $before_widget;

        echo '<div class="bns-support">'; /* CSS wrapper */

    		/* Title of widget (before and after defined by themes). */
    		if ( $title )
    			echo $before_title . $title . $after_title;

        /* Start - Display support information */
        
          /* ---- Blog URL ---- */
          echo 'URL: ' . get_bloginfo('url') . '<br />';

          /* ---- WordPress Version ---- */
          global $wp_version;
          echo 'WordPress Version: ' . $wp_version . '<br />';

          /* ---- Child Theme with Version and Parent Theme with Version ---- */
          $theme_version = ''; /* Clear variable */
          /* Get details of the theme / child theme */
          $blog_css_url = get_stylesheet_directory() . '/style.css';
          $my_theme_data = get_theme_data($blog_css_url);
          $parent_blog_css_url = get_template_directory() . '/style.css';
          $parent_theme_data = get_theme_data($parent_blog_css_url);
          /* Create and append to string to be displayed */
          $theme_version .= $my_theme_data['Name'] . ' v' . $my_theme_data['Version'];
          if ($blog_css_url != $parent_blog_css_url) {
            $theme_version .= ' a child of the ' . $parent_theme_data['Name'] . ' theme v' . $parent_theme_data['Version'];
          }
          /* Display string */
          echo 'Theme: ' . $theme_version . '<br />';
          
          /* ---- Current User Level ---- */
          echo 'Current User Level: ' . $current_user->user_level;

          /* ---- Active Plugins ---- */
          /* Code to be added? - see Viper007Bond's plugin */

       		/* End - Display support information */
       		
       		echo '<h6>Compliments of <a href="http://buynowshop.com/wordpress-services" target="_blank">WordPress Services</a> at <a href="http://buynowshop.com" target="_blank">BuyNowShop.com</a></h6>';

      		/* After widget (defined by themes). */
      		echo $after_widget;
      		
          echo '</div> <!-- .bns-support -->'; /* end CSS wrapper */
      		
      } /* BA */
    } /* Logged in */
  }

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title']      = strip_tags( $new_instance['title'] );
		$instance['blog_admin'] = $new_instance['blog_admin'];

		return $instance;
	}
	
	function form( $instance ) {
		/* Set up some default widget settings. */
		$defaults = array(
				'title'       => get_bloginfo('name'),
				'blog_admin'  => true,
			);
		$instance = wp_parse_args( (array) $instance, $defaults );

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		
    <p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['blog_admin'], true ); ?> id="<?php echo $this->get_field_id( 'blog_admin' ); ?>" name="<?php echo $this->get_field_name( 'blog_admin' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'blog_admin' ); ?>"><?php _e('Only Show Administrators?'); ?></label>
		</p>

  <?php
	}

}
?>