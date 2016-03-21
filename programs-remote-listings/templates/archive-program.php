<?php
/**
 * The template for programs archive 
 */

get_header();
$options = get_option('rs_settings');
if (isset($options['rs_template']['before'])) { echo $options['rs_template']['before']; }

global $rs_api_vars;
global $RS_Connect;

$programs = array_reverse($RS_Connect->get_programs($rs_api_vars));

?>

<h1 class="rs-archive-title"><?php _e( ucfirst($RS_Connect->style).'s', $RS_Connect->style.'s' ); ?></h1>
<?php

if (! empty($programs)) {
foreach($programs as $program):
    $image_size = ! empty($options['rs_template']['image_size']) ? $options['rs_template']['image_size'] : 'medium';
    $details_url = $program->alternate_url ? $program->alternate_url : get_site_url().'/'.$RS_Connect->style.'/'.$program->ID.'/'.$program->slug; ?>

    <div class="rs-program rs-group">

        <?php if (! empty($program->photo_details->{$image_size}->url) ) : ?>
            <?php  $program_image_url = $program->photo_details->{$image_size}->url; ?>
            <div class="rs-program-thumbnail"><a href="<?php echo $details_url; ?>"><img src="<?php echo $program_image_url; ?>"></a></div>
        <?php endif; ?>

        <?php if ( $program->title ) : ?>
            <h2 class="rs-program-title"><a href="<?php echo $details_url; ?>"><?php echo $program->title; ?></a></h2>
        <?php endif; ?>

        <?php if ( $program->date ) : ?>
            <div class="rs-program-date"><?php echo $program->date; ?></div>
        <?php endif; ?>

        <?php if ( $program->location) : ?>
            <div class="rs-program-location"><?php echo $program->location; ?></div>
        <?php endif; ?>

        <?php if ( $program->address) : ?>
            <div class="rs-program-address"><?php echo $program->address; ?></div>
        <?php endif; ?>

        <?php if ( $program->text) : ?>
            <div class="rs-program-excerpt"><?php echo $RS_Connect->excerpt($program->text); ?></div>
        <?php endif; ?>

        <?php do_action('rs_after_archive_program'); ?>

    </div>

<?php endforeach; } else { echo 'Sorry, no programs exist here.'; }?>

<?php if (isset($options['rs_template']['after'])) { echo $options['rs_template']['after']; } ?>

<?php get_footer(); ?>
