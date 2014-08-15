<?php
/**
 * Plugin options page.
 *
 * @package   DigiPass
 * @author    Labs64 <info@labs64.com>
 * @license   GPL-2.0+
 * @link      http://www.labs64.com
 * @copyright 2014 Labs64
 */


define('DP_OPTIONS', 'DP_OPTIONS');
define('API_KEY', '31c7bc4e-90ff-44fb-9f07-b88eb06ed9dc');


if (is_admin()) {
    // Add the options page and menu item.
    add_action('admin_menu', 'add_plugin_page');
    add_action('admin_init', 'page_init');

    // Add an action link pointing to the options page.
    $plugin_basename = plugin_basename(plugin_dir_path(__FILE__) . 'digipass.php');
    add_filter('plugin_action_links_' . $plugin_basename, 'add_action_links');

    // Load admin style sheet and JavaScript.
    add_action('admin_enqueue_scripts', 'enqueue_admin_styles');
    add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');

    // Get media data callback registration
    add_action('wp_ajax_validate', 'validate_callback');
    add_action('wp_ajax_get_media_data', 'get_media_data_callback');
}

/**
 * Add settings action link to the plugins page.
 */
function add_action_links($links)
{
    return array_merge(
        array(
            'settings' => '<a href="' . admin_url('options-general.php?page=digipass') . '">' . __('Settings', DP_SLUG) . '</a>'
        ),
        $links
    );
}

/**
 * Add options page
 */
function add_plugin_page()
{
    global $plugin_screen_hook_suffix;
    $plugin_screen_hook_suffix = add_options_page(
        __('DigiPass', DP_SLUG),
        __('DigiPass', DP_SLUG),
        'manage_options',
        DP_SLUG,
        'create_admin_page'
    );
}

/**
 * Register and enqueue admin-specific style sheet.
 *
 * @return    null    Return early if no settings page is registered.
 */
function enqueue_admin_styles()
{
    global $plugin_screen_hook_suffix;

    if (!isset($plugin_screen_hook_suffix)) {
        return;
    }

    $screen = get_current_screen();
    if ($screen->id == $plugin_screen_hook_suffix) {
        wp_enqueue_style(DP_SLUG . '-admin-styles', plugins_url('css/dp-admin.css', __FILE__), array(), DP_VERSION);
    }

}

/**
 * Register and enqueue admin-specific JavaScript.
 *
 * @return    null    Return early if no settings page is registered.
 */
function enqueue_admin_scripts()
{
    global $plugin_screen_hook_suffix;

    if (!isset($plugin_screen_hook_suffix)) {
        return;
    }

    $screen = get_current_screen();
    if ($screen->id == $plugin_screen_hook_suffix) {
        wp_enqueue_script(DP_SLUG . '-admin-script', plugins_url('js/dp-admin.js', __FILE__), array('jquery'), DP_VERSION);
    }

}

/**
 * Options page callback
 */
function create_admin_page()
{
    ?>
    <div class="wrap" xmlns="http://www.w3.org/1999/html">
        <a href="http://www.labs64.com" target="_blank" class="icon-labs64 icon32"></a>

        <h2><?php _e('DigiPass by Labs64', DP_SLUG); ?></h2>

        <form method="post" action="options.php">
            <?php
            // This prints out all hidden setting fields
            settings_fields('DP_OPTIONS_GROUP');
            settings_fields_hidden();
            do_settings_sections(DP_SLUG);
            submit_button();
            ?>
        </form>
        <hr/>
        <?php
        print_reference_section();
        ?>
    </div>
    <div class="info_menu">
        <?php
        print_features_section();
        print_divider();
        print_feedback_section();
        ?>
    </div>
<?php
}

/**
 * Print sections divider
 */
function print_divider()
{
    ?>
    <hr/>
<?php
}

/**
 * Print the Section info text
 */
function get_on_off($opt)
{
    if ($opt == '1') {
        return "<span class='label-on'>ON</span>";
    } else {
        return "<span class='label-off'>OFF</span>";
    }
}

