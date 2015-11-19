<?php

/*
Plugin Name: Retreat Booking Guru Connect
Description: Connect to Retreat Booking Guru to show program listings on your site and link to registration forms.
Version: 1.2.1
Author: Retreat Guru
Author URI: http://retreat.guru/booking
*/

class RS_Connect
{

    public $program = null;

    public function __construct()
    {
        // Base domain to connect with (do not include http://)
        $this->mbm_domain = 'secure.retreat.guru';
//        $this->mbm_domain = 'programs.dev';

        $options = get_option('rs_settings');

        if(isset($options['style']))
        {
            $this->style = $options['style'];
        } else {
            $this->style = 'program';
        }

        $this->plugin_dir = plugin_dir_path( __FILE__ );
        $this->includes();

        add_filter('admin_init', array($this, 'rs_flush_rewrite_rules'));
        add_action('wp_head', array($this, 'rs_set_meta'));
        add_filter('wp_title', array($this, 'rs_set_title'), 100);

        add_action('wp_enqueue_scripts', array($this, 'rs_enqueue_items'));
        add_action('admin_menu', array($this, 'rs_admin_menu_items'));
        add_action('admin_init', array($this, 'rs_register_settings'));
        add_action('admin_notices', array($this, 'my_admin_notice'));

        add_shortcode('rs_programs', array($this, 'rs_shortcode_programs'));
        add_shortcode('rs_register_button', array($this, 'rs_shortcode_register_button'));

        add_action('init', array($this, 'setup_rewrite'));
        add_filter( 'query_vars', array($this, 'register_query_var' ));
        add_filter('template_include', array($this, 'template_include'), 100, 1);
    }

    function setup_rewrite() {
        global $wp_rewrite;
        // Programs todo: switch to ?rs_program instead of ?program
        add_rewrite_rule( $this->style.'s/?$',  'index.php?programs=true', 'top' );
        add_rewrite_rule( $this->style.'s/category/([^/]*)/?',  'index.php?programs=true&category=$matches[1]', 'top' );
        add_rewrite_rule( $this->style.'/([^/]*)/?',  'index.php?programs=true&program=$matches[1]', 'top' );
        add_rewrite_rule( $this->style.'/([^/]*)/([^/]*)/?',  'index.php?programs=true&program=$matches[1]', 'top' );
        // Teachers
        add_rewrite_rule( 'teachers/?$',  'index.php?teachers=true', 'top' );
        add_rewrite_rule( 'teachers/category/([^/]*)/?',  'index.php?teachers=true&category=$matches[1]', 'top' );
        add_rewrite_rule( 'teacher/([^/]*)/([^/]*)/?',  'index.php?teachers=true&teacher=$matches[1]', 'top' );
    }

    function register_query_var( $vars ) {
        $vars[] = 'programs';
        $vars[] = 'program';
        $vars[] = 'teachers';
        $vars[] = 'teacher';
        $vars[] = 'category';

        return $vars;
    }

    function rs_set_title($title = null)
    {
        global $wp_query;

        if(get_query_var('category')) $category = get_query_var('category') . " | "; else $category = '';

        if (get_query_var('program')) {

            if(isset($this->program->title)) {
                $program_title = $this->program->title . " | " . get_bloginfo('name');
            } else {
                $program_title = get_bloginfo('name');
            }

            return $program_title;
        }

        if (get_query_var('programs')) {
            return ucfirst($this->style) . 's' . " | " . $category . get_bloginfo('name');
        }

        return $title;
    }

    function rs_set_meta() {
        if(isset($this->program->text)) {
            echo '<meta property="og:description" content="' . wp_trim_words($this->program->text, 100, '...') . '" />';
        }

        echo '<meta property="og:title" content="' . $this->rs_set_title() . '" />';
    }

