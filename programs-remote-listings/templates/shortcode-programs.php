<?php
global $RS_Connect;
if (is_array($shortcode_atts)) extract($shortcode_atts);
if (! empty($rs_the_programs)) {

    foreach($rs_the_programs as $program): ?>

        <?php $details_url = $program->alternate_url ? $program->alternate_url : get_site_url().'/'.$RS_Connect->style.'/'.$program->ID.'/'.$program->slug; ?>

        <div class="rs-program rs-group">

            <?php if ( $program->photo_details && empty($hide_photo) ) : ?>
                <div class="rs-program-thumbnail"><a href="<?php echo $details_url; ?>"><img src="<?php echo $program->photo_details->thumbnail->url; ?>"></a></div>
            <?php endif; ?>

            <?php if ( $program->title && empty($hide_title) ) : ?>
                <h2 class="rs-program-title"><a href="<?php echo $details_url; ?>"><?php echo $program->title; ?></a></h2>
            <?php endif; ?>

            <?php if ( $program->date && empty($hide_date) ) : ?>
                <div class="rs-program-date"><?php echo $program->date; ?></div>
            <?php endif; ?>

            <?php if ( $program->location && empty($hide_location) ) : ?>
                <div class="rs-program-location"><?php echo $program->location; ?></div>
            <?php endif; ?>

            <?php if ( $program->text && empty($hide_text) ) : ?>
                <div class="rs-program-excerpt"><?php echo wp_trim_words($program->text, 100); ?></div>
            <?php endif; ?>

            <?php if (! empty($show_register_link) ) : ?>
                <?php if ($program->registration_wait_list): ?>
                    <a href="<?php echo $program->registration_link; ?>" target="_blank">Join waiting list</a>
                <?php elseif ($program->registration_bookable): ?>
                    <a href="<?php echo $program->registration_link; ?>" target="_blank">Register Now</a>
                <?php else: ?>
                    <?php if (empty($program->registration_action)) { echo "Closed"; } else { echo $program->registration_action; } ?>
                <?php endif; ?>
            <?php endif; ?>

        </div>

    <?php endforeach; } else { echo 'Sorry, no programs exist here.'; }?>
