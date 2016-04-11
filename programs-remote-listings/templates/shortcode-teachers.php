<?php
global $RS_Connect;
global $rs_the_teachers;
global $shortcode_atts;
$options = get_option('rs_remote_settings');
if (is_array($shortcode_atts)) extract($shortcode_atts);
if (! empty($rs_the_teachers)) {
    foreach ($rs_the_teachers as $teacher) :
        $details_url = $RS_Connect->get_page_url('teachers').$teacher->ID.'/'.$teacher->slug; ?>
        <div class="rs-teacher rs-group">
            <div class="teacher type-teacher status-publish has-post-thumbnail hentry rs-teacher rs-group">
                <?php if (isset($teacher->photo_details->thumbnail)) { ?>
                    <div class="rs-teacher-thumbnail"><a href="<?php echo $details_url; ?>"><img width="150" height="150" src="<?php echo $teacher->photo_details->thumbnail->url; ?>" class="attachment-thumbnail wp-post-image" alt="DavidRome700" /></a></div>
                <?php } ?>
                <h2 class="rs-teacher-title"><a href="<?php echo $details_url; ?>"><?php echo $teacher->name; ?></a></h2>
                <p class="rs-teacher-excerpt"><?php echo wp_trim_words($teacher->text, 100); ?></p>
            </div>
        </div>
    <?php endforeach; } else { echo 'Sorry, no teachers exist here.'; } ?>