
<div class="wrap">
    <h2>Retreat Booking Guru Settings</h2>

    <div class="updated">
        <p>For Booking Guru Support please contact us via email: <a href="mailto:support@retreat.guru">support@retreat.guru</a></p>
    </div>

    <form action="options.php" method="post"><?php
        settings_fields('rs_remote_settings');
        do_settings_sections(__FILE__);
        $options = get_option('rs_remote_settings');
        $rs_domain = (! empty($options['rs_domain']) && $options['rs_domain'] != '') ? $options['rs_domain'] : '';
        $test_host = getenv('TEST_HOST');
        $sub_domain_default = getenv('TEST_SUB_DOM') ?: 'tests';
        $base_domain = '.secure.retreat.guru';
        $http = 'https://';

        if (is_string($test_host)) {
            $base_domain = '.'.$test_host;
            $http = 'http://';
        }

        $site_link = $http.$rs_domain.$base_domain.'/wp-admin';

        ?>
        <table class="form-table">
            <tr>
                <th scope="row">Subdomain</th>
                <td>
                    <fieldset>
                        <label><?php  ?>
                            <?php echo $http; ?>
                            <input name="rs_remote_settings[rs_domain]" type="text" id="rs_domain" value="<?php echo $rs_domain; ?>"/>
                            <a target="_blank" href="<?php echo $site_link; ?>"> <?php echo $base_domain; ?></a><br/>
                        </label> <?php if(empty($rs_domain)) { echo "<span style='color:red;'>Required</span>"; } ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Programs Page</th>
                <td>
                    <p>Choose an existing page on your site to host your programs. This page will list all your programs and be the base location for loading individual programs and categories.</p>
                    <fieldset>
                        <select id="page-programs" name="rs_remote_settings[page][programs]">
                            <option value="">-- Select --</option>
                            <?php
                            $args = array(
                                'sort_order' => 'asc',
                                'sort_column' => 'post_title',
                                'post_type' => 'page',
                                'post_status' => 'publish,private,draft',
                            );
                            $pages = get_pages($args);

                            $selected_page = ! empty($options['page']['programs']) ? $options['page']['programs'] : '';
                            foreach($pages as $page) {
                                echo "<option value='{$page->ID}'".selected($selected_page, $page->ID, 0).">{$page->post_title}</option>";
                            }
                            ?>
                        </select> <?php if(empty($selected_page)) { echo "<span style='color:red;'>Required</span>"; } ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Teachers Page</th>
                <td>
                    <p>Choose an existing page on your site to host your teachers. This page will list your teachers and be the base location for loading individual teachers.</p>
                    <fieldset>
                        <select id="page-teachers" name="rs_remote_settings[page][teachers]">
                            <option value="">-- Select --</option>
                            <?php
                            $args = array(
                                'sort_order' => 'asc',
                                'sort_column' => 'post_title',
                                'post_type' => 'page',
                                'post_status' => 'publish,private,draft',
                            );
                            $pages = get_pages($args);

                            $selected_page = ! empty($options['page']['teachers']) ? $options['page']['teachers'] : '';
                            foreach($pages as $page) {
                                echo "<option value='{$page->ID}'".selected($selected_page, $page->ID, 0).">{$page->post_title}</option>";
                            }
                            ?>
                        </select> <?php if(empty($selected_page)) { echo "<span style='color:red;'>Required</span>"; } ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Google Analytics</th>
                <td>
                    <fieldset>
                        <label><input type="checkbox" name="rs_remote_settings[google_analytics_enable]" value="1" <?php echo (isset($options['google_analytics_enable'])) ? checked($options['google_analytics_enable'], '1') : ''; ?>>
                            Enable Google Analytics tracking (and e-commerce)</label><br>
                        <small>Enabling this will allow you to track users from your site to the Retreat Booking Guru registration form and registration completion page. In order to track how much was spent you need to enable E-commerce Tracking in your Google Analytics admin settings.</small>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Image thumbnails</th>
                <td>
                    <fieldset>
                        <select name="rs_remote_settings[rs_template][image_size]">
                            <?php $image_size = ! empty($options['rs_template']['image_size']) ? $options['rs_template']['image_size'] : 'medium'; ?>
                            <option value="thumbnail" <?php selected($image_size, 'thumbnail') ?>>Small - Square Cropped</option>
                            <option value="medium" <?php selected($image_size, 'medium') ?>>Medium - Uncropped</option>
                        </select>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Highlight Color</th>
                <td>
                    <fieldset>
                        <label>
                            #<input name="rs_remote_settings[rs_template][register_now]" type="text" id="rs_remote_settings[rs_template][register_now]"
                                    value="<?php echo (isset($options['rs_template']['register_now']) && $options['rs_template']['register_now'] != '') ? $options['rs_template']['register_now'] : ''; ?>"/>
                        </label> Used for Register Now button and early bird discounts
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Excerpt length</th>
                <td>
                    <fieldset>
                        <p>Set a word limit for teacher and program descriptions on your listings.</p>
                        <label>
                            <input name="rs_remote_settings[rs_template][limit_description]" type="text" id="rs_remote_settings[rs_template][limit_description]" style="width:70px;"
                                    value="<?php echo (isset($options['rs_template']['limit_description']) && $options['rs_template']['limit_description'] != '') ? $options['rs_template']['limit_description'] : '100'; ?>"/> words
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Hide contact button</th>
                <td>
                    <fieldset>
                        <label>
                            <input name="rs_remote_settings[rs_template][hide_contact_button]" type="checkbox" value="1"
                                <?php if (isset($options['rs_template']['hide_contact_button'])) { echo 'checked'; } ?>
                                />
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Customize contact button text</th>
                <td>
                    <fieldset>
                        <label>
                            <input name="rs_remote_settings[rs_template][contact_button_text]" type="text" id="rs_remote_settings[rs_template][contact_button_text]" placeholder="Email us about program"
                                    value="<?php echo (isset($options['rs_template']['contact_button_text']) && $options['rs_template']['contact_button_text'] != '') ? $options['rs_template']['contact_button_text'] : ''; ?>"/>
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
                        <textarea name="rs_remote_settings[rs_template][css]"
                                  type="text" style="width:700px; height:200px;"
                                  id="rs_remote_settings[rs_template][css]"
                        ><?php if (isset($options['rs_template']['css'])) echo trim($options['rs_template']['css']); ?></textarea><br/>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th><a href="javascript:;" id="rs-plugin-settings-show-advanced">Show advanced settings +</a></th>
            </tr>
            <tr class="rs-plugin-settings-advanced-container" style="<?php echo empty($options['rs_template']['js']) ? 'display: none' : ''; ?>">
                <th scope="row">Advanced Settings<br><small>(for developers)</small></th>
                <td>
                    <div>
                        <?php if (current_user_can('publish_pages')) : ?>
                            <fieldset id="rs-connect-settings-show-theme-js">
                                Add JavaScript code below. <strong>* Warning * Be careful, this could break the listings pages</strong><br/>
                                <label>
                            <textarea name="rs_remote_settings[rs_template][js]"
                                      type="text" style="width:700px; height:200px;"
                                      id="rs_remote_settings[rs_template][js]"
                            ><?php if (isset($options['rs_template']['js'])) echo trim($options['rs_template']['js']); ?></textarea><br/>
                            </fieldset>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <tr style="display:none;">
                <th scope="row">Before theme & after</th>
                <td>
                    <fieldset>
                        Wrap template tags around the program listings to fix template bugs<br/>
                        <label>
                                    <textarea name="rs_remote_settings[rs_template][before]" type="text" style="width:700px;height:200px;" id="rs_remote_settings[rs_template][before]"><?php if (isset($options['rs_template']['before'])) echo $options['rs_template']['before']; ?>
                                    </textarea>
                        </label><br/>
                        <label>
                                    <textarea name="rs_remote_settings[rs_template][after]" type="text" style="width:700px;height:100px;" id="rs_remote_settings[rs_template][after]"><?php if (isset($options['rs_template']['after'])) echo $options['rs_template']['after']; ?>
                                    </textarea>
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"></th>
                <?php if (isset($options['style'])) { ?>
                    <input name="rs_remote_settings[style]" type="hidden" value="<?php echo $options['style']; ?>"/>
                <?php } ?>
                <td><input type="submit" style="font-size: 24px;" value="Save"/></td>
            </tr>
        </table>
    </form>
</div>
