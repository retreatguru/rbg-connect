<?php
global $RS_Connect;
// Maybe we set and get these as class properties
global $shortcode_atts;
global $rs_the_program;
$options = get_option('rs_remote_settings');

if (empty($rs_the_program)) {
    return;
}

$program_id = ! empty($rs_the_program->ID) ? $rs_the_program->ID : '';
$program_title = ! empty($rs_the_program->title) ? $rs_the_program->title : '';
$date_title = ! empty($rs_the_program->date_title) ? $rs_the_program->date_title : 'Date &amp; Time Details:';
$location_title = ! empty($rs_the_program->location_title) ? $rs_the_program->location_title : 'Location:';
$address_title = ! empty($rs_the_program->address_title) ? $rs_the_program->address_title : 'Address:';
$program_image = $rs_the_program->photo_details->large ?? null;
$early_bird_discount =! empty($rs_the_program->early_bird_discount) ? $rs_the_program->early_bird_discount : '';
$price_details = ! empty($rs_the_program->price_details) ? $rs_the_program->price_details : '';
$date_time = ! empty($rs_the_program->date_time) ? $rs_the_program->date_time : '';
$location = ! empty($rs_the_program->location) ? $rs_the_program->location : '';
$address = ! empty($rs_the_program->address) ? $rs_the_program->address : '';
$contact = ! empty($rs_the_program->contact) ? $rs_the_program->contact : '';
$custom = ! empty($rs_the_program->custom) ? $rs_the_program->custom : '';
$email = ! empty($rs_the_program->email) ? $rs_the_program->email : '';
$registration_action = ! empty($rs_the_program->registration_action) ? $rs_the_program->registration_action : '';
$date = ! empty($rs_the_program->date) ? $rs_the_program->date : '';
$text_full = ! empty($rs_the_program->text_full) ? $rs_the_program->text_full : '';

$teacher_list = ! empty($rs_the_program->teacher_list) ? $rs_the_program->teacher_list : [];
$teacher_details = ! empty($rs_the_program->teacher_details) ? $rs_the_program->teacher_details : '';
$teacher_objects = $teacher_details && ! empty($teacher_details->teacher_objects) ? $teacher_details->teacher_objects : [];
$teacher_settings = $teacher_details && ! empty($teacher_details->teacher_settings) ? $teacher_details->teacher_settings : false;

?>

<?php
if (is_array($shortcode_atts)) extract($shortcode_atts); ?>

<article class="single-program" id="rs-single-program-id-<?php echo $program_id; ?>">

    <div class="entry-content">

        <div class="rs-program-meta">
            <?php if ($program_image->url ?? '') : ?>
                <div class="rs-program-photo">
                    <img src="<?php echo $program_image->url ?? ''; ?>" alt="<?php echo $program_image->alt ?? 'Program featured image'; ?>">
                </div>
            <?php endif; ?>

            <div class="rs-meta-content-container">

                <?php if ($early_bird_discount && empty($hide_discount)) : ?>
                    <div class="rs-program-early-bird-discount rs-highlight"><?php echo $early_bird_discount; ?></div>
                <?php endif; ?>

                <?php // Pricing ?>
                <?php if ($price_details) : ?>
                    <div class="rs-program-price"><?php echo $price_details ?></div>
                <?php endif; ?>

                <?php // Datetime details ?>
                <?php if ($date_time) : ?>
                    <p class="rs-program-datetime"><span class="rs-program-label"><?php echo $date_title; ?></span> <?php echo $date_time; ?></p>
                <?php endif; ?>

                <?php // Location ?>
                <?php if ($location) : ?>
                    <p class="rs-program-location"><span class="rs-program-label"><?php echo $location_title; ?></span> <?php echo $location; ?></p>
                <?php endif; ?>

                <?php // Address ?>
                <?php if ($address) : ?>
                    <p class="rs-program-address"><span class="rs-program-label"><?php echo $address_title; ?></span> <?php echo $address; ?></p>
                <?php endif; ?>

                <?php // Contact details ?>
                <?php if ($contact) : ?>
                    <p class="rs-program-contact"><?php echo $contact ?></p>
                <?php endif; ?>

                <?php // Custom fields ?>
                <?php if ($custom) : ?>
                    <div class="rs-program-custom-wrap"><?php echo $custom; ?></div>
                <?php endif; ?>

                <?php if ($email && empty($options['rs_template']['hide_contact_button'])) : ?>
                    <?php if (! empty($options['rs_template']['contact_button_text'])) {
                        $contact_button_text = $options['rs_template']['contact_button_text'];
                    } else {
                        $contact_button_text = 'Email us about program';
                    } ?>
                    <a href="mailto:<?php echo $email; ?>?subject=An inquiry about <?php echo $program_title; ?>" class="rs-button"><?php echo $contact_button_text; ?></a>
                <?php endif; ?>

                <div class="rs-regsitration-wrap"><?php echo $registration_action; ?></div>
            </div>
        </div>

        <h1 class="rs-program-title"><?php echo $program_title; ?></h1>
        
        <?php if (! empty($teacher_list)) : ?>
            <h2 class="rs-program-teacher"><?php echo $teacher_list; ?></h2>
        <?php endif; ?>
        
        <p class="rs-program-date"><?php echo $date; ?></p>

        <div class="rs-regsitration-wrap"><?php echo $registration_action; ?></div>

        <?php // Program Details ?>
        <div class="rs-program-content">
            <?php if ($text_full) : ?>
                <div class="rs-program-custom-wrap"><?php echo $text_full; ?></div>
            <?php endif; ?>
        </div>

        <?php if ($teacher_objects && $teacher_settings) : ?>
            <div class="rs-teachers-container">
                <?php $teacher_title = ! empty($teacher_settings->title) ? $teacher_settings->title : 'Teacher'; ?>
                <?php $teacher_title_plural = ! empty($teacher_settings->title_plural) ? $teacher_settings->title_plural : 'Teachers'; ?>
                <h2 class="rs-teachers-title"><?php echo _n($teacher_title, $teacher_title_plural,
                        count($teacher_objects)) ?></h2>

                <?php foreach ($teacher_objects as $teacher) : ?>
                    <?php
                    $teacher_id = ! empty($teacher->ID) ? $teacher->ID : '';
                    $teacher_slug = ! empty($teacher->slug) ? $teacher->slug : '';
                    $teacher_name = ! empty($teacher->name) ? $teacher->name : '';
                    $teacher_text = ! empty($teacher->text) ? $teacher->text : '';
                    $teacher_image = $teacher->photo_details->medium ?? null;
                    $teacher_url = $RS_Connect->get_page_url('teachers') . $teacher_id . '/' . $teacher_slug;
                    ?>
                    <div class="teacher" style="clear:left; position:relative;">
                        <?php if (isset($teacher_image)) : ?>
                            <div
                                style="float:left; width:<?php echo $teacher->photo_details->medium->width; ?>px; margin-right:20px;">
                                <a href="<?php echo $teacher_url; ?>" style="float:left; margin:5px 20px 10px 0;">
                                    <img src="<?php echo $teacher_image->url ?? '' ?>" alt="<?php echo $teacher_image->alt ?? 'Teacher profile image'; ?>" style="float:left; margin:5px 20px 10px 0;">
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="rs-teachers-content">
                            <a href="<?php echo $teacher_url; ?>"><strong><?php echo $teacher_name; ?></strong></a><br/>
                            <?php echo $RS_Connect->excerpt($teacher_text); ?>
                            <br/><a href="<?php echo $teacher_url; ?>">Learn more about <?php echo $teacher_name; ?></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div style="clear:both;"></div>

</article>
