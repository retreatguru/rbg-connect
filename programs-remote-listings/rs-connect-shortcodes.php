<?php

class RS_Connect_Shortcodes {
    public function __construct() {
        add_shortcode('rs_programs', [$this, 'shortcode_programs']);
        add_shortcode('rs_program', [$this, 'shortcode_program']);
        add_shortcode('rs_register_button', [$this, 'shortcode_register_button']);
        add_shortcode('rs_teachers', [$this, 'shortcode_teachers']);
        add_shortcode('rs_teacher', [$this, 'shortcode_teacher']);
    }

    public function shortcode_programs($atts)
    {
        global $rs_the_programs;
        global $shortcode_atts;

        $shortcode_atts = $this->normalize_empty_atts($atts);

        $vars = null;

        if (isset($atts['category'])) {
            $vars .= 'category='.$atts['category'];
        }

        $template = 'shortcode-programs.php';
        if (! empty($shortcode_atts['table'])) {
            $template = 'shortcode-programs-table.php';
        }

        $rs_the_programs = RS_Connect_Api::get_programs($vars);
        if (! is_array($rs_the_programs)) {
            return '';
        }

        $rs_the_programs = array_reverse($rs_the_programs); // todo: We should probably do this to our api directly (newest first)

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
        $shortcode_atts = $this->normalize_empty_atts($atts);

        return $this->include_shortcode_template('shortcode-programs-single.php');
    }

    public function shortcode_register_button($atts)
    {
        global $shortcode_atts;

        if (! isset($shortcode_atts['id'])) {
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

        $vars = null;

        $template = 'shortcode-teachers.php';

        $rs_the_teachers = RS_Connect_Api::get_teachers();
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
            return [];
        }

        foreach ($atts as $attribute => $value) {
            if (is_int($attribute)) {
                $atts[strtolower($value)] = true;
                unset($atts[$attribute]);
            }
        }

        return $atts;
    }
}

new RS_Connect_Shortcodes();