    function template_include($template)
    {
        global $wp_query; //Load $wp_query object
        global $api_vars;

        // Support Yoast SEO
        add_filter( 'wpseo_canonical', array($this, 'canonical_url' ));
        add_filter( 'wpseo_title', array($this, 'rs_set_title' ));
        add_filter( 'wpseo_metadesc', '__return_false' );

        // Load program views
        $programs = get_query_var('programs');
        $program = get_query_var('program');
        $category = get_query_var('category');

        if($programs)
        {
            if($program)
            {
                $this->program = $this->get_program($program);
                return $this->get_template_path('single-program.php');
            }


            if($category) {  $api_vars .= 'category=' . $category . '&'; }



            return $this->get_template_path('archive-program.php');
        }

        // Load teachers views
        if($wp_query->query_vars['teachers'])
        {
            if(isset($wp_query->query_vars['teacher']))
            {
                global $rs_the_teacher;
                $rs_the_teacher= $this->get_teacher($wp_query->query_vars['teacher']);

                return $this->get_template_path('single-teacher.php');
            }

            global $api_vars;
            if($wp_query->query_vars['category']) {  $api_vars .= 'category=' . get_query_var('category') . '&'; }

            return $this->get_template_path('archive-teacher.php');
        }

        return $template;
    }

    function canonical_url()
    {
        return $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    }

    function rs_load_template($template)
    {
        if ($overridden_template = locate_template($template)) {
            load_template($overridden_template);
        } else {
            load_template(plugin_dir_path(__FILE__) . 'templates/' . $template);
        }
    }

    function get_template_path($template)
    {
        if($this->template_overridden($template)) {
            return $this->template_overridden($template);
        } else {
            return plugin_dir_path(__FILE__) . 'templates/'.$template;
        }
    }

    function template_overridden($template)
    {
        $path = locate_template($template);
        if ($path) return $path;

        return false;
    }

    function includes()
    {
        if($this->configured()) include( "{$this->plugin_dir}rs-connect-widgets.php" );
    }

    function rs_shortcode_register_button($atts)
    {
        if(! isset($atts['id'])) { return 'Error: You must specify a program ID in your shortcode<br/>e.g. [rs_register_button <strong>id="45"</strong>]'; }
        // todo: Build link here to customize the link title instead of pulling it dynamically or maybe just search and replace string
        $program = $this->get_program($atts['id']);
        return $program->registration_action;
    }

    function rs_shortcode_programs($atts)
    {
        global $rs_the_programs;

        $shortcode_atts = $this->normalize_empty_atts( $atts );

        $vars = null;

        if (isset($atts['category'])) {
            $vars .= 'category=' . $atts['category'] . '&';
        }

        $template = 'shortcode-programs.php';
        if (! empty($shortcode_atts['table'])) {
            $template = 'shortcode-programs-table.php';
        }

        $rs_the_programs = $this->get_programs($vars);
        if ( ! is_array($rs_the_programs)) return '';

        $rs_the_programs = array_reverse($rs_the_programs);

        if (isset($atts['limit'])) {
            $rs_the_programs = array_slice($rs_the_programs, 0, $atts['limit']);
        }

        // the proper way to do it
//        $shortcode_atts = shortcode_atts(array(
//            'show_location' => false,
//         ), $atts, 'rs_programs');

        ob_start();
        $overridden_template = locate_template('shortcode-programs.php');
        if ($overridden_template) {
            include($overridden_template);
        } else {
            include(plugin_dir_path(__FILE__) . 'templates/' . $template);
        }
        $ret = ob_get_clean();

        return $ret;
    }

    function normalize_empty_atts ($atts) {
        if (! $atts) return array();

        foreach ($atts as $attribute => $value) {
            if (is_int($attribute)) {
                $atts[strtolower($value)] = true;
                unset($atts[$attribute]);
            }
        }
        return $atts;
    }

    public function get_program($id)
    {
        return $this->remote_get($this->get_url_to_mbm() . '/wp-json/events/' . $id . '?' .rand());
    }

    public function get_programs($vars = null)
    {
        return $this->remote_get($this->get_url_to_mbm() . '/wp-json/events/?' . $vars . rand());
    }

    public function get_teachers($vars = null)
    {
        return $this->remote_get($this->get_url_to_mbm() . '/wp-json/teachers/?' . $vars . rand());
    }

