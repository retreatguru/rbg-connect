<?php

/*
Plugin Name: Booking Manager Connect
Description: Connect to the Mandala Booking Manager to show program listings on your site and link to registration forms.
Version: 1.2.1
Author: Blue Mandala
Author URI: http://bluemandala.com
*/

class RS_Connect
{

    public function __construct()
    {
        // Base domain to connect with (do not include http://)
        $this->mbm_domain = 'secure.retreat.guru';

        $this->plugin_dir      = plugin_dir_path( __FILE__ );
        $this->includes();

        add_filter('admin_init', array($this, 'rs_flush_rewrite_rules'));
        add_filter('rewrite_rules_array', array($this, 'rs_create_rewrite_rules'));
        add_filter('query_vars', array($this, 'rs_add_query_vars'));
        add_filter('template_include', array($this, 'rs_redirect_intercept'));
        add_filter('wp_title', array($this, 'rs_set_title'), 100);

        add_action('wp_enqueue_scripts', array($this, 'rs_enqueue_items'));
        add_action('admin_menu', array($this, 'rs_admin_menu_items'));
        add_action('admin_init', array($this, 'rs_register_settings'));
        add_action('admin_notices', array($this, 'my_admin_notice'));

        add_shortcode('rs_programs_table', array($this, 'rs_shortcode_programs_table'));
//      add_shortcode('rs_programs', array($this, 'rs_shortcode_programs'));

    }

    function includes()
    {
        if($this->configured()) include( "{$this->plugin_dir}rs-connect-widgets.php" );
    }

    /*    function rs_shortcode_programs($atts)
        {
            ob_start();
            $this->rs_load_template('archive-program.php');
            $ob_get = ob_get_contents();
            ob_end_clean();

            return $ob_get;
        }*/

    function rs_shortcode_programs_table($atts)
    {
        $vars = null;

        if ($atts['category']) {
            $vars .= 'category=' . $atts['category'] . '&';
        }

        global $rs_the_programs;
        $rs_the_programs = array_reverse($this->get_programs($vars));

        ob_start();
        $this->rs_load_template('shortcode-programs-table.php');
        $ret = ob_get_contents();
        ob_end_clean();

        return $ret;
    }

    function rs_redirect_intercept($template)
    {

        if (get_query_var('pagename') == 'programs') {

            $program = get_query_var('program');

            global $api_vars;
            if(get_query_var('category')) {  $api_vars .= 'category=' . get_query_var('category') . '&'; }

            if (empty($program)) {
                $this->rs_load_template('archive-program.php');
                exit;
            } else {
                global $rs_the_program;
                $rs_the_program = $this->get_program($program);
                $this->rs_load_template('single-program.php');
                exit;
            }

        }

        return $template;
    }

    function rs_load_template($template)
    {
        if ($overridden_template = locate_template($template)) {
            load_template($overridden_template);
        } else {
            load_template(plugin_dir_path(__FILE__) . 'templates/' . $template);
        }
    }

    public function get_program($id)
    {
        return json_decode($this->cURL($this->get_url_to_mbm() . '/wp-json/events/' . $id));
    }

    public function get_programs($vars = null)
    {
        return json_decode($this->cURL($this->get_url_to_mbm() . '/wp-json/events/?' . $vars . rand()));
    }

    function rs_enqueue_items()
    {
        wp_enqueue_style('rs-f', plugins_url('/resources/frontend/rs.css', __FILE__));
    }

    function rs_set_title($title)
    {
        if(get_query_var('category')) $category = get_query_var('category'); else $category = '';
        if (get_query_var('pagename') == 'programs') {
            return 'Programs | ' . $category . get_bloginfo('name');
        }


        return $title;
    }

    function rs_admin_menu_items()
    {

        add_menu_page('Booking Manager', 'Booking Manager', 'manage_options', 'booking-manager.php', arraY(&$this, 'admin_programs_page'), 'dashicons-calendar-alt');
        add_submenu_page('booking-manager.php', 'Program List', 'Program List', 'manage_options', 'booking-manager.php', array(&$this, 'admin_programs_page'));
        add_submenu_page('booking-manager.php', 'Settings', 'Settings', 'manage_options', 'options-mbm', array(&$this, 'admin_settings_page'));
    }

