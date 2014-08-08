<?php
/**
 * Plugin Name: Fx Google News and Standout
 * Plugin URI: http://webmasterninja.wordpress.com/
 * Description: Add tags for Google News and Google Standout. Automatically generate google news meta tag using post tags (working out of the box). To enable Google Standout rel tag, you need to enable it on post editor metabox by selecting (1) Use current post or (2) Use other post by adding its url at the url reference box. More information about Google News Meta tag <a href="https://support.google.com/news/publisher/answer/68297?hl=en">HERE</a> and for Google Standout rel tag <a href="https://support.google.com/news/publisher/answer/191283?hl=en">HERE</a>.
 * Version: 1.1
 * Author: Jayson Antipuesto
 * Author URI: http://webmasterninja.wordpress.com/
 * License: GPL2
 */

/* Actions */

add_action( 'add_meta_boxes', 'fxpips_gstandout_add_custom_box' );
add_action( 'save_post', 'fxpips_gstandout_save_postdata' );
add_action( 'wp_head', 'fxpips_gstandout_meta' );



/* Adds a box to the main column on the Post and Page edit screens */
function fxpips_gstandout_add_custom_box() {
    add_meta_box(
        'fxpips_gstandout_sectionid',
        __( 'Google News Publisher META', 'fxpips_gstandout_textdomain' ),
        'fxpips_gstandout_inner_custom_box',
        'post'
        );
    add_meta_box(
        'fxpips_gstandout_sectionid',
        __( 'Google News Publisher META', 'fxpips_gstandout_textdomain' ),
        'fxpips_gstandout_inner_custom_box',
        'page'
        );
}

/* Prints the box content */
function fxpips_gstandout_inner_custom_box( $post ) {
    global $post;

    $meta_url = get_post_meta($post->ID, 'fxpips_gstandout_url', true);
    $meta_type = get_post_meta($post->ID, 'fxpips_gstandout_type', true);

    echo '<label for="fxpips_gstandout_type">';
    _e("Referenced Type", 'fxpips_gstandout_textdomain' );
    echo '</label>';
    echo '<select name="fxpips_gstandout_type">';
    if ($meta_type=='none')
     echo '<option value="none" selected="selected">None</option>';
    else
     echo '<option value="none">None</option>';
    if ($meta_type=='standout')
     echo '<option value="standout" selected="selected">Current post as standout</option>';
    else
     echo '<option value="standout">Current post as standout</option>';
    if ($meta_type=='otherstandout')
     echo '<option value="otherstandout" selected="selected">Other article as standout</option>';
    else
     echo '<option value="otherstandout">Other article as standout</option>';
    echo '</select>';
    echo '<br />';
    echo '<label for="fxpips_gstandout_url">';
    _e("Referenced URL if not current post (include http://)", 'fxpips_gstandout_textdomain' );
    echo '</label> ';
    echo '<input type="text" id="fxpips_gstandout_url" name="fxpips_gstandout_url" value="'.$meta_url.'" size="60" />';
}

function fxpips_gstandout_save_postdata( $post_id ) {
//  if (isset($_POST['fxpips_gstandout_url']) and isset($_POST['fxpips_gstandout_type'])) {
    if (isset($_POST['fxpips_gstandout_type']) and $_POST['fxpips_gstandout_type'] != 'None') {
        $gurl = $_POST['fxpips_gstandout_url'];
        $gtype = $_POST['fxpips_gstandout_type'];

        update_post_meta($post_id, 'fxpips_gstandout_url', $gurl);
        update_post_meta($post_id, 'fxpips_gstandout_type', $gtype);
    } else {
        delete_post_meta($post_id, 'fxpips_gstandout_type');
        delete_post_meta($post_id, 'fxpips_gstandout_url');
    }
}

function fxpips_gstandout_meta() {
    global $post;
    $url = get_post_meta($post->ID, 'fxpips_gstandout_url', true);
    $type = get_post_meta($post->ID, 'fxpips_gstandout_type', true);

    if ( $type == 'standout' ) {
        echo '<link rel="standout" href="' . get_permalink($post->ID) . '" />' . "\n";
    } elseif ( $type == 'otherstandout' ) {
        echo '<link rel="standout" href="' .$url. '" />' . "\n";
    } elseif ( $url ) {
        echo '<link rel="'.$type.'" href="'.$url.'"/>'."\n";
        remove_action('wp_head', 'rel_canonical');
    }
}
?>