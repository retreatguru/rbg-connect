<?php

/*
Plugin Name: Retreat Booking Guru Connect
Description: Connect to Retreat Booking Guru to show program listings on your site and link to registration forms.
Version: 1.9.0
Author: Retreat Guru
Author URI: http://retreat.guru/booking
*/

class RS_Connect
{
    protected $options = null;
    protected $program = null;
    protected $style = 'program';

    public function __construct()
    {
        $this->options = get_option('rs_settings');
        $this->plugin_dir = plugin_dir_path(__FILE__);
        $this->includes();

        add_filter('init', array($this, 'setup_rewrites'));

        add_filter('the_content', array($this, 'insert_shortcode'));
        add_action('wp_head', array($this, 'set_program_meta'));
        add_filter('pre_get_document_title', array($this, 'set_program_title'));

        add_action('admin_menu', array($this, 'admin_add_menu_items'));
        add_action('admin_init', array($this, 'admin_register_settings'));
        add_action('admin_notices', array($this, 'admin_setup_notice'));

        add_filter('query_vars', array($this, 'register_query_var'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_items'));

        add_action( 'template_redirect', array($this, 'receive_preview_request') );
        register_activation_hook(__FILE__, array($this, 'on_activate_upgrade'));
    }

    function includes()
    {
        require("{$this->plugin_dir}rs-connect-shortcodes.php");
        require("{$this->plugin_dir}rs-connect-api.php");
        require("{$this->plugin_dir}rs-connect-upgrade.php");

        if ($this->configured()) {
            include("{$this->plugin_dir}rs-connect-widgets.php");
        }
    }

    function setup_rewrites()
    {
        if (! $this->configured()) { return; }

        // Programs
        $programs_page = $this->get_page('programs');
        $programs_page_slug = $programs_page->post_name;
        add_rewrite_rule($programs_page_slug . '/([^/]*)/?', 'index.php?page_id=' . $programs_page->ID . '&rs_program=$matches[1]', 'top');
        add_rewrite_rule($programs_page_slug . '/([^/]*)/([^/]*)/?', 'index.php?page_id=' . $programs_page->ID . '&rs_program=$matches[1]', 'top');
        add_rewrite_rule($programs_page_slug . '/category/([^/]*)/?', 'index.php?page_id=' . $programs_page->ID . '&category=$matches[1]', 'top');

        // Teachers
        $teachers_page = $this->get_page('teachers');
        $teachers_page_slug = $teachers_page->post_name;
        add_rewrite_rule($teachers_page_slug . '/([^/]*)/([^/]*)/?', 'index.php?page_id=' . $teachers_page->ID . '&rs_teacher=$matches[1]', 'top');
        add_rewrite_rule($teachers_page_slug . '/category/([^/]*)/?', 'index.php?page_id=' . $teachers_page->ID . '&category=$matches[1]', 'top');

        // Legacy Rules: We migrate existing installs: This is useful if the new teacher / program slug is somehow different than the old one.
        add_rewrite_rule($this->style . 's/?$', 'index.php?page_id=' . $programs_page->ID, 'top');
        add_rewrite_rule($this->style . 's/category/([^/]*)/?', 'index.php?page_id=' . $programs_page->ID . '&category=$matches[1]', 'top');
        add_rewrite_rule($this->style . '/([^/]*)/?', 'index.php?page_id=' . $programs_page->ID . '&rs_program=$matches[1]', 'top');
        add_rewrite_rule($this->style . '/([^/]*)/([^/]*)/?', 'index.php?page_id=' . $programs_page->ID . '&rs_program=$matches[1]', 'top');

        add_rewrite_rule('teachers/?$', 'index.php?page_id=' . $teachers_page->ID, 'top');
        add_rewrite_rule('teachers/category/([^/]*)/?', 'index.php?page_id=' . $teachers_page->ID . '&category=$matches[1]', 'top');
        add_rewrite_rule('teacher/([^/]*)/([^/]*)/?', 'index.php?page_id=' . $teachers_page->ID . '&rs_teacher=$matches[1]', 'top');
    }

    function insert_shortcode($content)
    {
        $current_page = $GLOBALS['post']->post_name;
        if(! $this->configured()) return $content;

        if ($current_page == $this->get_programs_page()->post_name) {
            return $this->use_shortcode('rs_program');
        }

        if ($current_page == $this->get_teachers_page()->post_name) {
            return $this->use_shortcode('rs_teacher');
        }

        return $content;
    }

    function use_shortcode($shortcode)
    {
        // Load a specific program or teacher
        if (! empty(get_query_var($shortcode))) {
            $program_id = get_query_var($shortcode);
            return "[{$shortcode} id='{$program_id}']";
        }

        // Load a category of programs
        if (! empty(get_query_var('category'))) {
            $category_slug = get_query_var('category');
            return "[{$shortcode}s category='{$category_slug}']";
        }

        // Return either a list of programs or teachers
        return "[{$shortcode}s]";
    }

    function set_program_meta()
    {
        if (get_query_var('rs_program')) {
            $program_id = get_query_var('rs_program');
            $this->program = RS_Connect_Api::get_program($program_id);
            if (! empty($this->program->text)) {
                echo '<meta property="og:description" content="' . wp_trim_words($this->program->text, 100, '...') . '" />';
            }
        }
    }

    function set_program_title($title = null)
    {
        if (get_query_var('rs_program')) {
            $program_id = get_query_var('rs_program');
            $this->program = RS_Connect_Api::get_program($program_id);
            if (isset($this->program->title)) {
                $program_title = $this->program->title . " | " . get_bloginfo('name');
            } else {
                $program_title = get_bloginfo('name');
            }

            return $program_title;
        }

        return $title;
    }

    function get_programs_page() {
        return $this->get_page('programs');
    }

    function get_teachers_page() {
        return $this->get_page('teachers');
    }

    function get_page($page)
    {
        if (empty($this->options['page']) || empty($this->options['page'][$page])) return false;

        $page_id = $this->options['page'][$page];
        $page = get_post($page_id);

        return $page;
    }

    /**
     * @param $type - Either programs or teachers
     * @return false|string
     */
    function get_page_url($type)
    {
        $entity_base = $this->get_page($type);

        return get_permalink($entity_base->ID);
    }

    function admin_add_menu_items()
    {
        add_menu_page('Retreat Booking Guru', 'Retreat Booking Guru', 'manage_options', 'booking-manager.php', array($this, 'admin_programs_page'), 'dashicons-calendar-alt');
        add_submenu_page('booking-manager.php', 'Program & Help', 'Program & Help', 'manage_options', 'booking-manager.php', array($this, 'admin_programs_page'));
        add_submenu_page('booking-manager.php', 'Retreat Guru Settings', 'Retreat Guru Settings', 'manage_options', 'options-mbm', array($this, 'admin_settings_page'));
    }

    function admin_register_settings()
    {
        register_setting('rs_settings', 'rs_settings');
    }

    function admin_setup_notice()
    {
        if ($this->configured()) {
            return true;
        }
        ?>
        <div class="error">
            <p>Please setup Retreat Booking Guru.<a href="<?php echo admin_url('admin.php?page=options-mbm'); ?>">Click Here</a></p>
        </div>
        <?php
    }

    function admin_programs_page()
    {
        include($this->plugin_dir . '/views/admin-main.php');
    }

    function admin_settings_page()
    {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();

        include($this->plugin_dir . '/views/admin-settings.php');
    }

    function excerpt($description)
    {
        $limit = ! empty($this->options['rs_template']['limit_description']) ? $this->options['rs_template']['limit_description'] : 100;

        return wp_trim_words($description, $limit);
    }

    function register_query_var($vars)
    {
        $vars[] = 'rs_programs';
        $vars[] = 'rs_program';
        $vars[] = 'rs_teachers';
        $vars[] = 'rs_teacher';
        $vars[] = 'category';

        return $vars;
    }

    function enqueue_items()
    {
        wp_enqueue_script('rs-js', plugins_url('/resources/frontend/rs.js', __FILE__), array('jquery'), '20160224');

        if (!empty($this->options['google_analytics_enable'])) {
            wp_enqueue_script('rs-ga-js', plugins_url('/resources/frontend/rs_ga.js', __FILE__), array('jquery'),
                '20160224');
        }

        wp_enqueue_style('rs-f', plugins_url('/resources/frontend/rs.css', __FILE__), null, '20151013a');

        $inline_styles = '';

        if (isset($this->options['rs_template']['register_now'])) {
            $inline_styles .= '
            .rs-register-link a, .rs-button {
            border-color: #' . $this->options['rs_template']['register_now'] . ';
            color: #' . $this->options['rs_template']['register_now'] . '!important ;
            }';
        }

        if (isset($this->options['rs_template']['css'])) {
            $inline_styles .= $this->options['rs_template']['css'];
        }
        wp_add_inline_style('rs-f', $inline_styles);
    }

    function configured()
    {
        if (empty($this->options['rs_domain']) || empty($this->get_programs_page()) || empty($this->get_teachers_page()) ) {
            return false;
        }

        return true;
    }

    // Receive a request from secure.retreat.guru to load a program page.
    function receive_preview_request()
    {
        if (isset( $_REQUEST['program'] )  ) {
            if (! $this->configured()) { echo 'Please setup the booking plugin before using this feature'; exit(); }
            $url = $this->get_page_url('programs') . '/' . $_REQUEST['program'];
            wp_redirect($url);
            exit();
        }
    }

    function on_activate_upgrade()
    {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
        RS_Upgrade::init();
    }

}

global $RS_Connect;
$RS_Connect = new RS_Connect();
