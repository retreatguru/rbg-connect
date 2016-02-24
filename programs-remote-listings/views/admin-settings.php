
<div class="wrap">
    <h2>Retreat Booking Guru Settings</h2>

    <form action="options.php" method="post"><?php
        settings_fields('rs_settings');
        do_settings_sections(__FILE__);

        $options = get_option('rs_settings'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">Subdomain</th>
                <td>
                    <fieldset>
                        <label>
                            https:// <input name="rs_settings[rs_domain]" type="text" id="rs_domain"
                                            value="<?php echo (isset($options['rs_domain']) && $options['rs_domain'] != '') ? $options['rs_domain'] : ''; ?>"/>
                            .<?php echo $this->mbm_domain; ?> <br/>
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Label</th>
                <td>
                    <fieldset>
                        What kind of experience do you offer?<br/>
                        <small>This provides the correct url structure and page titles</small><br/>
                        <input type="radio" name="rs_settings[style]" value="program" <?php if ($options['style'] == 'program' || ! isset($options['style'])) { echo "checked"; } ?>>Programs<br>
                        <input type="radio" name="rs_settings[style]" value="event" <?php if ($options['style'] == 'event') { echo "checked"; } ?>>Events<br>
                        <input type="radio" name="rs_settings[style]" value="retreat" <?php if ($options['style'] == 'retreat') { echo "checked"; } ?>>Retreats<br>
                        <input type="radio" name="rs_settings[style]" value="workshop" <?php if ($options['style'] == 'workshop') { echo "checked"; } ?>>Workshops<br>
                        <input type="radio" name="rs_settings[style]" value="trip" <?php if ($options['style'] == 'trip') { echo "checked"; } ?>>Trips<br>
                        <input type="radio" name="rs_settings[style]" value="tour" <?php if ($options['style'] == 'tour') { echo "checked"; } ?>>Tours
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Google Analytics</th>
                <td>
                    <fieldset>
                        <label><input type="checkbox" name="rs_settings[google_analytics_enable]" value="1" <?php echo checked($options['google_analytics_enable'], '1'); ?>>
                            Enable Google Analytics tracking (and e-commerce)</label><br>
                        <small>Enabling this will allow you to track users from your site to the Retreat Booking Guru registration form and registration completion page. In order to track how much was spent you need to enable E-commerce Tracking in your Google Analytics admin settings.</small>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Image thumbnails</th>
                <td>
                    <fieldset>
                        <select name="rs_settings[rs_template][image_size]">
                            <?php $image_size = ! empty( $options['rs_template']['image_size'] ) ? $options['rs_template']['image_size'] : 'medium'; ?>
                            <option value="thumbnail" <?php selected($image_size, 'thumbnail') ?>>Small - Square Cropped</option>
                            <option value="medium" <?php selected($image_size, 'medium') ?>>Medium - Uncropped</option>
                        </select>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Register Now Button Color</th>
                <td>
                    <fieldset>
                        <label>
                            #<input name="rs_settings[rs_template][register_now]" type="text" id="rs_settings[rs_template][register_now]"
                                    value="<?php echo (isset($options['rs_template']['register_now']) && $options['rs_template']['register_now'] != '') ? $options['rs_template']['register_now'] : ''; ?>"/>
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Hide contact button</th>
                <td>
                    <fieldset>
                        <label>
                            <input name="rs_settings[rs_template][hide_contact_button]" type="checkbox" value="1"
                                <?php if (isset($options['rs_template']['hide_contact_button'])) { echo "checked"; } ?>
                                />
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Before theme & after</th>
                <td>
                    <fieldset>
                        Wrap template tags around the program listings to fix template bugs<br/>
                        <label>
                                    <textarea name="rs_settings[rs_template][before]" type="text" style="width:700px;height:200px;" id="rs_settings[rs_template][before]"><?php if (isset($options['rs_template']['before'])) echo $options['rs_template']['before']; ?>
                                    </textarea>
                        </label><br/>
                        <label>
                                    <textarea name="rs_settings[rs_template][after]" type="text" style="width:700px;height:100px;" id="rs_settings[rs_template][after]"><?php if (isset($options['rs_template']['after'])) echo $options['rs_template']['after']; ?>
                                    </textarea>
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Style Adjustments</th>
                <td>
                    <fieldset>
                        Customize or add CSS site styles below<br/>
                        <label>
                                    <textarea name="rs_settings[rs_template][css]" type="text" style="width:700px; height:200px;" id="rs_settings[rs_template][css]"><?php if (isset($options['rs_template']['css'])) echo trim($options['rs_template']['css']); ?>
                                    </textarea><br/>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"></th>
                <td><input type="submit" style="font-size: 24px;" value="Save"/></td>
            </tr>
        </table>
    </form>
</div>
