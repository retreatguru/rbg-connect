<?php

class RS_Connect_Shortcodes {
    public function __construct() {
        add_shortcode('rs_programs', array($this, 'shortcode_programs'));
        add_shortcode('rs_program', array($this, 'shortcode_program'));
        add_shortcode('rs_register_button', array($this, 'shortcode_register_button'));
        add_shortcode('rs_teachers', array($this, 'shortcode_teachers'));
        add_shortcode('rs_teacher', array($this, 'shortcode_teacher'));
    }

    // todo: we should not be using globals at all here. rather pass vars to the template
    // todo: poor way to create a url. using add_query_args() here or in RS_Connect_Api is better
    // todo: we should be reversing array and limiting results via api
    public function shortcode_programs($atts)
    {
        global $rs_the_programs;
        global $shortcode_atts;

        $shortcode_atts = $this->normalize_empty_atts($atts);

        $vars = 'fields=_program_base_,_program_registration_';

        if (empty($shortcode_atts['table'])) {
            $vars .= ',price_details';
            $template = 'shortcode-programs.php';
        } else {
            $vars .= ',price_details,teacher_details,price_first,custom_fields';
            $template = 'shortcode-programs-table.php';
        }

        if (isset($shortcode_atts['show_first_price'])) {
            $vars .= ',price_first';
        }

        if (isset($shortcode_atts['show_first_teacher_photo'])) {
            $vars .= ',teacher_details';
        }

        if (isset($atts['category'])) {
            $vars .= '&category='.$atts['category'];
        }

        $rs_the_programs = RS_Connect_Api::get_programs($vars);
        if (! is_array($rs_the_programs)) {
            return '';
        }

        $rs_the_programs = array_reverse($rs_the_programs);

        if (isset($atts['limit'])) {
            $rs_the_programs = array_slice($rs_the_programs, 0, $atts['limit']);
        }

        return $this->include_shortcode_template($template);
    }

    public function shortcode_program($atts)
    {
        global $rs_the_program;
        global $shortcode_atts;
        $rs_the_program = RS_Connect_Api::get_program($atts['id']);

        if (empty($rs_the_program->ID)) {
            self::force_404();
        }

        $shortcode_atts = $this->normalize_empty_atts($atts);

        return $this->include_shortcode_template('shortcode-programs-single.php');
    }

    public function shortcode_register_button($atts)
    {
        if (! isset($atts['id'])) {
            return 'Error: You must specify a program ID in your shortcode<br/>e.g. [rs_register_button <strong>id="45"</strong>]';
        }

        // todo: Build link here to customize the link title instead of pulling it dynamically or maybe just search and replace string
        $program = RS_Connect_Api::get_program($atts['id']);

        return $program->registration_action;
    }

    public function shortcode_teachers($atts)
    {
        global $rs_the_teachers;
        global $shortcode_atts;
        $shortcode_atts = $this->normalize_empty_atts($atts);

        $vars = 'fields=_teacher_base_';

        if (isset($atts['category'])) {
            $vars .= '&category='.$atts['category'];
        }

        $rs_the_teachers = RS_Connect_Api::get_teachers($vars);
        
        if (! is_array($rs_the_teachers)) {
            return '';
        }

        return $this->include_shortcode_template('shortcode-teachers.php');
    }

    public function shortcode_teacher($atts)
    {
        global $rs_the_teacher;
        global $shortcode_atts;
        $rs_the_teacher = RS_Connect_Api::get_teacher($atts['id']);

        if (empty($rs_the_teacher->ID)) {
            self::force_404();
        }

        $shortcode_atts = $this->normalize_empty_atts($atts);

        return $this->include_shortcode_template('shortcode-teachers-single.php');
    }

    public function include_shortcode_template($template_file)
    {
        ob_start();
        $template_override = locate_template($template_file);

        if ($template_override) {
            include $template_override;
        } else {
            include plugin_dir_path(__FILE__).'templates/'.$template_file;
        }

        return ob_get_clean();
    }

    public function normalize_empty_atts($atts)
    {
        if (! $atts) {
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

    private static function force_404(): void
    {
        status_header(404);
        nocache_headers();
        include(get_query_template('404'));
        die();
    }
}

new RS_Connect_Shortcodes();
