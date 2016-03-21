<?php
/**
 * The Template for Single Programs.
 */

get_header();
$options = get_option('rs_settings');
if (isset($options['rs_template']['before'])) echo $options['rs_template']['before']; ?>

<?php $rs_the_program = $RS_Connect->program; ?>

        <article class="page type-page status-publish single-program">

            <div class="entry-content">

                <h1 class="rs-program-title"><?php echo $rs_the_program->title; ?></h1>

                <p class="rs-program-date"><?php echo $rs_the_program->date; ?></p>

                <div class="rs-program-meta">

                    <?php  if ( $rs_the_program->photo_details ) : ?>
                        <div class="rs-program-photo">
                            <img src="<?php echo $rs_the_program->photo_details->large->url; ?>" width="100%">
                        </div>
                    <?php endif; ?>

                    <div class="rs-meta-content-container">

                        <?php // Pricing ?>
                        <?php if ( $rs_the_program->price_details ) : ?>
                            <div class="rs-program-price"><?php echo $rs_the_program->price_details ?></div>
                        <?php endif; ?>

                        <?php // Datetime details ?>
                        <?php if ( $rs_the_program->date_time ) : ?>
                            <p class="rs-program-datetime"><span class="rs-program-label">Date &amp; Time Details:</span> <?php echo $rs_the_program->date_time; ?></p>
                        <?php endif; ?>

                        <?php // Location ?>
                        <?php if ( $rs_the_program->location ) : ?>
                            <p class="rs-program-location"><span class="rs-program-label">Location:</span> <?php echo $rs_the_program->location; ?></p>
                        <?php endif; ?>

                        <?php // Address ?>
                        <?php if ( $rs_the_program->address ) : ?>
                            <p class="rs-program-address"><span class="rs-program-label">Address:</span> <?php echo $rs_the_program->address; ?></p>
                        <?php endif; ?>

                        <?php // Contact details ?>
                        <?php if ( $rs_the_program->contact ) : ?>
                            <p class="rs-program-contact"><?php echo $rs_the_program->contact ?></p>
                        <?php endif; ?>

                        <?php // Custom fields ?>
                        <?php if ( $rs_the_program->custom ) : ?>
                            <div class="rs-program-custom-wrap"><?php echo $rs_the_program->custom; ?></div>
                        <?php endif; ?>

                        <?php if ( $rs_the_program->email && empty($options['rs_template']['hide_contact_button'])) : ?>
                        <a href="mailto:<?php echo $rs_the_program->email; ?>?subject=An inquiry about <?php echo $rs_the_program->title; ?>" class="rs-button">Email us about program</a>
                        <?php endif; ?>

                        <div class="rs-regsitration-wrap"><?php echo $rs_the_program->registration_action; ?></div>

                    </div>
                </div>

                <div class="rs-regsitration-wrap"><?php echo $rs_the_program->registration_action; ?></div>

                <?php // Program Details ?>
                <div class="rs-program-content">
                    <?php if ( $rs_the_program->text ) : ?>
                        <div class="rs-program-custom-wrap"><?php echo $rs_the_program->text_full; ?></div>
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

                <h3>Teachers</h3>
                    <?php foreach($rs_the_program->teacher_details->teacher_objects as $teacher) : ?>
                        <div class="teacher" style="clear:left; position:relative;">
                            <div style="float:left; width:<?php echo $teacher->photo_details->thumbnail->width; ?>px; margin-right:20px;">
                                <a href="<?php echo get_site_url(); ?>/teacher/<?php echo $teacher->ID; ?>/<?php echo $teacher->post_name; ?>"><img src="<?php echo $teacher->photo_details->thumbnail->url; ?>" style="float:left; margin:5px 20px 10px 0;"></a>
                            </div>
                            <div style="overflow:hidden;">
                                <a href="<?php echo get_site_url(); ?>/teacher/<?php echo $teacher->ID; ?>/<?php echo $teacher->post_name; ?>"><strong><?php echo $teacher->post_title; ?></strong></a><br/>
                                <?php echo $RS_Connect->excerpt($teacher->post_content); ?>
                                <br/><a href="<?php echo get_site_url(); ?>/teacher/<?php echo $teacher->ID; ?>/<?php echo $teacher->post_name; ?>">Learn more about <?php echo $teacher->post_title; ?></a>
                            </div>

                        </div>
                        </div>
                    <?php endforeach; ?>

            <?php endif; ?>
</div>
          <div style="clear:both;"></div>

</article>

<?php if (isset($options['rs_template']['after'])) echo $options['rs_template']['after']; ?>

<?php get_footer(); ?>

