<?php
global $RS_Connect;
global $rs_the_teachers;
global $shortcode_atts;
$options = get_option('rs_remote_settings');
$image_size = ! empty($options['rs_template']['image_size']) ? $options['rs_template']['image_size'] : 'medium';
if (is_array($shortcode_atts)) extract($shortcode_atts);
if (! empty($rs_the_teachers)) {
    foreach ($rs_the_teachers as $teacher) :
        $details_url = $RS_Connect->get_page_url('teachers').$teacher->ID.'/'.$teacher->slug; ?>
        <div class="rs-teacher rs-group">
            <div class="teacher type-teacher status-publish has-post-thumbnail hentry rs-teacher rs-group"
            id="rs-single-teacher-id-<?php echo $teacher->ID; ?>">
                <?php if (isset($teacher->photo_details->thumbnail)) : ?>
                    <div class="rs-teacher-thumbnail"><a href="<?php echo $details_url; ?>"><img src="<?php echo $teacher->photo_details->{$image_size}->url; ?>" class="attachment-thumbnail wp-post-image" alt="DavidRome700" /></a></div>
                <?php endif; ?>
                <div class="rs-teacher-content-wrap">
                    <h2 class="rs-teacher-title"><a href="<?php echo $details_url; ?>"><?php echo $teacher->name; ?></a></h2>
                    <p class="rs-teacher-excerpt"><?php echo wp_trim_words($teacher->text, 100); ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; } else { echo 'Sorry, no teachers exist here.'; } ?>