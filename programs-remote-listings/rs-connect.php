<?php

/*
Plugin Name: Retreat Booking Guru Connect
Description: Connect to Retreat Booking Guru to show program listings on your site and link to registration forms.
Version: 2.3.2
Author: Retreat Guru
Author URI: http://retreat.guru/booking
*/

class RS_Connect
{
    protected $options = null;
    protected $program = null;
    public static $plugin_version = 'wp2.3.2'; // todo: always update this with wp + the plugin Version set above

    public function __construct()
    {
        $this->options = get_option('rs_remote_settings');
        $this->plugin_dir = plugin_dir_path(__FILE__);
        $this->includes();

        add_filter('init', array($this, 'setup_rewrites'));

        add_filter('the_content', array($this, 'insert_shortcode'));

        add_action('wp_head', array($this, 'set_program_meta'));
        // we're forcing the ga script to be at the bottom of the page so that we have access to it
        add_action('wp_footer', array($this, 'enqueue_footer_items'), 99999);
        add_filter('pre_get_document_title', array($this, 'set_program_title'));

        add_action('admin_menu', array($this, 'admin_add_menu_items'));
        add_action('admin_init', array($this, 'admin_register_settings'));
        add_action('admin_notices', array($this, 'admin_setup_notice'));

        add_filter('query_vars', array($this, 'register_query_var'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_header_items'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_header_items'));

        add_filter('body_class', array($this, 'body_classes'));
        add_action('template_redirect', array($this, 'receive_preview_request'));
        register_activation_hook(__FILE__, array($this, 'on_activate_upgrade'));

        add_action('init', array('RS_Upgrade_Remote', 'init'));
        add_filter('wp', array($this, 'remove_rel_canonical'));
    }

    public function includes()
    {
        require "{$this->plugin_dir}rs-connect-shortcodes.php";
        require "{$this->plugin_dir}rs-connect-api.php";
        require "{$this->plugin_dir}rs-connect-upgrade.php";

        if ($this->configured()) {
            include "{$this->plugin_dir}rs-connect-widgets.php";
        }
    }

    public function remove_rel_canonical()
    {
        if (get_query_var('rs_program') || get_query_var('rs_teacher') || get_query_var('rs_category')) {
            remove_action('wp_head', 'rel_canonical');
        }
    }

    public function setup_rewrites()
    {
        if (! $this->configured()) {
            return;
        }

        // Programs
        $programs_page = $this->get_page('programs');
        $programs_page_slug = $programs_page->post_name;
        add_rewrite_rule($programs_page_slug.'/category/([^/]*)/?',
            'index.php?page_id='.$programs_page->ID.'&rs_category=$matches[1]', 'top');
        add_rewrite_rule($programs_page_slug.'/([^/]*)/?',
            'index.php?page_id='.$programs_page->ID.'&rs_program=$matches[1]', 'top');
        add_rewrite_rule($programs_page_slug.'/([^/]*)/([^/]*)/?',
            'index.php?page_id='.$programs_page->ID.'&rs_program=$matches[1]', 'top');

        // Teachers
        $teachers_page = $this->get_page('teachers');
        $teachers_page_slug = $teachers_page->post_name;
        add_rewrite_rule($teachers_page_slug.'/category/([^/]*)/?',
            'index.php?page_id='.$teachers_page->ID.'&rs_category=$matches[1]', 'top');
        add_rewrite_rule($teachers_page_slug.'/([^/]*)/([^/]*)/?',
            'index.php?page_id='.$teachers_page->ID.'&rs_teacher=$matches[1]', 'top');

        // Legacy Rules: We migrate existing installs: This is useful if the new teacher / program slug is somehow different than the old one.
        if (empty($this->options['style'])) {
            return;
        }
        add_rewrite_rule($this->options['style'].'s/?$', 'index.php?page_id='.$programs_page->ID, 'top');
        add_rewrite_rule($this->options['style'].'s/category/([^/]*)/?',
            'index.php?page_id='.$programs_page->ID.'&rs_category=$matches[1]', 'top');
        add_rewrite_rule($this->options['style'].'/([^/]*)/?',
            'index.php?page_id='.$programs_page->ID.'&rs_program=$matches[1]', 'top');
        add_rewrite_rule($this->options['style'].'/([^/]*)/([^/]*)/?',
            'index.php?page_id='.$programs_page->ID.'&rs_program=$matches[1]', 'top');

        add_rewrite_rule('teachers/?$', 'index.php?page_id='.$teachers_page->ID, 'top');
        add_rewrite_rule('teachers/rs_category/([^/]*)/?',
            'index.php?page_id='.$teachers_page->ID.'&rs_category=$matches[1]', 'top');
        add_rewrite_rule('teacher/([^/]*)/([^/]*)/?',
            'index.php?page_id='.$teachers_page->ID.'&rs_teacher=$matches[1]', 'top');
    }