    function rs_activate()
    {
        global $wp_rewrite;
        $this->rs_flush_rewrite_rules();
    }

    function rs_create_rewrite_rules($rules)
    {
        global $wp_rewrite;
        $newRule = array(
            'programs/category/(.+)' => 'index.php?pagename=programs' . '&category=' . $wp_rewrite->preg_index(1),
            'programs/(.+)/(.+)' => 'index.php?pagename=programs' . '&program=' . $wp_rewrite->preg_index(1),
            'programs/' => 'index.php?pagename=programs' . '&program='
        );

        $newRules = $newRule + $rules;

        return $newRules;
    }


    function rs_add_query_vars($qvars)
    {
        $qvars[] = 'program';
        $qvars[] = 'programs';
        $qvars[] = 'vars';
        $qvars[] = 'category';

        return $qvars;
    }

    function rs_flush_rewrite_rules()
    {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

    private function cURL($url)
    {
        $curl = curl_init();
        $connect = false;

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array("API-Version: 1")
        ));

        $connect = curl_exec($curl);
        if ($connect == false) {
            // Error notice
        } else {
            return $connect;
        }

    }


    /*
    * Register the settings
    */

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
            <p>Please specify your mandala booking manager subdomain.
                    <a href="<?php echo admin_url('admin.php?page=options-mbm'); ?>">Click Here</a></p>
        </div>
    <?php
    }

    function get_url_to_mbm()
    {
        $options = get_option('rs_settings');
        if (empty($options['rs_domain'])) {
            return 'http://' . $this->mbm_domain;
        } else {
            return 'http://' . $options['rs_domain'] . "." . $this->mbm_domain;
        }

    }

    function admin_programs_page()
    {

        $rs_programs = $this->get_programs();

        ?>

        <div style="clear:left; margin:20px 20px 20px 0;">
            <a href="<?php echo $this->get_url_to_mbm(); ?>/wp-admin/admin.php?page=rs-programs" class="button">View All Programs</a>
            <a href="<?php echo $this->get_url_to_mbm(); ?>/wp-admin/admin.php?page=registrations" class="button">View All Registrations</a>
            <a href="<?php echo $this->get_url_to_mbm(); ?>/wp-admin/admin.php?page=rs-transactions" class="button">View All Finance</a>
        </div>

        <table class="wp-list-table widefat fixed posts">
            <thead>
            <tr>
                <th width="400">Program</th>
                <th width="100">Dates</th>
                <th width="100">Registration</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($rs_programs as $program): ?>
                <tr>
                    <td><a href="<?php echo $this->get_url_to_mbm(); ?>/wp-admin/post.php?action=edit&post=<?php echo $program->ID; ?>"><?php echo $program->title; ?></a></td>
                    <td><?php echo $program->date; ?></td>
                    <td><?php echo ucfirst($program->registration_status); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    <?php

    }

    //The markup for your plugin settings page
    function admin_settings_page()
    {
        ?>
        <div class="wrap">
            <h2>Mandala Booking Manager Settings</h2>

            <form action="options.php" method="post"><?php
                settings_fields('rs_settings');
                do_settings_sections(__FILE__);

                //get the older values, wont work the first time
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
                                    <span class="description">Please enter a valid subdomain.</span>
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
                                    <textarea name="rs_settings[rs_template][before]" type="text" id="rs_settings[rs_template][before]"><?php if(isset($options['rs_template']['before'])) echo $options['rs_template']['before']; ?>
                                    </textarea>
                                </label><br/>
                                <label>
                                    <textarea name="rs_settings[rs_template][after]" type="text" id="rs_settings[rs_template][after]"><?php if(isset($options['rs_template']['after'])) echo $options['rs_template']['after']; ?>
                                    </textarea>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                </table>
                <input type="submit" value="Save"/>
            </form>
        </div>
    <?php
    }


}

global $RS_Connect;
$RS_Connect = new RS_Connect();
register_activation_hook(__file__, array($RS_Connect, 'rs_activate'));
