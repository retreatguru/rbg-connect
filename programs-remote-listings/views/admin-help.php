<div class="card" style="margin-top: 0; max-width: 1000px; margin-right: 20px;">
    <h2>Using the Program Listing Shortcode</h2>
    <p>Use the [rs_programs] shortcode to add a list of your programs to any Wordpress post, page or theme file. Simply insert the following into the contents of your page or post and it will
        automatically list out the programs you've added to Mandala Booking Manager.</p>

    <p><code>[rs_programs]</code></p>

    <h3>Only list one category</h3>
    <p>If you'd like to list programs specific to one program cateogry on your site, add the category attribute with the "slug" value of that category. Here is an example:</p>

    <p><code>[rs_programs category="featured-events"]</code></p>

    <p>This will now only list programs you've added to a "Featured Events" category. You can create new categories and see a list of categories and their "slugs" by loging in to Mandala Booking
        Manager and clicking "Programs -> Category"</p>


    <h3>Hiding and showing program content in list</h3>
    <p>If you'd like to simplify the programs list you can add the following attributes to remove certain elements. Here is an example that would hide everything except the title:</p>

    <p><code>[rs_programs hide_photo hide_date hide_location hide_discount hide_text]</code></p>

    <p>You can also limit the number of programs that are shown in the list view or the table view.</p>
    <p><code>[rs_programs limit="10"]</code></p>

    <h3>Showing extra items that are not enabled by default:</h3>
    <p>If you want to add additional information on the program list page, you can add the following attributes.</p>

    <ul class="rs-connect-help-show-program-list">
        <li>
            <code>show_first_teacher_photo</code>
            <br>show the image of the first teacher (usually used in conjunction with hide_photo to show the teacher photo rather than the program photo)
        </li>
        <li>
            <code>show_first_price</code>
            <br>show the first/lowest price. Outputs "From $xxxx.xx".
        </li>
        <li>
            <code>show_price_details</code>
            <br>show the full price list. Outputs a full price list.
        </li>
        <li>
            <code>show_price_first</code>
            <br>show the lowest of the prices eg. From $50
        </li>
        <li>
            <code>show_more_link="see more..."</code>
            <br>show a link to prompt guests to see more about the program.
            <br><small>Optionally add a value to customize the text.</small>
        </li>
        <li>
            <code>show_availability="Spaces"</code>
            <br>show the number of available spaces left.
            <br><small>Optionally add a value to customize the label text.</small>
        </li>
        <li>
            <code>show_availability_words="Availability"</code>
            <br>show the availability as a word eg: 'Full'.
            <br><small>Optionally add a value to customize the label text.</small>
        </li>
        <li>
            <code>show_register_link="Register"</code>
            <br>show a button to link directly to the registration form.
            <br><small>Optionally add a value to customize the text.</small>
        </li>
        <li>
            <code>wait_list_text="Join waiting list"</code>
            <br>Customize the text for the Join waiting list button.
        </li>
    </ul>

    <style>.rs-connect-help-show-program-list li { margin-top: 2% !important; }</style>

    <p><code>[rs_programs show_first_teacher_photo show_first_price show_price_details show_more_link="see more..." show_availability="Spaces" show_availability_words="Availability" show_register_link="Register Now" wait_list_text="Join waiting list"]</code></p>

    <h3>Add a link directly to the registration form</h3>
    <p>To add a link underneath each program that points directly to the registration form, you can add the following attribute to the shortcode:</p>

    <p><code>[rs_programs show_register_link="Register now"]</code></p>

    <h3>Table view</h3>
    <p>This view is useful when your programs are mainly identical except for the dates, location or teachers. You can decide what data to show in the table list. All items are optional. A simple and
        more detailed examples are below:</p>

    <p><code>[rs_programs table show_date show_register_link]</code></p>

    <p><code>[rs_programs table show_title="Retreats" show_date="Dates" show_availability="Spaces" show_availability_words="Availability" show_teachers="Hosts" show_location="Location" show_price_details="Price" show_price_first="Price from" show_more_link="Details" show_register_link="Register" wait_list_text="Join waiting list"]</code></p>

    <p>Program extra display fields can be accesses using <code>extra_display_field="My Title"</code> where "My Title" is the exact title of the extra display field as set when editing the program.</p>

    <h3>Alternate program description URL</h3>
    <p>If you run one program many times it makes sense to keep the program description on your own website rather than duplicating it in each program then choose table view to display available
        dates. If so then enter the URL for the page on your website in the "Alternate Description URL" field (inside More Program Details when editing your program).</p>

    <h3>Adding the shortcode to a template file</h3>
    <p>For webmasters and site developers who want to position a program list that can't be done through editing a post, page or widget, you can also implement this shortcode in a theme file using
        the following wordpress method:</p>
    <p><code>&#x3C;?php echo do_shortcode(&#x27;[rs_programs]&#x27;); ?&#x3E;</code></p>

    <h3>Customize the color of the register now button</h3>
    <p>Available as an option on the <a href="<?php echo admin_url('admin.php?page=options-mbm'); ?>">settings page</a> to do this.</p>

    <h3>Teachers</h3>
    <p>Show all teachers or by teacher category:</p>
    <p><code>[rs_teachers]</code></p>
    <p><code>[rs_teachers category="feature"]</code></p>

    <h3>Viewing Categories via URL</h3>
    <p>You can see program and teacher categories by just using a url. If your main program URL is '/retreats/' then go to /retreats/category/featured-events/ to see just those categories. Teachers works similarly.</p>

    <h3>Simple Register Button</h3>
    <p><code>[rs_register_button id='33']</code>. This shortcode allows you to make a register now button just by adding the program id.</p>
</div>