/**
 * Print the Common-Section info text
 */
function print_common_section_info()
{
}

/**
 * Print the Retriever-Section info text
 */
function print_retriever_section_info()
{
    print __('Some Image Data Retriever needs additional configuration', DP_SLUG);
}

/**
 * Returns available plugin features
 */
function ct_get_features_array()
{
    $features = array(
        'ct_feature_retriever' => __('Image data retriever (Free)', DP_SLUG)
    );
    return $features;
}

/**
 * Get features list.
 */
function print_features_list($features)
{
    $ret = '<ul id="ct_features">';
    foreach ($features as $key => $value) {
        $ret .= '<li id="' . $key . '">&nbsp;' . $value . ' - ' . get_on_off(get_single_option($key)) . '</li>';
    }
    $ret .= '</ul>';
    print $ret;
}

/**
 * Print the features section
 */
function print_features_section()
{
    $ct_feature_retriever = get_single_option('ct_feature_retriever');

    ?>
    <h3><?php _e('Features', DP_SLUG); ?></h3>
    <p><?php _e('Available plugin features', DP_SLUG); ?>:</p>

    <?php print_features_list(ct_get_features_array()); ?>

    <button id="validate" type="button""><?php _e('Validate'); ?></button>
    <br/>
    <div style="font-style: italic; color: rgb(102, 102, 102); font-size: smaller;"><p>Powered by <a
                href="http://www.labs64.com/netlicensing"
                target="_blank">NetLicensing</a></p>
    </div>
<?php
}

/**
 * Print the feedback section
 */
function print_feedback_section()
{
    ?>
    <h3><?php _e('Feedback', DP_SLUG); ?></h3>

    <p><?php _e('Did you find a bug? Have an idea for a plugin? Please help us improve this plugin', DP_SLUG); ?>:</p>
    <ul>
        <li>
            <a href="https://github.com/Labs64/digipass/issues"
               target="_blank"><?php _e('Report a bug, or suggest an improvement', DP_SLUG); ?></a>
        </li>
        <li><a href="http://www.facebook.com/labs64" target="_blank"><?php _e('Like us on Facebook'); ?></a>
        </li>
        <li><a href="http://www.labs64.com/blog" target="_blank"><?php _e('Read Labs64 Blog'); ?></a></li>
    </ul>
<?php
}

/**
 * Print the reference section
 */
function print_reference_section()
{
    ?>
    <h3><?php _e('Shortcodes Reference', DP_SLUG); ?></h3>
    <table class="form-table">
        <tbody>
        <tr valign="top">
            <th scope="row">
                [caption]
            </th>
            <td>
                <p>Override WordPress [caption] shortcode.</p>

                <p>Attributes:</p>

                <p>&nbsp;&nbsp;<strong>text</strong> <i>(optional)</i> - custom attribute to override standard media caption. The
                    default behavior, if not specified standard media caption will be used.</p>

                <p><strong>Examples:</strong></p>

                <p><code>[caption]...[/caption]</code></p>

                <p>Override [caption] shortcode</p>

                <p><code>[caption text="image caption"]...[/caption]</code></p>

                <p>Override [caption] shortcode and use <i>text</i> instead of the standard media property</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                [digipass_table]
            </th>
            <td>
                <p>Generate 'Image Credits' table.</p>

                <p>Attributes:</p>

                <p>&nbsp;&nbsp;<strong>id</strong> <i>(optional)</i> - specify the attachment ID (one or more). The
                    default behavior, if no ID is specified, is to display all images containing author info.</p>

                <p>&nbsp;&nbsp;<strong>size</strong> <i>(optional)</i> - specify the image size to use for the thumbnail
                    display. Valid values include "thumbnail", "medium", "large", "full" or numeric values (e.g. "50" or "100x50").
                    The default value is "thumbnail".</p>

                <p>&nbsp;&nbsp;<strong>style</strong> <i>(optional)</i> - specify the table CSS style. Valid values
                    include "default", "mercury", "mars". The default value is "default".</p>

                <p><strong>Examples:</strong></p>

                <p><code>[digipass_table]</code></p>

                <p>Generate table for all images with non-empty attribute 'author' and small (thumbnail) preview
                    image</p>

                <p><code>[digipass_table id="11,22,33" size="medium" style="mercury"]</code></p>

                <p>Generate table for with image ids (11, 22 and 33) and medium preview image. Table will be styled with
                    "mercury" CSS style</p>
            </td>
        </tr>
        </tbody>
    </table>
<?php
}

