    <div class="card" style="margin-top: 0; max-width: 1000px; margin-right: 20px;">
        <h2>Using the Program Listing Shortcode</h2>
        <p>Use the [rs_programs] shortcode to add a list of your programs to any Wordpress post, page or theme file. Simply insert the following into the contents of your page or post and it will automatically list out the programs you've added to Mandala Booking Manager.</p>

        <p><code>[rs_programs]</code></p>


        <h3>Only list one category</h3>
        <p>If you'd like to list programs specific to one program cateogry on your site, add the category attribute with the "slug" value of that category.  Here is an example:</p>

        <p><code>[rs_programs category="featured-events"]</code></p>

        <p>This will now only list programs you've added to a "Featured Events" category. You can create new categories and see a list of categories and their "slugs" by loging in to Mandala Booking Manager and clicking "Programs -> Category"</p>


        <h3>Hide certain properties like the program picture or description</h3>
        <p>If you'd like to simplify the programs list you can add the following attributes to remove certain elements.  Here is an example that would hide everything except the title:</p>

        <p><code>[rs_programs hide_photo hide_date hide_location hide_discount hide_text]</code></p>


        <h3>Add a link directly to the registration form</h3>
        <p>To add a link underneath each program that points directly to the registration form, you can add the following attribute to the shortcode:</p>

        <p><code>[rs_programs show_register_link]</code></p>

        <h3>Table view</h3>
        <p>This view is useful when your programs are mainly identical except for the dates, location or teachers. You can decide what data to show in the table list. All items are optional. A simple and more detailed examples are below:</p>

        <p><code>[rs_programs table show_date show_register_link]</code></p>

        <p><code>[rs_programs table show_date show_availability show_availability_words show_teachers show_title show_location show_price_details show_more_link show_register_link]</code></p>

        <p>Program extra display fields can be accesses using <code>extra_display_field="My Title"</code> where "My Title" is the exact title of the extra display field as set when editing the program.</p>

        <h3>Alternate program description URL</h3>
        <p>If you run one program many times it makes sense to keep the program description on your own website rather than duplicating it in each program then choose table view to display available dates. If so then enter the URL for the page on your website in the "Alternate Description URL" field (inside More Program Details when editing your program).</p>

        <h3>Adding the shortcode to  a template file</h3>
        <p>For websmasters and site developers who want to position a program list that can't be done through editing a post, page or widget, you can also implement this shortcode in a theme file using the following wordpress method:</p>
        <p><code>&#x3C;?php echo do_shortcode(&#x27;[rs_programs]&#x27;); ?&#x3E;</code></p>

        <h3>Customize the color of the register now button</h3>
        <p>Available as an option on the <a href="<?php echo admin_url('admin.php?page=options-mbm'); ?>">settings page</a> to do this.</p>
    </div>