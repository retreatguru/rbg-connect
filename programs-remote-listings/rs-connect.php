<?php

/*
Plugin Name: Retreat Booking Guru Connect
Description: Connect to Retreat Booking Guru to show program listings on your site and link to registration forms.
Version: 1.6.1
Author: Retreat Guru
Author URI: http://retreat.guru/booking
*/

class RS_Connect
{

    // todo: separate this plugin into front end and admin classes
    // todo: stop creating urls via . use add_query_args() or other abstracted method
    // todo: general clean up to make plugin better organized

    public $program = null;

    public function __construct()
    {
        // Base domain to connect with (do not include http://)
        $this->mbm_domain = 'secure.retreat.guru';
        $this->https = 'https://';

        // local testing
        if (isset($_SERVER['SERVER_NAME']) && 'programs-remote.dev' == $_SERVER['SERVER_NAME']) {
            $this->mbm_domain = 'programs.dev';
            $this->https = 'http://';
        }

        $options = get_option('rs_settings');

        if (isset($options['style'])) {
            $this->style = $options['style'];
        } else {
            $this->style = 'program';
        }

        $this->plugin_dir = plugin_dir_path(__FILE__);
        $this->includes();

//        add_filter('admin_init', array($this, 'rs_flush_rewrite_rules')); // don't do this on every page
        add_action('wp_head', array($this, 'rs_set_meta'));
        add_filter('wp_title', array($this, 'rs_set_title'), 100);

        add_action('wp_enqueue_scripts', array($this, 'rs_enqueue_items'));
        add_action('admin_menu', array($this, 'rs_admin_menu_items'));
        add_action('admin_init', array($this, 'rs_register_settings'));
        add_action('admin_notices', array($this, 'my_admin_notice'));

        add_shortcode('rs_programs', array($this, 'rs_shortcode_programs'));
        add_shortcode('rs_register_button', array($this, 'rs_shortcode_register_button'));

        add_action('init', array($this, 'setup_rewrite'));
        add_filter('query_vars', array($this, 'register_query_var'));
        add_filter('template_include', array($this, 'template_include'), 100, 1);
    }

    function setup_rewrite()
    {
        // Programs todo: switch to ?rs_program instead of ?program
        add_rewrite_rule($this->style . 's/?$', 'index.php?programs=true', 'top');
        add_rewrite_rule($this->style . 's/category/([^/]*)/?', 'index.php?programs=true&category=$matches[1]', 'top');
        add_rewrite_rule($this->style . '/([^/]*)/?', 'index.php?programs=true&program=$matches[1]', 'top');
        add_rewrite_rule($this->style . '/([^/]*)/([^/]*)/?', 'index.php?programs=true&program=$matches[1]', 'top');
        // Teachers
        add_rewrite_rule('teachers/?$', 'index.php?teachers=true', 'top');
        add_rewrite_rule('teachers/category/([^/]*)/?', 'index.php?teachers=true&category=$matches[1]', 'top');
        add_rewrite_rule('teacher/([^/]*)/([^/]*)/?', 'index.php?teachers=true&teacher=$matches[1]', 'top');
    }

