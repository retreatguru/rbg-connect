<?php
/**
 * The template for programs archive
 */

get_header();
$options = get_option('rs_settings');
global $api_vars;
if(isset($options['rs_template']['before'])) echo $options['rs_template']['before']; ?>

        <h1><?php _e( 'Programs', 'programs' ); ?></h1>
        <?php
        global $RS_Connect;
        $programs = array_reverse($RS_Connect->get_programs($api_vars));

        if(! empty($programs)) {
        foreach($programs as $program): ?>

            <div class="rs-program rs-group">

                <?php // do_action('rs_before_archive_program'); ?>

                <?php if ( $program->photo_details ) : ?>
                    <div class="rs-program-thumbnail"><a href="/programs/<?php echo $program->ID; ?>/<?php echo $program->slug; ?>"><img src="<?php echo $program->photo_details->thumbnail->url; ?>"></a></div>
                <?php endif; ?>

                <?php if ( $program->title ) : ?>
                    <h2 class="rs-program-title"><a href="/programs/<?php echo $program->ID; ?>/<?php echo $program->slug; ?>"><?php echo $program->title; ?></a></h2>
                <?php endif; ?>

                <?php if ( $program->date ) : ?>
                    <div class="rs-program-date"><?php echo $program->date; ?></div>
                <?php endif; ?>

                <?php if ( $program->location) : ?>
                    <div class="rs-program-location"><?php echo $program->location; ?></div>
                <?php endif; ?>

                <?php if ( $program->text) : ?>
                    <div class="rs-program-excerpt"><?php echo wp_trim_words($program->text, 100); ?></div>
                <?php endif; ?>

                <?php do_action('rs_after_archive_program'); ?>

            </div>

        <? endforeach; } else { echo 'Sorry, no programs exist here.'; }?>

        </div>

        <?php get_sidebar(); ?>

<?php if(isset($options['rs_template']['after'])) echo $options['rs_template']['after']; ?>

<?php get_footer(); ?>