    public function register_query_var($vars)
    {
        $vars[] = 'rs_programs';
        $vars[] = 'rs_program';
        $vars[] = 'rs_teachers';
        $vars[] = 'rs_teacher';
        $vars[] = 'rs_category';

        return $vars;
    }

    public function insert_shortcode($content)
    {
        if ($this->configured()) {
            $current_page = $GLOBALS['post']->post_name ?? null;

            // Return early if unable to determine the post_name from the page
            if (! $current_page) {
                return $content;
            }

            if ($current_page == $this->get_programs_page()->post_name) {
                return $this->use_shortcode('rs_program', $content);
            }

            if ($current_page == $this->get_teachers_page()->post_name) {
                return $this->use_shortcode('rs_teacher', $content);
            }
        }

        return $content;
    }

    public function use_shortcode($shortcode, $content)
    {
        // Load a specific program or teacher
        $program_id = get_query_var($shortcode);
        if ($program_id) {
            return "[{$shortcode} id='{$program_id}']";
        }

        // Load a category of programs
        $category_slug = get_query_var('rs_category');
        if ($category_slug) {
            return "[{$shortcode}s category='{$category_slug}']";
        }

        // if the shortcode is already in the content, don't auto-add it
        // Otherwise return either a list of programs or teachers plus default content on this page.
        if (strpos($content, $shortcode)) {
            return $content;
        } else {
            return "$content<br>[{$shortcode}s]";
        }
    }

    public function set_program_meta()
    {
        if (get_query_var('rs_program')) {
            $program_id = get_query_var('rs_program');
            $this->program = RS_Connect_Api::get_program($program_id);

            if (! $this->program || ! is_object($this->program)) {
                return;
            }

            $program_url = $this->get_page_url('programs').$this->program->ID.'/'.$this->program->slug;
            $meta_description = ! empty($this->program->seo_description) ? $this->program->seo_description : wp_trim_words($this->program->text, 50, '...');

            if (! empty($this->program->text)) {
                echo '<meta property="og:url" content="'.$program_url.'/" />'."\n";
                echo '<meta property="og:title" content="'.$this->program->title.'" />'."\n";

                $medium = $this->program->photo_details->medium ?? null;
                if (is_object($medium)) {
                    echo '<meta property="og:image" content="'.$medium->url.'" />'."\n";
                    echo '<meta property="og:image:width" content="'.$medium->width.'" />'."\n";
                    echo '<meta property="og:image:height" content="'.$medium->height.'" />'."\n";
                }

                echo '<meta property="og:description" content="'.$meta_description.'" />'."\n";
                echo '<meta name="description" content="'.$meta_description.'" />'."\n";
            }
        }
    }

    public function set_program_title($title = null)
    {
        if (get_query_var('rs_program')) {
            $program_id = get_query_var('rs_program');
            $this->program = RS_Connect_Api::get_program($program_id);

            if (! empty($this->program->seo_title)) {
                $program_title = $this->program->seo_title;
            } elseif (! empty($this->program->title)) {
                $program_title = $this->program->title.' | '.get_bloginfo('name');
            } else {
                $program_title = get_bloginfo('name');
            }

            return $program_title;
        }

        return $title;
    }

    public function get_programs_page()
    {
        return $this->get_page('programs');
    }

    public function get_teachers_page()
    {
        return $this->get_page('teachers');
    }

    public function get_page($page)
    {
        if (empty($this->options['page']) || empty($this->options['page'][$page])) {
            return false;
        }

        $page_id = $this->options['page'][$page];
        $page = get_post($page_id);

        return $page;
    }

    /**
     * @param $type - Either programs or teachers
     *
     * @return false|string
     */
    public function get_page_url($type)
    {
        $entity_base = $this->get_page($type);
        if (! $entity_base) {
            return false;
        }

        return get_permalink($entity_base->ID);
    }