/**
 * Register and add settings
 */
function page_init()
{
    register_setting(
        'DP_OPTIONS_GROUP', // Option group
        DP_OPTIONS, // Option name
        'sanitize' // Sanitize
    );

    add_settings_section(
        'DP_COMMON_SETTINGS', // ID
        __('DigiPass Settings', DP_SLUG), // Title
        'print_common_section_info', // Callback
        DP_SLUG // Page
    );

    add_settings_section(
        'DP_RETRIEVER_SETTINGS', // ID
        __('Retriever Settings', DP_SLUG), // Title
        'print_retriever_section_info', // Callback
        DP_SLUG // Page
    );

    add_settings_field(
        'ct_copyright_format',
        __('Copyright format', DP_SLUG),
        'ct_text_field_callback',
        DP_SLUG,
        'DP_COMMON_SETTINGS',
        array(
            'id' => 'ct_copyright_format',
            'description' => __('Default copyright format (HTML allowed)<br/>Allowed placeholders: %ident_nr%, %source%, %author%, %publisher%, %license%, %link%<br/>...as well as standard attributes: %title%, %caption%', DP_SLUG),
        )
    );

    add_settings_field(
        'ct_override_caption_shortcode',
        __('Override shortcodes', DP_SLUG),
        'ct_checkbox_field_callback',
        DP_SLUG,
        'DP_COMMON_SETTINGS',
        array(
            'id' => 'ct_override_caption_shortcode',
            'caption' => __('Override WordPress [caption] shortcode', DP_SLUG),
            'description' => __('Replaces output of standard WordPress [caption] shortcode with improved version (add Image Microdata and Image Credit)', DP_SLUG),
        )
    );

    add_settings_field(
        'ct_auth_flickr_apikey',
        __('Flickr api_key', DP_SLUG),
        'ct_text_field_callback',
        DP_SLUG,
        'DP_RETRIEVER_SETTINGS',
        array(
            'id' => 'ct_auth_flickr_apikey',
            'description' => __('To use the Flickr data retriever you need to have an Flickr API application key.' . ' <a href="https://www.flickr.com/services/api/misc.api_keys.html" target="_blank">See here</a>' . ' for more details.', DP_SLUG),
        )
    );
}

/**
 * Sanitize each setting field as needed
 *
 * @param array $input Contains all settings fields as array keys
 */
function sanitize($input)
{
    if (empty($input['ct_copyright_format'])) {
        if (is_admin()) {
            add_settings_error(DP_OPTIONS, 'empty-copyright-format', 'Please specify copyright format.');
        }
    } else {
        $input['ct_copyright_format'] = $input['ct_copyright_format'];
    }

    $input['ct_auth_flickr_apikey'] = sanitize_text_field($input['ct_auth_flickr_apikey']);

    return $input;
}

/**
 */
function settings_fields_hidden()
{
    print_settings_field_hidden('ct_feature_retriever');
}

/**
 */
function print_settings_field_hidden($id)
{
    $value = get_single_option($id);
    echo "<input type='hidden' id='$id' name='DP_OPTIONS[$id]' value='$value' />";
}

/**
 */
function ct_text_field_callback($args)
{
    $id = $args['id'];
    $description = $args['description'];
    $value = get_single_option($id);
    echo "<input type='text' id='$id' name='DP_OPTIONS[$id]' value='$value' class='regular-text' />";
    echo "<p class='description'>$description</p>";
}

