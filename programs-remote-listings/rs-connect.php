<?php

/*
Plugin Name: Retreat Booking Guru Connect
Description: Connect to Retreat Booking Guru to show program listings on your site and link to registration forms.
Version: 1.8.3
Author: Retreat Guru
Author URI: http://retreat.guru/booking
*/

class RS_Connect
{
    // todo: separate this plugin into front end and admin classes
    // todo: stop creating urls via . use add_query_args() or other abstracted method
    // todo: general clean up to make plugin better organized

    protected $options = null;
    protected $program = null;
    protected $https = 'http://';
    protected $mbm_domain = 'programs.dev';
    protected $style = 'program';

    public function __construct()
    {
        $this->options = get_option('rs_settings');
        $this->plugin_dir = plugin_dir_path(__FILE__);
        $this->includes();

        register_activation_hook(__FILE__, array($this, 'activate_or_upgrade'));

        add_action('init', array($this, 'setup_rewrites'));
        add_action('wp_enqueue_scripts', array($this, 'rs_enqueue_items'));
        add_filter('query_vars', array($this, 'register_query_var'));

        add_action('admin_menu', array($this, 'rs_admin_menu_items'));
        add_action('admin_init', array($this, 'rs_register_settings'));
        add_action('admin_notices', array($this, 'my_admin_notice'));

        add_action('wp_head', array($this, 'rs_set_meta'));
        add_filter('pre_get_document_title', array($this, 'set_title'));
        add_filter('the_content', array($this, 'inject_shortcode'));
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
        $programs_page = $this->get_base_page('programs');
        if (empty($programs_page)) { return; } // todo: If this isn't set, we need to prompt the user to set this.
        $programs_page_slug = $programs_page->post_name;

        // Programs
        add_rewrite_rule($programs_page_slug . '/([^/]*)/([^/]*)/?', 'index.php?page_id=' . $programs_page->ID . '&rs_program=$matches[1]', 'top');
        add_rewrite_rule($programs_page_slug . '/category/([^/]*)/?', 'index.php?page_id=' . $programs_page->ID . '&category=$matches[1]', 'top');

        // Legacy Programs may be useless because the migration scripts set the above $slugs to $this->style.
        add_rewrite_rule($this->style . 's/?$', 'index.php?page_id=' . $programs_page->ID, 'top');
        add_rewrite_rule($this->style . 's/category/([^/]*)/?', 'index.php?page_id=' . $programs_page->ID . '&category=$matches[1]', 'top');
        add_rewrite_rule($this->style . '/([^/]*)/?', 'index.php?page_id=' . $programs_page->ID . '&rs_program=$matches[1]', 'top');
        add_rewrite_rule($this->style . '/([^/]*)/([^/]*)/?', 'index.php?page_id=' . $programs_page->ID . '&rs_program=$matches[1]', 'top');

        $teachers_page = $this->get_base_page('teachers');
        if (empty($teachers_page)) { return; } // todo: If this isn't set, we need to prompt the user to set this.
        $teachers_page_slug = $teachers_page->post_name;

        // Teachers
        add_rewrite_rule($teachers_page_slug . '/([^/]*)/([^/]*)/?', 'index.php?page_id=' . $teachers_page->ID . '&rs_teacher=$matches[1]', 'top');
        add_rewrite_rule($teachers_page_slug . '/category/([^/]*)/?', 'index.php?page_id=' . $teachers_page->ID . '&category=$matches[1]', 'top');

        // Legacy Teachers
        add_rewrite_rule('teachers/?$', 'index.php?page_id=' . $teachers_page->ID, 'top');
        add_rewrite_rule('teachers/category/([^/]*)/?', 'index.php?page_id=' . $teachers_page->ID . '&category=$matches[1]', 'top');
        add_rewrite_rule('teacher/([^/]*)/([^/]*)/?', 'index.php?page_id=' . $teachers_page->ID . '&rs_teacher=$matches[1]', 'top');
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

    function inject_shortcode($content)
    {
        $current_page = $GLOBALS['post']->post_name;

        $programs_page = $this->get_programs_page();
        if ($current_page == $programs_page->post_name) {
            return $this->use_shortcode('rs_program');
        }

        $teachers_page = $this->get_teachers_page();
        if ($current_page == $teachers_page->post_name) {
            return $this->use_shortcode('rs_teacher');
        }

        return $content;
    }

    function use_shortcode($shortcode)
    {
        if (! empty(get_query_var($shortcode))) {
            $program_id = get_query_var($shortcode);
            return "[{$shortcode} id='{$program_id}']";
        }

        if (! empty(get_query_var('category'))) {
            $category_slug = get_query_var('category');
            return "[{$shortcode}s category='{$category_slug}']";
        }

        return "[{$shortcode}s]";
    }

    function get_programs_page() {
        return $this->get_base_page('programs');
    }

    function get_teachers_page() {
        return $this->get_base_page('teachers');
    }

    function get_base_page($page)
    {
        $options = get_option('rs_settings');
        if (empty($options['page']) || empty($options['page'][$page])) {
            return null;
        }

        $page_id = $this->options['page'][$page];
        $page = get_post($page_id);

        return $page;
    }

    function get_base_page_url($type)
    {

        // Set a base page fallback, like rbg-programs and rbg-teachers?
        // How to handle draft pages?

        $entity_base = $this->get_base_page($type);

        return get_permalink($entity_base->ID);
    }

    function set_title($title = null)
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

    function rs_set_meta()
    {
        if (isset($this->program->text)) {
            echo '<meta property="og:description" content="' . wp_trim_words($this->program->text, 100, '...') . '" />';
        }

       // echo '<meta property="og:title" content="' . $this->rs_set_title() . '" />';
    }


    function rs_enqueue_items()
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

    function rs_admin_menu_items()
    {
        add_menu_page('Retreat Booking Guru', 'Retreat Booking Guru', 'manage_options', 'booking-manager.php',
            arraY(&$this, 'admin_programs_page'), 'dashicons-calendar-alt');
        add_submenu_page('booking-manager.php', 'Program & Help', 'Program & Help', 'manage_options',
            'booking-manager.php', array(&$this, 'admin_programs_page'));
        add_submenu_page('booking-manager.php', 'Retreat Guru Settings', 'Retreat Guru Settings', 'manage_options',
            'options-mbm', array(&$this, 'admin_settings_page'));
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
        if (empty($this->options['rs_domain'])) {
            return false;
        }

        return true;
    }

    function my_admin_notice()
    {
        if ($this->configured()) {
            return true;
        }
        ?>
        <div class="error">
            <p>Please specify your Retreat Booking Guru subdomain.
                <a href="<?php echo admin_url('admin.php?page=options-mbm'); ?>">Click Here</a></p>
        </div>
        <?php
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

    function excerpt($description)
    {
        $limit = !empty($this->options['rs_template']['limit_description']) ? $this->options['rs_template']['limit_description'] : 100;

        return wp_trim_words($description, $limit);
    }

    function activate_or_upgrade()
    {
        $this->rs_flush_rewrite_rules();
        RS_Upgrade::init();
    }

}

global $RS_Connect;
$RS_Connect = new RS_Connect();