    public function get_teacher($id)
    {
        return $this->remote_get($this->get_url_to_mbm() . '/wp-json/teachers/' . $id  . '?' .rand());
    }

    function rs_enqueue_items()
    {
        wp_enqueue_style('rs-f', plugins_url('/resources/frontend/rs.css', __FILE__), null, '20151013a');
        wp_enqueue_script('rs-js', plugins_url('/resources/frontend/rs.js', __FILE__), array('jquery'), '20150612a');

        $options = get_option('rs_settings');
        $inline_styles = '';

        if(isset($options['rs_template']['register_now']))
        {
            $inline_styles .= '
            .rs-register-link a {
            border-color: #'.$options['rs_template']['register_now'].';
            color: #'.$options['rs_template']['register_now'].'!important ;
            }';
        }

        if(isset($options['rs_template']['css']))
        {
            $inline_styles .= $options['rs_template']['css'];
        }
        wp_add_inline_style('rs-f', $inline_styles);
    }

    function rs_admin_menu_items()
    {
        add_menu_page('Retreat Booking Guru', 'Retreat Booking Guru', 'manage_options', 'booking-manager.php', arraY(&$this, 'admin_programs_page'), 'dashicons-calendar-alt');
        add_submenu_page('booking-manager.php', 'Program List', 'Program List', 'manage_options', 'booking-manager.php', array(&$this, 'admin_programs_page'));
        add_submenu_page('booking-manager.php', 'Settings', 'Settings', 'manage_options', 'options-mbm', array(&$this, 'admin_settings_page'));
//        add_submenu_page('booking-manager.php', 'Help', 'Help', 'manage_options', 'mbm-help', array(&$this, 'admin_mbm_help_page'));
    }

    function rs_activate()
    {
        global $wp_rewrite;
        $this->rs_flush_rewrite_rules();
    }

    function rs_flush_rewrite_rules()
    {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

    private function remote_get($url, $args = array())
    {
        $response = wp_remote_get($url, $args);

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) )
			return false;

