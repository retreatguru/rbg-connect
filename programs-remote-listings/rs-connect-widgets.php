<?php

if (! class_exists('RS_Connect_Widget')) {
    class RS_Connect_Widget extends WP_Widget {
        public function __construct() {
            $widget_ops = array('classname' => 'RS_Connect_Widget', 'description' => __('Displays a list of programs'));
            parent::__construct('rs_programs', __('Programs'), $widget_ops);
            $this->alt_option_name = 'widget_retreat_site_programs_option';

            add_action('save_post', array($this, 'flush_widget_cache'));
            add_action('deleted_post', array($this, 'flush_widget_cache'));
            add_action('switch_theme', array($this, 'flush_widget_cache'));
        }

        public function defaults() {
            $defaults = array(
                'title'          => __('Programs'),
                'number'         => 3,
                'category'       => '',
                'show_thumbnail' => true,
                'image_w'        => 65,
                'image_h'        => 65,
                'show_excerpt'   => false,
                'excerpt_words'  => 20,
                'excerpt_more'   => __('More&nbsp;&rarr;'),
                'show_date'      => true,
                'view_all'       => __('View all programs &raquo;'),
                'show_teacher'   => true,
                'featured_only'  => false,
            );

            return $defaults;
        }

        public function widget($args, $instance) {

            global $RS_Connect;

            $instance = array_merge($this->defaults(), $instance);
            extract($instance);
            extract($args);

            $vars = null;

            if ($category) {
                $vars .= 'category='.$category;
            }
            $rs_the_programs = array_reverse((array) RS_Connect_Api::get_programs($vars));

            $count = 0;
            echo $before_widget;

            if ($title) echo $before_title.$title.$after_title;

            ?>

            <ul class="rs-programs-widget">
                <?php
                if ($rs_the_programs) {
                    foreach ($rs_the_programs as $program) {
                        if ($count < $number) {
                            $options = get_option('rs_remote_settings');
                            $image_size = ! empty($options['rs_template']['image_size']) ? $options['rs_template']['image_size'] : 'medium';
                            $details_url = $program->alternate_url ? $program->alternate_url : $RS_Connect->get_page_url('programs').$program->ID.'/'.$program->slug;
                            ?>
                            <li>
                                <?php if ($show_thumbnail && $program->photo_details) : ?>
                                    <?php $program_image_url = $program->photo_details->{$image_size}->url; ?>
                                    <div class="rs-program-thumbnail">
                                        <a href="<?php echo $details_url; ?>"><img
                                                src="<?php echo $program_image_url; ?>"
                                                width="<?php echo $image_w; ?>" height="<?php echo $image_h; ?>"></a>
                                    </div>
                                <?php endif; ?>
                                <h4 class="rs-program-title">
                                    <a href="<?php echo $details_url ?>"><?php echo $program->title; ?></a>
                                </h4>

                                <p class="rs-program-date"><?php if ($show_date) {
                                        echo $program->date;
                                    } ?></p>

                                <p class="rs-program-excerpt"><?php if ($show_excerpt) {
                                        echo wp_trim_words($program->text, $excerpt_words);
                                        if ($excerpt_more) echo '<a href="'.$details_url.'">'.$excerpt_more.'</a>';
                                    } ?></p>

                            </li>

                            <?php
                            $count++;
                        }
                    }
                }

            if ($view_all)  echo '<p><a href="'.$RS_Connect->get_page_url('programs').'"">'.$view_all.'</a></p>';

            ?>
            </ul>
            <?php

            echo $after_widget;

        }

        // do validation
        public function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = strip_tags($new_instance['title']);
            $instance['number'] = strip_tags($new_instance['number']);
            $instance['category'] = strip_tags($new_instance['category']);
            $instance['show_thumbnail'] = ! empty($new_instance['show_thumbnail']) ? 1 : 0;
            $instance['image_w'] = (int) $new_instance['image_w'];
            $instance['image_h'] = (int) $new_instance['image_h'];
            $instance['show_teacher'] = ! empty($new_instance['show_teacher']) ? 1 : 0;
            $instance['show_date'] = ! empty($new_instance['show_date']) ? 1 : 0;
            $instance['show_excerpt'] = ! empty($new_instance['show_excerpt']) ? 1 : 0;
            $instance['excerpt_words'] = (int) $new_instance['excerpt_words'];
            $instance['excerpt_more'] = strip_tags($new_instance['excerpt_more']);
            $instance['featured_only'] = ! empty($new_instance['featured_only']) ? 1 : 0;
            $instance['view_all'] = strip_tags($new_instance['view_all']);

            $this->flush_widget_cache();

            $alloptions = wp_cache_get('alloptions', 'options');
            if (isset($alloptions['widget_retreat_site_programs_option']))
                delete_option('widget_retreat_site_programs_option');

            return $instance;
        }

        public function flush_widget_cache() {
            wp_cache_delete('widget_retreat_site_programs', 'retreat_site');
        }

        public function form($instance) {

            // initialize instance with defaults
            $instance = array_merge($this->defaults(), $instance);

            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of programs to display:'); ?></label> <input type="text" value="<?php echo esc_attr($instance['number']); ?>" name="<?php echo $this->get_field_name('number'); ?>" id="<?php echo $this->get_field_id('number'); ?>" size="3" />
            </p>
            <p>
                <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_thumbnail'); ?>" name="<?php echo $this->get_field_name('show_thumbnail'); ?>"<?php checked($instance['show_thumbnail']); ?> />
                <label for="<?php echo $this->get_field_id('show_thumbnail'); ?>"><?php _e('Show Featured Image'); ?></label><br>

                <label for="<?php echo $this->get_field_id('image_w'); ?>"><?php _e('Width:'); ?></label>
                <input type="text" class="small-text" id="<?php echo $this->get_field_id('image_w'); ?>" name="<?php echo $this->get_field_name('image_w'); ?>" value="<?php echo $instance['image_w'] ?>" />
                &nbsp; <label for="<?php echo $this->get_field_id('image_h'); ?>"><?php _e('Height:'); ?></label>
                <input type="text" class="small-text" id="<?php echo $this->get_field_id('image_h'); ?>" name="<?php echo $this->get_field_name('image_h'); ?>" value="<?php echo $instance['image_h'] ?>" />
            </p>
            <?php if (function_exists('rs_has_teachers')) : ?>
                <p>
                    <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_teacher'); ?>" name="<?php echo $this->get_field_name('show_teacher'); ?>"<?php checked($instance['show_teacher']); ?> />
                    <label for="<?php echo $this->get_field_id('show_teacher'); ?>"><?php _e('Show Teacher'); ?></label>
                </p>
            <?php endif; ?>
            <p>
                <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>"<?php checked($instance['show_date']); ?> />
                <label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('Show Date'); ?></label>
            </p>
            <p>
                <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_excerpt'); ?>" name="<?php echo $this->get_field_name('show_excerpt'); ?>"<?php checked($instance['show_excerpt']); ?> />
                <label for="<?php echo $this->get_field_id('show_excerpt'); ?>"><?php _e('Show Blurb/Excerpt'); ?></label><br>

                <label for="<?php echo $this->get_field_id('excerpt_words'); ?>"><?php _e('Words:'); ?></label>
                <input type="text" class="small-text" id="<?php echo $this->get_field_id('excerpt_words'); ?>" name="<?php echo $this->get_field_name('excerpt_words'); ?>" value="<?php echo $instance['excerpt_words'] ?>" />

                &nbsp; <label for="<?php echo $this->get_field_id('excerpt_more'); ?>"><?php _e('Link:'); ?></label>
                <input type="text" class="small-text" id="<?php echo $this->get_field_id('excerpt_more'); ?>" name="<?php echo $this->get_field_name('excerpt_more'); ?>" value="<?php echo esc_attr($instance['excerpt_more']) ?>" /><br>

            </p>
            <p>
                <label for="<?php echo $this->get_field_id('view_all'); ?>"><?php _e('"View all" link text:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('view_all'); ?>" name="<?php echo $this->get_field_name('view_all'); ?>" type="text" value="<?php echo esc_attr($instance['view_all']); ?>" />
            </p>
            <h4><?php _e('Options:'); ?></h4>
            <p>
                <label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Only from category (use slug):'); ?></label> <input type="text" value="<?php echo esc_attr($instance['category']); ?>" name="<?php echo $this->get_field_name('category'); ?>" id="<?php echo $this->get_field_id('category'); ?>" class="widefat" />
            </p>
        <?php
        }

    }
}

/**
 * Register the widgets.
 */
function rs_connect_widgets_init() {
    register_widget('RS_Connect_Widget');
}
add_action('widgets_init', 'rs_connect_widgets_init');
