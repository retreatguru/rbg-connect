<?php
/**
 * The Template for Single Programs.
 */

get_header();
$options = get_option('rs_settings');
if(isset($options['rs_template']['before'])) echo $options['rs_template']['before']; ?>

<?php global $rs_the_program; ?>

        <article class="page type-page status-publish single-program">
                <header class="entry-header">
                <h1 class="rs-program-title"><?php echo $rs_the_program->title; ?></h1>
                </header>

                <div class="entry-content">
                <?php  if ( $rs_the_program->photo_details ) : ?>
                <div class="rs-program-photo">
                    <img src="<?php echo $rs_the_program->photo_details->medium->url; ?>" width="<? echo $rs_the_program->photo_details->medium->width; ?>" height="<? echo $rs_the_program->photo_details->medium->height; ?>">
                </div>
                <?php endif; ?>
                <p class="rs-program-date"><?php echo $rs_the_program->date; ?></p>

                <div class="rs-regsitration-wrap"><?php echo $rs_the_program->registration_action; ?></div>

                <div class="rs-program-meta">

                    <?php // Pricing ?>
                    <?php if ( $rs_the_program->price_details ) : ?>
                        <div class="rs-program-price"><?php echo $rs_the_program->price_details ?></div>
                    <?php endif; ?>

                    <?php // Datetime details ?>
                    <?php if ( $rs_the_program->date_time ) : ?>
                        <p class="rs-program-datetime"><span class="rs-program-label">Date and Time Details:</span> <?php echo $rs_the_program->date_time; ?></p>
                    <?php endif; ?>

                    <?php // Location ?>
                    <?php if ( $rs_the_program->location ) : ?>
                        <p class="rs-program-location"><span class="rs-program-label">Location:</span> <?php echo $rs_the_program->location; ?></p>
                    <?php endif; ?>

                    <?php // Location Address ?>
                    <?php if ( $rs_the_program->address ) : ?>
                        <p class="rs-program-location-address"><span class="rs-program-label">Address:</span> <?php echo $rs_the_program->address; ?></p>
                    <?php endif; ?>

                    <?php // Contact details ?>
                    <?php if ( $rs_the_program->contact ) : ?>
                        <p class="rs-program-contact"><?php echo $rs_the_program->contact ?></p>
                    <?php endif; ?>

                    <?php // Information Message ?>
                    <?php if ( $rs_the_program->message ) : ?>
                        <p class="rs-program-message-link">
                            <a href="#"><?php _e( 'View Visitor Information' ); ?> <span>+</span></a>
                        </p>
                        <div class="rs-program-message" style="display:none;"><?php echo wpautop( $rs_the_program->message ); ?></div>
                    <?php endif; ?>

                    <?php // Custom fields ?>
                    <?php if ( $rs_the_program->custom ) : ?>
                        <div class="rs-program-custom-wrap"><?php echo $rs_the_program->custom; ?></div>
                    <?php endif; ?>
                </div>

                <?php // Program Details ?>
                <div class="rs-program-content">
                    <?php if ( $rs_the_program->text ) : ?>
                        <div class="rs-program-custom-wrap"><?php echo wpautop($rs_the_program->text_full); ?></div>
                    <?php endif; ?>
                </div>

            <?php // Category(ies) ?>
            <?php $program_cats = class_exists( 'RS_Enhanced_Plugin') ? rs_has_program_categories() : null; ?>
            <?php if ( $program_cats ) : ?>
                <p class="rs-program-categories">
                    <span class="rs-program-label"><?php echo _n( 'Category', 'Categories', count( $program_cats ) ) ?>:</span>
                    <?php echo get_the_term_list( 0, 'program_category', '', ', ' ); ?>
                </p>
            <?php endif; ?>

            <?php if ( $rs_the_program->additional_info ) : ?>
                <div class="rs-program-additional-info"><?php echo $rs_the_program->additional_info; ?></div>
            <?php endif; ?>

            <?php if ( $rs_the_program->teacher_details->teacher_objects ) : ?>
                <div style="clear:left; float:left;">
                <h3>Teachers</h3>
                    <?php foreach($rs_the_program->teacher_details->teacher_objects as $teacher) : ?>
                        <div class="teacher" style="clear:left; float:left; padding:10px 0;">
                            <p>
                                <a href="/teacher/<?php echo $teacher->ID; ?>/<?php echo $teacher->post_name; ?>"><img src="<?php echo $teacher->photo_details->thumbnail->url; ?>" style="float:left; margin:0 20px 10px 0;"></a>
                                <strong><?php echo $teacher->post_title; ?></strong><br/>

                                <?php echo wp_trim_words( nl2br($teacher->post_content), 70, '...' ); ?>
                                <br/><a href="/teacher/<?php echo $teacher->ID; ?>/<?php echo $teacher->post_name; ?>">Learn more about <?php echo $teacher->post_title; ?></a>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
</article>

<?php if(isset($options['rs_template']['after'])) echo $options['rs_template']['after']; ?>

<?php get_footer(); ?>