		return json_decode( wp_remote_retrieve_body( $response ) );
    }

    function rs_register_settings()
    {
        register_setting('rs_settings', 'rs_settings');
    }

    function configured()
    {
        $options = get_option('rs_settings');
        if (empty($options['rs_domain'])) return false;

        return true;
    }

    function my_admin_notice()
    {
        if($this->configured()) return true;
        ?>
        <div class="error">
            <p>Please specify your Retreat Booking Guru subdomain.
                    <a href="<?php echo admin_url('admin.php?page=options-mbm'); ?>">Click Here</a></p>
        </div>
    <?php
    }

    function get_url_to_mbm()
    {
        $options = get_option('rs_settings');

        if (RS_TESTING && empty($options['rs_domain'])) return 'http://programs.dev'; // local debug

        $sub_domain = ! empty($options['rs_domain']) ? $options['rs_domain'] : 'demo';

        return 'http://' . $sub_domain . "." . $this->mbm_domain;
    }

    function admin_programs_page()
    {
        $rs_programs = $this->get_programs();
        ?>
        <div style="clear:left; margin:20px 20px 20px 0;">
            <a href="<?php echo add_query_arg( array('page' => 'options-mbm'), admin_url('admin.php')); ?>" class="button">Plugin Settings</a>
            &nbsp; &nbsp; &nbsp;
            <a href="<?php echo $this->get_url_to_mbm(); ?>/wp-admin/admin.php?page=rs-programs" class="button">View All Programs</a>
            <a href="<?php echo $this->get_url_to_mbm(); ?>/wp-admin/admin.php?page=registrations" class="button">View All Registrations</a>
            <a href="<?php echo $this->get_url_to_mbm(); ?>/wp-admin/admin.php?page=rs-transactions" class="button">View All Transactions</a>
        </div>

        <div style="width: 58%; float: left;">
        <table class="wp-list-table widefat fixed posts">
            <thead>
            <tr>
                <th width="300">Program</th>
                <th width="100">Dates</th>
                <th width="100">Registration</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ((array)$rs_programs as $program): ?>
                <tr>
                    <td>
                        <a href="<?php echo $this->get_url_to_mbm(); ?>/wp-admin/post.php?action=edit&post=<?php echo $program->ID; ?>"><?php echo $program->title; ?></a>
                        - <a href="<?php echo get_site_url() . '/' . $this->style . '/' . $program->ID . '/' . $program->slug; ?>">view</a></td>
                    <td><?php echo $program->date; ?></td>
                    <td><?php echo ucfirst($program->registration_status); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        <div style="width: 40%; float: right;">
            <?php include($this->plugin_dir . '/views/admin-help.php'); ?>
        </div>

    <?php
    }

    function admin_settings_page()
    {
        $this->rs_flush_rewrite_rules();
        ?>
        <div class="wrap">
            <h2>Retreat Booking Guru Settings</h2>

            <form action="options.php" method="post"><?php
                settings_fields('rs_settings');
                do_settings_sections(__FILE__);

                $options = get_option('rs_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Subdomain</th>
                        <td>
                            <fieldset>
                                <label>
                                    https:// <input name="rs_settings[rs_domain]" type="text" id="rs_domain"
                                                    value="<?php echo (isset($options['rs_domain']) && $options['rs_domain'] != '') ? $options['rs_domain'] : ''; ?>"/>
                                    .<?php echo $this->mbm_domain; ?> <br/>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Label</th>
                        <td>
                            <fieldset>
                              What do you offer?<br/>
                                <small>This provides the correct permalink structure for your site</small><br/>
                                <input type="radio" name="rs_settings[style]" value="program" <?php if($options['style'] == 'program' || ! isset($options['style'])) { echo "checked"; } ?>>Programs<br>
                                <input type="radio" name="rs_settings[style]" value="event" <?php if($options['style'] == 'event') { echo "checked"; } ?>>Events<br>
                                <input type="radio" name="rs_settings[style]" value="retreat" <?php if($options['style'] == 'retreat') { echo "checked"; } ?>>Retreats<br>
                                <input type="radio" name="rs_settings[style]" value="workshop" <?php if($options['style'] == 'workshop') { echo "checked"; } ?>>Workshops
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Register Now Button Color</th>
                        <td>
                            <fieldset>
                                <label>
                                    #<input name="rs_settings[rs_template][register_now]" type="text" id="rs_settings[rs_template][register_now]"
                                            value="<?php echo (isset($options['rs_template']['register_now']) && $options['rs_template']['register_now'] != '') ? $options['rs_template']['register_now'] : ''; ?>"/>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Before theme & after</th>
                        <td>
                            <fieldset>
                                Wrap template tags around the program listings to fix template bugs<br/>
                                <label>
                                    <textarea name="rs_settings[rs_template][before]" type="text" style="width:700px;height:200px;" id="rs_settings[rs_template][before]"><?php if(isset($options['rs_template']['before'])) echo $options['rs_template']['before']; ?>
                                    </textarea>
                                </label><br/>
                                <label>
                                    <textarea name="rs_settings[rs_template][after]" type="text" style="width:700px;height:100px;" id="rs_settings[rs_template][after]"><?php if(isset($options['rs_template']['after'])) echo $options['rs_template']['after']; ?>
                                    </textarea>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Style Adjustments</th>
                        <td>
                            <fieldset>
                                Customize or add CSS site styles below<br/>
                                <label>
                                    <textarea name="rs_settings[rs_template][css]" type="text" style="width:700px; height:200px;" id="rs_settings[rs_template][css]"><?php if(isset($options['rs_template']['css'])) echo trim($options['rs_template']['css']); ?>
                                    </textarea><br/>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                    <th scope="row"></th>
                    <td><input type="submit" style="font-size: 24px;" value="Save"/></td>
                    </tr>
                </table>
            </form>
        </div>
    <?php
    }
}
global $RS_Connect;
$RS_Connect = new RS_Connect();
register_activation_hook(__file__, array($RS_Connect, 'rs_activate'));