    public function admin_add_menu_items()
    {
        add_menu_page('Retreat Booking Guru', 'Retreat Booking Guru', 'manage_options', 'booking-manager.php',
            array($this, 'admin_programs_page'), 'dashicons-calendar-alt');
        add_submenu_page('booking-manager.php', 'Program & Help', 'Program & Help', 'manage_options',
            'booking-manager.php', array($this, 'admin_programs_page'));
        add_submenu_page('booking-manager.php', 'Retreat Guru Settings', 'Retreat Guru Settings', 'manage_options',
            'options-mbm', array($this, 'admin_settings_page'));
    }

    public function admin_register_settings()
    {
        register_setting('rs_remote_settings', 'rs_remote_settings');
    }

    public function admin_setup_notice()
    {
        if ($this->configured()) {
            return true;
        }
        ?>
        <div class="error">
            <p>Important: <a href="<?php echo admin_url('admin.php?page=options-mbm');
                ?>">Setup Retreat Booking Guru</a></p>
        </div>
        <?php

    }

    public function admin_programs_page()
    {
        include $this->plugin_dir.'/views/admin-main.php';
    }

    public function admin_settings_page()
    {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();

        include $this->plugin_dir.'/views/admin-settings.php';
    }

    public function excerpt($description)
    {
        $limit = ! empty($this->options['rs_template']['limit_description']) ? $this->options['rs_template']['limit_description'] : 100;

        return wp_trim_words($description, $limit);
    }

    public function enqueue_header_items()
    {
        wp_enqueue_script('rs-js', plugins_url('/resources/frontend/rs.js', __FILE__), array('jquery'), self::$plugin_version);

        wp_enqueue_style('rs-f', plugins_url('/resources/frontend/rs.css', __FILE__), null, self::$plugin_version);

        $inline_styles = '';

        if (isset($this->options['rs_template']['register_now'])) {
            $inline_styles .= '
            .rs-register-link a, .rs-button, .rs-highlight {
            border-color: #'.$this->options['rs_template']['register_now'].';
            color: #'.$this->options['rs_template']['register_now'].'!important ;
            }';
        }

        if (isset($this->options['rs_template']['css'])) {
            $inline_styles .= $this->options['rs_template']['css'];
        }

        wp_add_inline_style('rs-f', $inline_styles);

        if (isset($this->options['rs_template']['js'])) {
            $inline_script = $this->options['rs_template']['js'];
            wp_add_inline_script('rs-js', $inline_script);
        }
    }

    public function enqueue_footer_items()
    {
        if (! empty($this->options['google_analytics_enable'])) {
            wp_register_script('rs-ga-js', plugins_url('/resources/frontend/rs_ga.js', __FILE__), null, self::$plugin_version, true);
            wp_print_scripts('rs-ga-js');
        }
    }

    public function enqueue_admin_header_items()
    {
        wp_enqueue_script('rs-js', plugins_url('/resources/admin/rs-admin.js', __FILE__), array('jquery'), self::$plugin_version);
        wp_enqueue_style('rs-f', plugins_url('/resources/admin/rs.css', __FILE__), null, self::$plugin_version);
    }

    public function configured()
    {
        if (empty($this->options['rs_domain']) || ! $this->get_programs_page() || ! $this->get_teachers_page()) {
            return false;
        }

        return true;
    }

    function body_classes($classes)
    {
        $current_page = $GLOBALS['post']->post_name ?? null;

        // If page cannot be detected, return the existing classes
        if (! $current_page) {
            return $classes;
        }

        $programs_page = $this->get_programs_page();
        $teachers_page = $this->get_teachers_page();

        if ($programs_page && $current_page == $this->get_programs_page()->post_name) {
            if (get_query_var('rs_program')) {
                $classes[] = 'rs-programs-single';
            } else {
                $classes[] = 'rs-programs';
            }
        }

        if ($teachers_page && $current_page == $this->get_teachers_page()->post_name) {
            if (get_query_var('rs_teacher')) {
                $classes[] = 'rs-teachers-single';
            } else {
                $classes[] = 'rs-teachers';
            }
        }

        return $classes;
    }

    // Receive a request from secure.retreat.guru to load a program page.
    public function receive_preview_request()
    {
        if (isset($_REQUEST['program'])) {
            if (! $this->configured()) {
                echo 'Please setup the booking plugin before using this feature';
                exit();
            }
            $url = $this->get_page_url('programs').'/'.$_REQUEST['program'];
            wp_redirect($url);
            exit();
        }
    }

    public function on_activate_upgrade()
    {
        //global $wp_rewrite;
        //$wp_rewrite->flush_rules();
        //RS_Upgrade_Remote::init();
    }
}

global $RS_Connect;
$RS_Connect = new RS_Connect();