function ct_checkbox_field_callback($args)
{
    $id = $args['id'];
    $caption = $args['caption'];
    $description = $args['description'];
    $value = get_single_option($id);
    echo "<input type='checkbox' id='$id' name='DP_OPTIONS[$id]' value='1' class='code' " . checked(1, $value, false) . " /> $caption";
    echo "<p class='description'>$description</p>";
}

/**
 * Returns default options.
 * If you override the options here, be careful to use escape characters!
 */
function get_default_options()
{
    $default_options = array(
        'ct_feature_retriever' => '0',
        'ct_copyright_format' => '&copy; %author%',
        'ct_auth_flickr_apikey' => '',
        'ct_override_caption_shortcode' => '0'
    );
    return $default_options;
}

/**
 * Retrieves (and sanitises) options
 */
function get_options()
{
    $options = get_default_options();
    $stored_options = get_option(DP_OPTIONS);
    if (!empty($stored_options)) {
        sanitize($stored_options);
        $options = wp_parse_args($stored_options, $options);
    }
    update_option(DP_OPTIONS, $options);
    return $options;
}

/**
 * Retrieves single option
 */
function get_single_option($name)
{
    $options = get_options();
    return $options[$name];
}

/**
 * Set single option value
 */
function set_single_option($name, $value)
{
    $options = get_options();
    $options[$name] = $value;
    update_option(DP_OPTIONS, $options);
}

/**
 * Returns available sources meta
 */
function ct_get_sources_names_array()
{
    $names = array();

    foreach (ct_get_sources_array() as $k => $v) {
        $names[$k] = $v['caption'];
    }

    return $names;
}

/**
 * Returns source caption
 */
function ct_get_source_caption($source)
{
    $sources = ct_get_sources_array();
    if (isset($sources[$source]) && !empty($sources[$source]['caption'])) {
        return $sources[$source]['caption'];
    } else {
        return $source;
    }
}

/**
 * Returns source copyright format
 */
function ct_get_source_copyright($source)
{
    $sources = ct_get_sources_array();
    if (isset($sources[$source]) && !empty($sources[$source]['copyright'])) {
        return call_user_func($sources[$source]['copyright']);
    }
}

/**
 * Returns source metadata
 */
function ct_get_source_metadata($source, $number)
{
    $sources = ct_get_sources_array();
    if (isset($sources[$source]) && !empty($sources[$source]['retriever'])) {
        return call_user_func($sources[$source]['retriever'], $number);
    }
}

/**
 * Validate allowed features against Labs64 Netlicensing
 */
function validate_callback()
{
    // validate features
    $nlic = new NetLicensing(API_KEY);
    $res = $nlic->validate('CT', strip_url(get_site_url(), 1000), urlencode(get_site_url()));

    // TODO: process NetLicensing response

    // update options
    set_single_option('ct_feature_retriever', '1');

    // prepare return values
    $licenses = array(
        'netlicensing_response' => $res,
        'ct_feature_retriever' => get_single_option('ct_feature_retriever')
    );
    echo json_encode($licenses);

    die(); // this is required to return a proper result
}

/**
 * Media data callback
 */
function get_media_data_callback()
{
    $item = ct_get_source_metadata($_POST['source'], $_POST['ident_nr']);

    $mediadata = array(
        'source' => $_POST['source'],
        'ident_nr' => $_POST['ident_nr'],
        'author' => $item['author'],
        'publisher' => $item['publisher'],
        'license' => $item['license'],
        'link' => $item['link']
    );

    echo json_encode($mediadata);

    die(); // this is required to return a proper result
}

/**
 * Returns available sources meta
 */