    function register_query_var($vars)
    {
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

        if (get_query_var('category')) {
            $category = get_query_var('category') . " | ";
        } else {
            $category = '';
        }

        if (get_query_var('program')) {

            if (isset($this->program->title)) {
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

    function rs_set_meta()
    {
        if (isset($this->program->text)) {
            echo '<meta property="og:description" content="' . wp_trim_words($this->program->text, 100, '...') . '" />';
        }

        echo '<meta property="og:title" content="' . $this->rs_set_title() . '" />';
    }

    function template_include($template)
    {
        global $wp_query; //Load $wp_query object
        global $rs_api_vars;

        // Support Yoast SEO
        add_filter( 'wpseo_canonical', array($this, 'canonical_url' ));
        add_filter( 'wpseo_title', array($this, 'rs_set_title' ));
        add_filter( 'wpseo_metadesc', '__return_false' );

        // Load program views
        $programs = get_query_var('programs');
        $program = get_query_var('program');
        $category = get_query_var('category');

        if ($programs) {
            if ($program) {
                $this->program = $this->get_program($program);
                return $this->get_template_path('single-program.php');
            }

            if ($category) {
                $rs_api_vars .= 'category=' . $category;
            }

            return $this->get_template_path('archive-program.php');
        }

        // Load teachers views
        if (isset($wp_query->query_vars['teachers'])) {
            if (isset($wp_query->query_vars['teacher'])) {
                global $rs_the_teacher;
                $rs_the_teacher = $this->get_teacher($wp_query->query_vars['teacher']);

                return $this->get_template_path('single-teacher.php');
            }

            if ($wp_query->query_vars['category']) {
                $rs_api_vars .= 'category=' . get_query_var('category');
            }

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
        if ($this->template_overridden($template)) {
            return $this->template_overridden($template);
        } else {
            return plugin_dir_path(__FILE__) . 'templates/' . $template;
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
        if ($this->configured()) {
            include("{$this->plugin_dir}rs-connect-widgets.php");
        }
    }

    function rs_shortcode_register_button($atts)
    {
        if (!isset($atts['id'])) {
            return 'Error: You must specify a program ID in your shortcode<br/>e.g. [rs_register_button <strong>id="45"</strong>]';
        }
        // todo: Build link here to customize the link title instead of pulling it dynamically or maybe just search and replace string
        $program = $this->get_program($atts['id']);
        return $program->registration_action;
    }

    function rs_shortcode_programs($atts)
    {
        global $rs_the_programs;

        $shortcode_atts = $this->normalize_empty_atts($atts);

        $vars = null;

        if (isset($atts['category'])) {
            $vars .= 'category=' . $atts['category'];
        }

        $template = 'shortcode-programs.php';
        if (!empty($shortcode_atts['table'])) {
            $template = 'shortcode-programs-table.php';
        }

        $rs_the_programs = $this->get_programs($vars);
        if (!is_array($rs_the_programs)) {
            return '';
        }

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

    function normalize_empty_atts($atts)
    {
        if (!$atts) {
            return array();
        }

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
        return $this->remote_get($this->get_url_to_mbm() . '/wp-json/events/' . $id);
    }

    public function get_programs($vars = null)
    {
        return $this->remote_get($this->get_url_to_mbm() . '/wp-json/events/?' . $vars);
    }

    public function get_teachers($vars = null)
    {
        return $this->remote_get($this->get_url_to_mbm() . '/wp-json/teachers/?' . $vars);
    }

    public function get_teacher($id)
    {
        return $this->remote_get($this->get_url_to_mbm() . '/wp-json/teachers/' . $id);
    }

    /**
     * If the RBG api is down, return cached data, otherwise get fresh from api and save data for later
     * todo: better would be to always serve cached data, then reset cache when new data is available via remote post from RBG
     *
     * @param $url
     * @return array|bool|mixed|object
     */
    private function remote_get($url)
    {
        global $rs_api_status;

        if ( $rs_api_status == 'down') {
            // if api is down then return cached version
            return $this->get_api_cache($url);
        }

        // ensure api calls are not cached
        $rand_url = add_query_arg(array('rs-rand' => rand()), $url);
        $response = wp_remote_get($rand_url, array('timeout' => 4));

        if (is_wp_error($response) || 200 != wp_remote_retrieve_response_code($response)) {
            $rs_api_status = 'down';
            return $this->get_api_cache($url);
        }

        $rs_api_status = 'good';
        $body = json_decode(wp_remote_retrieve_body($response));
        $this->save_api_cache($url, $body);

        return $body;
    }

    // save api data in case RBG is down later, if the value is the same, wp won't update it
    public function save_api_cache($url, $body)
    {
        update_option($this->api_cache_slug($url), serialize($body));
    }

    public function get_api_cache($url)
    {
        return unserialize(get_option($this->api_cache_slug($url)));
    }

    public function api_cache_slug($url)
    {
        return 'rs_api_cache_' . preg_replace('/[^a-zA-Z0-9_-]/', '',  $url);
    }


    function rs_enqueue_items()
    {
        wp_enqueue_style('rs-f', plugins_url('/resources/frontend/rs.css', __FILE__), null, '20151013a');
        wp_enqueue_script('rs-js', plugins_url('/resources/frontend/rs.js', __FILE__), array('jquery'), '20150612a');

        $options = get_option('rs_settings');
        $inline_styles = '';

        if (isset($options['rs_template']['register_now'])) {
            $inline_styles .= '
            .rs-register-link a {
            border-color: #' . $options['rs_template']['register_now'] . ';
            color: #' . $options['rs_template']['register_now'] . '!important ;
            }';
        }

        if (isset($options['rs_template']['css'])) {
            $inline_styles .= $options['rs_template']['css'];
        }
        wp_add_inline_style('rs-f', $inline_styles);
    }

    function rs_admin_menu_items()
    {
        add_menu_page('Retreat Booking Guru', 'Retreat Booking Guru', 'manage_options', 'booking-manager.php', arraY(&$this, 'admin_programs_page'), 'dashicons-calendar-alt');
        add_submenu_page('booking-manager.php', 'Program & Help', 'Program & Help', 'manage_options', 'booking-manager.php', array(&$this, 'admin_programs_page'));
        add_submenu_page('booking-manager.php', 'Retreat Guru Settings', 'Retreat Guru Settings', 'manage_options', 'options-mbm', array(&$this, 'admin_settings_page'));
//        add_submenu_page('booking-manager.php', 'Help', 'Help', 'manage_options', 'mbm-help', array(&$this, 'admin_mbm_help_page'));
    }

    function rs_flush_rewrite_rules()
    {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
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
        if ($this->configured()) return true;
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
        $sub_domain = ! empty($options['rs_domain']) ? $options['rs_domain'] : 'demo';

        return $this->https . $sub_domain . "." . $this->mbm_domain;
    }

    function admin_programs_page()
    {
        include($this->plugin_dir . '/views/admin-main.php');
    }

    function admin_settings_page()
    {
        $this->rs_flush_rewrite_rules();

        include($this->plugin_dir . '/views/admin-settings.php');
    }

}

global $RS_Connect;
$RS_Connect = new RS_Connect();
