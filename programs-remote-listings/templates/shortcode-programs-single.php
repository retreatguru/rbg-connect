<?php
global $RS_Connect;
// Maybe we set and get these as class properties
global $shortcode_atts;
global $rs_the_program;
$options = get_option('rs_remote_settings');
?>

<?php
if (is_array($shortcode_atts)) extract($shortcode_atts); ?>

<article class="single-program">

    <div class="entry-content">

        <h1 class="rs-program-title"><?php echo $rs_the_program->title; ?></h1>

        <?php if (! empty($rs_the_program->teacher_list)) : ?>
            <h2 class="rs-program-teacher"><?php echo $rs_the_program->teacher_list; ?></h2>
        <?php endif; ?>

        <p class="rs-program-date"><?php echo $rs_the_program->date; ?></p>

        <div class="rs-regsitration-wrap rs-regsitration-wrap-top"><?php echo $rs_the_program->registration_action; ?></div>

        <div class="rs-program-meta">
            <?php  if ($rs_the_program->photo_details) : ?>
                <div class="rs-program-photo">
                    <img src="<?php echo $rs_the_program->photo_details->large->url; ?>" width="100%">
                </div>
            <?php endif; ?>

            <div class="rs-meta-content-container">

                <?php if ($rs_the_program->early_bird_discount && empty($hide_discount)) : ?>
                    <div class="rs-program-early-bird-discount rs-highlight"><?php echo $rs_the_program->early_bird_discount; ?></div>
                <?php endif; ?>

                <?php // Pricing ?>
                <?php if ($rs_the_program->price_details) : ?>
                    <div class="rs-program-price"><?php echo $rs_the_program->price_details ?></div>
                <?php endif; ?>

                <?php // Datetime details ?>
                <?php if ($rs_the_program->date_time) : ?>
                    <p class="rs-program-datetime"><span class="rs-program-label">Date &amp; Time Details:</span> <?php echo $rs_the_program->date_time; ?></p>
                <?php endif; ?>

                <?php // Location ?>
                <?php if ($rs_the_program->location) : ?>
                    <p class="rs-program-location"><span class="rs-program-label">Location:</span> <?php echo $rs_the_program->location; ?></p>
                <?php endif; ?>

                <?php // Address ?>
                <?php if ($rs_the_program->address) : ?>
                    <p class="rs-program-address"><span class="rs-program-label">Address:</span> <?php echo $rs_the_program->address; ?></p>
                <?php endif; ?>

                <?php // Contact details ?>
                <?php if ($rs_the_program->contact) : ?>
                    <p class="rs-program-contact"><?php echo $rs_the_program->contact ?></p>
                <?php endif; ?>

                <?php // Custom fields ?>
                <?php if ($rs_the_program->custom) : ?>
                    <div class="rs-program-custom-wrap"><?php echo $rs_the_program->custom; ?></div>
                <?php endif; ?>

                <?php if ($rs_the_program->email && empty($options['rs_template']['hide_contact_button'])) : ?>
                    <?php if (! empty($options['rs_template']['contact_button_text'])) {
                        $contact_button_text = $options['rs_template']['contact_button_text'];
                    } else {
                        $contact_button_text = 'Email us about program';
                    } ?>
                    <a href="mailto:<?php echo $rs_the_program->email; ?>?subject=An inquiry about <?php echo $rs_the_program->title; ?>" class="rs-button"><?php echo $contact_button_text; ?></a>
                <?php endif; ?>

                <div class="rs-regsitration-wrap"><?php echo $rs_the_program->registration_action; ?></div>
            </div>
        </div>

        <?php // Program Details ?>
        <div class="rs-program-content">
            <?php if ($rs_the_program->text_full) : ?>
                <div class="rs-program-custom-wrap"><?php echo $rs_the_program->text_full; ?></div>
            <?php endif; ?>
        </div>

        <?php if ($rs_the_program->teacher_details->teacher_objects) : ?>
            <div class="rs-teachers-container">
                <h2 class="rs-teachers-title"><?php echo _n('Teacher', 'Teachers',
                        count($rs_the_program->teacher_details->teacher_objects)) ?></h2>

                <?php foreach ($rs_the_program->teacher_details->teacher_objects as $teacher) : ?>
                    <?php $teacher_url = $RS_Connect->get_page_url('teachers') . $teacher->ID . '/' . $teacher->slug; ?>
                    <div class="teacher">
                        <?php if (isset($teacher->photo_details->medium)) { ?>
                            <div class="rs-teacher-photo">
                                <a href="<?php echo $teacher_url; ?>">
                                    <img src="<?php echo $teacher->photo_details->medium->url; ?>">
                                </a>
                            </div>
                        <?php } ?>
                        <div class="rs-teachers-content">
                            <a href="<?php echo $teacher_url; ?>"><strong><?php echo $teacher->name; ?></strong></a><br/>
                            <?php echo $RS_Connect->excerpt($teacher->text); ?>
                            <br/><a href="<?php echo $teacher_url; ?>">Learn more about <?php echo $teacher->name; ?></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div style="clear:both;"></div>

</article>