function ct_get_sources_array()
{
    $sources = array(
        'custom' => array(
            'caption' => __('Custom', DP_SLUG),
            'copyright' => '',
            'retriever' => ''
        ),
        'Fotolia' => array(
            'caption' => 'Fotolia',
            'copyright' => 'get_fotolia_copyright',
            'retriever' => 'get_fotolia_metadata'
        ),
        'iStockphoto' => array(
            'caption' => 'iStockphoto',
            'copyright' => 'get_istockphoto_copyright',
            'retriever' => 'get_istockphoto_metadata'
        ),
        'Shutterstock' => array(
            'caption' => 'Shutterstock',
            'copyright' => 'get_shutterstock_copyright',
            'retriever' => 'get_shutterstock_metadata'
        ),
        'Corbis_Images' => array(
            'caption' => 'Corbis Images',
            'copyright' => 'get_corbis_images_copyright',
            'retriever' => 'get_corbis_images_metadata'
        ),
        'Getty_Images' => array(
            'caption' => 'Getty Images',
            'copyright' => 'get_getty_images_copyright',
            'retriever' => 'get_getty_images_metadata'
        ),
        'pixelio' => array(
            'caption' => 'Pixelio',
            'copyright' => 'get_pixelio_copyright',
            'retriever' => 'get_pixelio_metadata'
        ),
        'flickr' => array(
            'caption' => 'Flickr',
            'copyright' => 'get_flickr_copyright',
            'retriever' => 'get_flickr_metadata'
        ),
        'freeimages' => array(
            'caption' => 'Freeimages',
            'copyright' => 'get_freeimages_copyright',
            'retriever' => 'get_freeimages_metadata'
        )
    );
    return $sources;
}

/**
 * Fotolia: copyright
 */
function get_fotolia_copyright()
{
    return Fotolia::COPYRIGHT;
}

/**
 * Fotolia: metadata
 */
function get_fotolia_metadata($number)
{
    $parser = new Fotolia();
    return $parser->execute($number);
}

/**
 * iStockphoto: copyright
 */
function get_istockphoto_copyright()
{
    return IStockphoto::COPYRIGHT;
}

/**
 * iStockphoto: metadata
 */
function get_istockphoto_metadata($number)
{
    $parser = new IStockphoto();
    return $parser->execute($number);
}

/**
 * Shutterstock: copyright
 */
function get_shutterstock_copyright()
{
    return '&copy; %author%';
}

/**
 * Shutterstock: metadata
 */
function get_shutterstock_metadata($number)
{
    $item = array();

    $item['author'] = '...not implemented yet...';
    $item['publisher'] = 'Shutterstock';
    $item['license'] = 'Royalty-free';

    return $item;
}

/**
 * Corbis Images: copyright
 */
function get_corbis_images_copyright()
{
    return '&copy; %author%/Corbis';
}

/**
 * Corbis Images: metadata
 */
function get_corbis_images_metadata($number)
{
    $item = array();

    $item['author'] = '...not implemented yet...';
    $item['publisher'] = 'Corbis Images';
    $item['license'] = 'Royalty-free';

    return $item;
}

/**
 * Getty Images: copyright
 */
function get_getty_images_copyright()
{
    return '&copy; %author% / Getty Images';
}

/**
 * Getty Images: metadata
 */
function get_getty_images_metadata($number)
{
    $item = array();

    $item['author'] = '...not implemented yet...';
    $item['publisher'] = 'Getty Images';
    $item['license'] = 'Royalty-free';

    return $item;
}

/**
 * Pixelio: copyright
 */
function get_pixelio_copyright()
{
    return Pixelio::COPYRIGHT;
}

/**
 * Pixelio: metadata
 */
function get_pixelio_metadata($number)
{
    $parser = new Pixelio();
    return $parser->execute($number);
}

/**
 * Flickr: copyright
 */
function get_flickr_copyright()
{
    return Flickr::COPYRIGHT;
}

/**
 * Flickr: metadata
 */
function get_flickr_metadata($number)
{
    $parser = new Flickr(get_single_option('ct_auth_flickr_apikey'));
    return $parser->execute($number);
}

/**
 * Freeimages: copyright
 */
function get_freeimages_copyright()
{
    return Freeimages::COPYRIGHT;
}

/**
 * Freeimages: metadata
 */
function get_freeimages_metadata($number)
{
    $parser = new Freeimages();
    return $parser->execute($number);
}

?>