<?php
/*
Plugin Name: Total Social Counter
Plugin URI: http://www.webdev3000.com/
Description: Combines the number of your RSS readers, twitter followers, and fans of your facebook fan page, to give an estimate of your social popularity
Author: Csaba Kissi
Version: 0.8.0
Author URI: http://www.webdev3000.com/
*/
class TSC_widget extends WP_Widget {


    /** constructor -- name this the same as the class above */
    function TSC_widget() {
        parent::WP_Widget(false, $name = 'Total Social Counter');
        $this->cacheFileName = WP_CONTENT_DIR."/cache.txt";
    }

    /** @see WP_Widget::widget -- do not rename this */
    function widget($args, $instance) {
        extract( $args );
        $title 		= apply_filters('widget_title', $instance['title']);
        $facebook_id	= $instance['facebook_id'];
        $twitter_id	= $instance['twitter_id'];
        $feedburner_id = $instance['feedburner_id'];
        require "subscriber_stats.class.php";
        $cacheFileName = $this->cacheFileName;
        //unlink($cacheFileName);

        if(file_exists($cacheFileName) && time() - filemtime($cacheFileName) < 6*60*60)
        {
            $stats = unserialize(file_get_contents($cacheFileName));
        }

        if(!$stats)
        {
            // If no cache was found, fetch the subscriber stats and create a new cache:

            $stats = new SubscriberStats(array(
                'facebookFanPageID'	=> $facebook_id,
                'feedBurnerURL'		=> $feedburner_id,
                'twitterName'		=> $twitter_id
            ));

            file_put_contents($cacheFileName,serialize($stats));
        }

        //	You can access the individual stats like this:
        //	$stats->twitter;
        //	$stats->facebook;
        //	$stats->rss;

        //	Output the markup for the stats:

        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
							<?php $stats->generate(); ?>
              <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update -- do not rename this */
    function update($new_instance, $old_instance) {
        if($new_instance != $old_instance) unlink($this->cacheFileName);
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['twitter_id'] = strip_tags($new_instance['twitter_id']);
        $instance['facebook_id'] = strip_tags($new_instance['facebook_id']);
        $instance['feedburner_id'] = strip_tags($new_instance['feedburner_id']);
        return $instance;
    }

    /** @see WP_Widget::form -- do not rename this */
    function form($instance) {

        $title 		 = esc_attr($instance['title']);
        $twitter_id  = esc_attr($instance['twitter_id']);
        $facebook_id = esc_attr($instance['facebook_id']);
        $feedburner_id = esc_attr($instance['feedburner_id']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('twitter_id'); ?>"><?php _e('Twitter ID:'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('twitter_id'); ?>" name="<?php echo $this->get_field_name('twitter_id'); ?>" type="text" value="<?php echo $twitter_id; ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('facebook_id'); ?>"><?php _e('Facebook page URL (not ID !):'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('facebook_id'); ?>" name="<?php echo $this->get_field_name('facebook_id'); ?>" type="text" value="<?php echo $facebook_id; ?>" />
        </p>
        <p>
          <label for="<?php echo $this->get_field_id('feedburner_id'); ?>"><?php _e('Feedburner URL (not ID !):'); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id('feedburner_id'); ?>" name="<?php echo $this->get_field_name('feedburner_id'); ?>" type="text" value="<?php echo $feedburner_id; ?>" />
        </p>
        <?php
    }


} // end class example_widget

function tsc_stylesheet() {
    $myStyleUrl = plugins_url('css/total-social-counter.css', __FILE__); // Respects SSL, Style.css is relative to the current file
    $myStyleFile = WP_PLUGIN_DIR . '/total-social-counter/css/total-social-counter.css';
    if ( file_exists($myStyleFile) ) {
        wp_register_style('myStyleSheets', $myStyleUrl);
        wp_enqueue_style( 'myStyleSheets');
    }
    wp_enqueue_style('jquery.tipTip', plugins_url('css/tipTip.css',__FILE__));
}
function tsc_scripts() {
   wp_register_script('jquery.tipTip', plugins_url('js/jquery.tipTip.minified.js', __FILE__), array('jquery'), '1.3');
   wp_enqueue_script('jquery.tipTip');
   wp_enqueue_script('tsc-script', plugins_url('/js/script.js',__FILE__), array('jquery'), '1.0.0');
}

add_action('widgets_init', create_function('', 'return register_widget("TSC_widget");'));
add_action('wp_print_styles', 'tsc_stylesheet');
add_action('wp_enqueue_scripts', 'tsc_scripts');
?>