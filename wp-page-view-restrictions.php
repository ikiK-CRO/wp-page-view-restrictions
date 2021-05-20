<?php

/**
 * Plugin Name: WP Page View Restrictions
 * Plugin URI: https://wpcompress.appdiz-informatika.hr/
 * Description: This is the very first plugin I ever created.
 * Version: 1.0.0
 * Author: Kristijan Bičak
 * Author URI: www.appdiz-informatika.hr
 **/


function wporg_options_page_html()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <table class="wp-list-table widefat fixed striped table-view-list pages">
            <thead>
                <tr>
                    <td>Title</td>
                    <td>Author</td>
                    <td>Page status</td>
                    <td>Restrictions</td>
                </tr>
            </thead>
            <tbody>
                <?php
                $pages = get_pages();
                foreach ($pages as $page) {
                    $page_id = $page->ID;
                    $page_title = $page->post_title;
                    $author_id = $page->post_author;
                    $page_staus = get_post_status($page_id);
                    $status;
                    if ($page_staus == 'private') {
                        $status =  'private';
                    } else {
                        $status = 'public';
                    }

                    if (get_metadata('post',  $page_id, 'restrictions', true) != 'Non restricted' or $restrictions != 'Restricted') {
                        add_post_meta($page_id, 'restrictions', 'Non restricted', true);
                    }

                    echo "<tr>
                    <td class='title column-title has-row-actions column-primary page-title'>" . $page_title . "</td>
                    <td class='author column-author'>" . get_the_author_meta('display_name', $author_id) . "</td>
                    <td>" . $status . "</td>
                    <td class='change_restrictions' data-id='" . $page_id . "'><a href=''>" . get_metadata('post',  $page_id, 'restrictions', true) . "</a></td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
    add_action('admin_footer', 'my_action_javascript'); // Write our JS below here

    function my_action_javascript()
    {
    ?>
        <script>
            jQuery(document).ready(function() {
                jQuery(".change_restrictions").click(function(e) {
                    e.preventDefault();
                    //console.log(jQuery(this).text())
                    const el = jQuery(this)
                    const id = jQuery(this).attr('data-id')
                    const res = jQuery(this).text();

                    jQuery.get(ajaxurl, {
                            'action': 'my_action',
                            'restrictions': res,
                            'page_id': id
                        })
                        .done(function(data) {
                            if (data != '') {
                                el.find("a").text(data)
                                // alert('Restrictions set to: ' + data);
                            }
                        });
                });
            });
        </script>


<?php

    }
}
function wporg_options_page()
{
    add_submenu_page(
        'options-general.php',
        'WP Page View Restrictions',
        'WP Page View Restrictions',
        'manage_options',
        'wporg',
        'wporg_options_page_html'
    );
}
add_action('admin_menu', 'wporg_options_page');
add_action('wp_ajax_my_action', 'my_action');

function my_action()
{
    global $wpdb;

    if (isset($_GET['restrictions'])) {
        $page_id = $_GET['page_id'];
        if ($_GET['restrictions'] == 'Non restricted') {
            update_post_meta($page_id, 'restrictions', 'Restricted');
            echo "Restricted";
        } else {
            update_post_meta($page_id, 'restrictions', 'Non restricted');
            echo "Non restricted";
        }
    }

    wp_die();
}

function custom_redirects()
{

    // $url = 'https://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
    // $current_post_id = url_to_postid( $url );
    global $post;
    $restrictions = get_metadata('post',  $post->ID, 'restrictions', true);
    $urlhome = get_site_url();
    $login = is_user_logged_in();

    if ($restrictions  == "Restricted" && $login != "1") {
        echo '<script>window.location.href = "' . $urlhome . '";</script>';
    }
}
add_action('template_redirect', 'custom_redirects');
