<?php
/**
* Plugin Name: Sitemap by click5
* Plugin URI: https://www.click5interactive.com/wordpress-sitemap-plugin/
* Description: Generate and customize HTML & XML sitemaps for your website.
* Version: 1.0.28
* Author: click5 Interactive
* Author URI: https://www.click5interactive.com/?utm_source=sitemap-plugin&utm_medium=plugin-list&utm_campaign=wp-plugins
* Text Domain: sitemap-by-click5
* Domain Path: /languages
**/

define('CLICK5_SITEMAP_VERSION', '1.0.28');
define('CLICK5_SITEMAP_DEV_MODE', false);

// create custom plugin settings menu
require('api.php');

add_filter( 'wp_sitemaps_enabled', '__return_false' );

function c5_auto_update ( $update, $item ) {
	$plugins = array ( 'sitemap-by-click5' );
	if ( in_array( $item->slug, $plugins ) ) {
		// update plugin
		return true; 
	} else {
		// use default settings
		return $update; 
	}
}
add_filter( 'auto_update_plugin', 'c5_auto_update', 10, 2 );

add_action('admin_menu', 'click5_sitemap_create_menu');

function click5_sitemap_create_menu() {

	//create new top-level menu
	add_menu_page('Sitemap Settings', 'Sitemap', 'administrator', __FILE__, 'click5_sitemap_settings_page' , 'dashicons-editor-ul' );

	//call register settings function
	add_action( 'admin_init', 'click5_sitemap_settings' );
}

function click5_sitemap_activation_redirect( $plugin ) {
  if( $plugin == plugin_basename( __FILE__ ) ) {
      exit( wp_redirect( admin_url( 'admin.php?page=sitemap-by-click5%2Fsitemap-by-click5.php' ) ) );
  }
}
add_action( 'activated_plugin', 'click5_sitemap_activation_redirect' );

//left sidebar link
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'click5_sitemap_add_plugin_page_settings_link');
function click5_sitemap_add_plugin_page_settings_link( $links ) {
	$links[] = '<a href="' .
		admin_url( 'options-general.php?page=sitemap-by-click5%2Fsitemap-by-click5.php' ) .
		'">' . __('Settings') . '</a>';
	//$links[] = '<a target="_blank" rel="nofollow" href="https://www.click5interactive.com/wordpress-sitemap-plugin">' . __('About plugin') . '</a>';
	return $links;
}

add_filter( 'plugin_row_meta', 'click5_sitemap_plugin_meta', 10, 2 );
function click5_sitemap_plugin_meta( $links, $file ) { // add some links to plugin meta row
	if ( strpos( $file, 'sitemap-by-click5.php' ) !== false ) {
    //$links = array_merge( $links,  );

    array_splice( $links, 2, 0, array( '<a href="https://www.click5interactive.com/wordpress-sitemap-plugin/?utm_source=sitemap-plugin&utm_medium=plugin-list&utm_campaign=wp-plugins" target="_blank" rel="nofollow">About plugin</a>' ) );
	}
	return $links;
}

// Activation
function click5_sitemap_activation(){
    do_action( 'click5_sitemap_default_options' );
}
register_activation_hook( __FILE__, 'click5_sitemap_activation' );



function click5_sitemap_upgrade_completed( $upgrader_object, $options ) {
  $our_plugin = plugin_basename( __FILE__ );
  if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
   foreach( $options['plugins'] as $plugin ) {
    if( $plugin == $our_plugin ) {

      if (file_exists(ABSPATH.'/robots.txt') && !file_exists(ABSPATH.'/robots-click5-backup.txt')) {
        rename(ABSPATH.'/robots.txt', ABSPATH.'/robots-click5-backup.txt');
      }
      
      if (file_exists(ABSPATH.'/robots-click5-backup.txt')) {
          update_option('click5_sitemap_seo_robots_backup', true);
      }

    }
   }
  }
}
add_action( 'upgrader_process_complete', 'click5_sitemap_upgrade_completed', 10, 2 );


function click5_sitemap_save_default_order_list() {
  $styleSett = esc_attr( get_option('click5_sitemap_display_style') );
  $_style = !empty($styleSett) ? $styleSett : 'group';

  $newOrder = (array)(json_decode(html_entity_decode(json_encode(click5_sitemap_get_order_list($_style), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE))));

  $orderItems = click5_sitemap_order_list_setup_order_values($newOrder);

  update_option('click5_sitemap_order_list', json_encode($orderItems));
}

function click5_sitemap_forceDefaultSettings() {
  $post_types = click5_sitemap_get_post_types();
  foreach($post_types as $single_type) {
    $single_type = get_post_type_object($single_type);
    $option_name = 'click5_sitemap_display_'.$single_type->name;
    update_option($option_name, true);
    update_option('click5_sitemap_seo_post_type_'.$single_type->name, true);
  }

  update_option('click5_sitemap_seo_xml_categories', true);
  update_option('click5_sitemap_seo_xml_tags', true);

  $users = get_users();
  if( count($users) > 1 ){
    update_option('click5_sitemap_seo_xml_authors', true);
  }

  $cpt_args = array('public'   => true, '_builtin' => false);
  $cpt_output = 'names';
  $cpt_operator = 'and';
  $cpt_types = get_taxonomies( $cpt_args, $cpt_output, $cpt_operator ); 
  foreach ( $cpt_types  as $cpt_type ) { 
    update_option('click5_sitemap_seo_xml_'.$cpt_type.'_tax', true);
  }

  $getCustomCat = click5_sitemap_getCustomCategoriesCustomUrlsHTML();                       
  foreach($getCustomCat as $item) {
    update_option('click5_sitemap_seo_xml_'.str_replace(' ', '_', $item).'_custom', true);
  }

  if (file_exists(ABSPATH.'/robots.txt') && !file_exists(ABSPATH.'/robots-click5-backup.txt')) {
    rename(ABSPATH.'/robots.txt', ABSPATH.'/robots-click5-backup.txt');
  }

  if (file_exists(ABSPATH.'/robots-click5-backup.txt')) {
    update_option('click5_sitemap_seo_robots_backup', true);
  }

  update_option('click5_sitemap_seo_sitemap_xml', true);
  update_option('click5_sitemap_seo_robots_txt', true);
  update_option('click5_sitemap_seo_include_sitemap_xml', true);
  update_option('click5_sitemap_seo_sitemap_type', 'splitted');
  update_option('click5_sitemap_seo_auto', true);
  update_option('click5_sitemap_html_pagination_items_per_page', 50);
  update_option('click5_sitemap_display_columns', 1);

  click5_sitemap_generate_sitemap_XML_DoWork();
  click5_sitemap_generate_robots_txt();

  //save default order list
  click5_sitemap_save_default_order_list();
}

add_action( 'click5_sitemap_default_options', 'click5_sitemap_forceDefaultSettings' );


function click5_sitemap_settings() {
  //register our settings

	register_setting( 'click5_sitemap', 'click5_sitemap_post_template_HTML' );
  register_setting( 'click5_sitemap', 'click5_sitemap_is_multiple_time' );

  $post_types = click5_sitemap_get_post_types();
  foreach($post_types as $single_type) {
    $single_type = get_post_type_object($single_type);
    $option_name = 'click5_sitemap_display_'.$single_type->name;
    $option_names = array('click5_sitemap_use_custom_name_'.$single_type->name, 'click5_sitemap_custom_name_text_'.$single_type->name);

    register_setting('click5_sitemap', $option_name);
    register_setting('click5_sitemap', $option_names[0]);
    register_setting('click5_sitemap', $option_names[1]);
    //seo
    register_setting('click5_sitemap_seo', 'click5_sitemap_seo_post_type_'.$single_type->name);
  }

  register_setting('click5_sitemap_seo', 'click5_sitemap_seo_xml_categories');
  register_setting('click5_sitemap_seo', 'click5_sitemap_seo_xml_tags');
  register_setting('click5_sitemap_seo', 'click5_sitemap_seo_xml_authors');

  $cpt_args = array('public'   => true, '_builtin' => false);
  $cpt_output = 'names';
  $cpt_operator = 'and';
  $cpt_types = get_taxonomies( $cpt_args, $cpt_output, $cpt_operator ); 
  foreach ( $cpt_types  as $cpt_type ) { 
    register_setting('click5_sitemap_seo', 'click5_sitemap_seo_xml_'.$cpt_type.'_tax');
  }

  $getCustomCat = click5_sitemap_getCustomCategoriesCustomUrlsHTML();                       
  foreach($getCustomCat as $item) {
    register_setting('click5_sitemap_seo', 'click5_sitemap_seo_xml_'.str_replace(' ', '_', $item).'_custom');
  }
  
  register_setting('click5_sitemap', 'click5_sitemap_display_style');

  register_setting('click5_sitemap', 'click5_sitemap_url_target_blanc');

  register_setting('click5_sitemap_blacklist', 'click5_sitemap_blacklisted_array');
  register_setting('click5_sitemap_blacklist_seo', 'click5_sitemap_seo_blacklisted_array');


  //seo

  register_setting('click5_sitemap_seo', 'click5_sitemap_seo_sitemap_xml');

  register_setting('click5_sitemap_seo', 'click5_sitemap_seo_robots_txt');

  register_setting('click5_sitemap_seo', 'click5_sitemap_seo_include_sitemap_xml');

  register_setting('click5_sitemap_seo', 'click5_sitemap_seo_sitemap_type');
  register_setting('click5_sitemap_seo', 'click5_sitemap_seo_auto');


  //order list
  register_setting('click5_sitemap_html', 'click5_sitemap_order_list');
  register_setting('click5_sitemap_html', 'click5_sitemap_order_list_nested');
  register_setting('click5_sitemap_html', 'click5_sitemap_html_pagination_items_per_page');



  register_setting('click5_sitemap_urls', 'click5_sitemap_urls_list');

  //for authentication purposes
  $current_user = wp_get_current_user();
  register_setting('click5_sitemap_authentication', 'click5_sitemap_authentication_token_'.wp_get_current_user()->user_login);
}

function my_plugin_load_plugin_textdomain() {
  load_plugin_textdomain( 'sitemap-by-click5', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'my_plugin_load_plugin_textdomain' );


function click5_sitemap_settings_page() {
?>
<h1 class="click5_sitemap_heading"><?php _e('click5 Sitemap Settings', 'sitemap-by-click5'); ?>&nbsp;<span class="version">v<?php echo CLICK5_SITEMAP_VERSION; ?></span></h1>
<?php if( isset($_GET["settings-updated"]) ) { ?>
<div id="message" class="updated">
<p><strong><?php _e("Settings saved."); ?></strong></p>
</div>
<?php } ?>
<?php
            if( isset( $_GET[ 'tab' ] ) ) {
                $active_tab = sanitize_key($_GET[ 'tab' ]);
            } else {
              $active_tab = 'html';
            }
?>

<h2 class="nav-tab-wrapper">
    <a href="?page=sitemap-by-click5/sitemap-by-click5.php&tab=html" class="nav-tab <?php echo $active_tab == 'html' ? 'nav-tab-active' : ''; ?>"><?php _e('HTML Sitemap', 'sitemap-by-click5'); ?></a>
    <a href="?page=sitemap-by-click5/sitemap-by-click5.php&tab=seo" class="nav-tab <?php echo $active_tab == 'seo' ? 'nav-tab-active' : ''; ?>"><?php _e('XML Sitemap', 'sitemap-by-click5'); ?></a>
    <a href="?page=sitemap-by-click5/sitemap-by-click5.php&tab=urls" class="nav-tab <?php echo $active_tab == 'urls' ? 'nav-tab-active' : ''; ?>"><?php _e('Custom URLs', 'sitemap-by-click5'); ?></a>
</h2>
<div class="wrap click5_sitemap_wrapper_content_settings">
<div class="content-left">
<?php
      $verification_token = md5(uniqid(rand(), true));
      $cur_user_id = wp_get_current_user()->user_login;
      update_option('click5_sitemap_authentication_token_'.$cur_user_id, $verification_token);
?>
<input type="hidden" id="verification_token" value="<?php echo esc_attr($verification_token); ?>" />
<input type="hidden" id="user_identificator" value="<?php echo esc_attr($cur_user_id); ?>" />
<form method="post" action="options.php">
    <?php if ($active_tab == 'html'): ?>
    <?php settings_fields( 'click5_sitemap' ); ?>
    <?php do_settings_sections( 'click5_sitemap' ); ?>
    <div id="poststuff">
      <div id="post-body-content">
        <div class="postbox">
          <h3 class="hndle"><span><?php _e('HTML Sitemap Shortcode', 'sitemap-by-click5'); ?></span></h3>
          <div class="inside">
            <p><?php _e('In order to display Sitemap use that shortcode.', 'sitemap-by-click5'); ?></p>
            <blockquote id="copy-me" style="margin-left: 0;"><strong style="font-family: monospace; font-weight: 300; font-size: 1.5em;">[click5_sitemap]</strong></blockquote>
          </div>
        </div>
        <div class="postbox" id="enableSitemap">
          <h3 class="hndle"><span><?php _e('General HTML Sitemap Settings', 'sitemap-by-click5'); ?></span></h3>
          <div class="inside">
            <p><?php _e('Select post types which you want to include in your HTML Sitemap:', 'sitemap-by-click5'); ?></p>
            <table class="form-table">
              <tbody>
              <?php
                $post_types = click5_sitemap_get_post_types();
                foreach($post_types as $single_type) {
                  $single_type = get_post_type_object($single_type);
                  //print_r($single_type);
              ?>
              <tr class="click5_sitemap_post_type_item">
                <td>
                    <?php $option_name = 'click5_sitemap_display_'.$single_type->name; ?>
                    <label for="<?php echo esc_attr($option_name); ?>">
                    <input type="checkbox" name="<?php echo esc_attr($option_name); ?>" id="<?php echo esc_attr($option_name); ?>" value="1" <?php echo (intval(esc_attr( get_option($option_name) )) == 1 ? 'checked' : ''); ?>>
                    <strong><?php echo esc_attr($single_type->label); ?></strong></label>
                </td>
                <td>
                  <?php
                    $option_names = array('click5_sitemap_use_custom_name_'.$single_type->name, 'click5_sitemap_custom_name_text_'.$single_type->name);
                  ?>
                  <label><?php _e('Section Heading:', 'sitemap-by-click5'); ?>&nbsp;</label>
                  <label><input type="checkbox" name="<?php echo esc_attr($option_names[0]); ?>" id="<?php echo esc_attr($option_names[0]); ?>" value="1" <?php echo (intval(esc_attr( get_option($option_names[0]) )) == 1 ? 'checked' : ''); ?>/></label>
                  <input type="text" placeholder="<?php echo esc_attr($single_type->label); ?>" name="<?php echo esc_attr($option_names[1]); ?>" id="<?php echo esc_attr($option_names[1]); ?>" value="<?php echo esc_attr( get_option($option_names[1]) ); ?>"/>
                </td>
              </tr>
              <?php } ?>
              </tbody>
            </table>

            <p style="padding:10px 0"><?php _e('Select taxonomies which you want to include in your HTML Sitemap:', 'sitemap-by-click5'); ?></p>
            <table class="form-table">
              <tbody>

              <tr class="click5_sitemap_post_type_item">
                <td>
                    <?php $cat_tax_option_name = 'click5_sitemap_display_cat_tax'; ?>
                    <label for="<?php echo esc_attr($cat_tax_option_name); ?>">
                    <input type="checkbox" name="<?php echo esc_attr($cat_tax_option_name); ?>" id="<?php echo esc_attr($cat_tax_option_name); ?>" value="1" <?php echo (intval(esc_attr( get_option($cat_tax_option_name) )) == 1 ? 'checked' : ''); ?>>
                    <strong>Categories</strong></label>
                </td>
                <td>
                  <?php
                    $cat_tax_option_names = array('click5_sitemap_use_custom_name_cat_tax', 'click5_sitemap_custom_name_text_cat_tax');
                  ?>
                  <label><?php _e('Section Heading:', 'sitemap-by-click5'); ?>&nbsp;</label>
                  <label style="display:none"><input type="checkbox" name="<?php echo esc_attr($cat_tax_option_names[0]); ?>" id="<?php echo esc_attr($cat_tax_option_names[0]); ?>" value="1" <?php echo (intval(esc_attr( get_option($cat_tax_option_names[0]) )) == 1 ? 'checked' : ''); ?>/></label>
                  <input type="text" placeholder="Category" name="<?php echo esc_attr($cat_tax_option_names[1]); ?>" id="<?php echo esc_attr($cat_tax_option_names[1]); ?>" value="<?php echo esc_attr( get_option($cat_tax_option_names[1]) ); ?>"/>
                </td>
              </tr>

              <tr class="click5_sitemap_post_type_item">
                <td>
                    <?php $tag_tax_option_name = 'click5_sitemap_display_tag_tax'; ?>
                    <label for="<?php echo esc_attr($tag_tax_option_name); ?>">
                    <input type="checkbox" name="<?php echo esc_attr($tag_tax_option_name); ?>" id="<?php echo esc_attr($tag_tax_option_name); ?>" value="1" <?php echo (intval(esc_attr( get_option($tag_tax_option_name) )) == 1 ? 'checked' : ''); ?>>
                    <strong>Tags</strong></label>
                </td>
                <td>
                  <?php
                    $tag_tax_option_names = array('click5_sitemap_use_custom_name_tag_tax', 'click5_sitemap_custom_name_text_tag_tax');
                  ?>
                  <label><?php _e('Section Heading:', 'sitemap-by-click5'); ?>&nbsp;</label>
                  <label style="display:none"><input type="checkbox" name="<?php echo esc_attr($tag_tax_option_names[0]); ?>" id="<?php echo esc_attr($tag_tax_option_names[0]); ?>" value="1" <?php echo (intval(esc_attr( get_option($tag_tax_option_names[0]) )) == 1 ? 'checked' : ''); ?>/></label>
                  <input type="text" placeholder="Tag" name="<?php echo esc_attr($tag_tax_option_names[1]); ?>" id="<?php echo esc_attr($tag_tax_option_names[1]); ?>" value="<?php echo esc_attr( get_option($tag_tax_option_names[1]) ); ?>"/>
                </td>
              </tr>

              <?php

                $tax_args=array(
                  'public'   => true,
                  '_builtin' => false
                );
                $output = 'objects';
                $operator = 'and'; 
                $taxonomies=get_taxonomies($tax_args,$output,$operator); 

                if  ($taxonomies) {
                  foreach ($taxonomies  as $taxonomy ) {


              ?>
              <tr class="click5_sitemap_post_type_item">
                <td>
                    <?php $tax_option_name = 'click5_sitemap_display_'.$taxonomy->name; ?>
                    <label for="<?php echo esc_attr($tax_option_name); ?>">
                    <input type="checkbox" name="<?php echo esc_attr($tax_option_name); ?>" id="<?php echo esc_attr($tax_option_name); ?>" value="1" <?php echo (intval(esc_attr( get_option($tax_option_name) )) == 1 ? 'checked' : ''); ?>>
                    <strong><?php echo ucwords(esc_attr($taxonomy->labels->name)); ?></strong></label>
                </td>
                <td>
                  <?php
                    $tax_option_names = array('click5_sitemap_use_custom_name_'.$taxonomy->name, 'click5_sitemap_custom_name_text_'.$taxonomy->name);
                  ?>
                  <label><?php _e('Section Heading:', 'sitemap-by-click5'); ?>&nbsp;</label>
                  <label style="display:none"><input type="checkbox" name="<?php echo esc_attr($tax_option_names[0]); ?>" id="<?php echo esc_attr($tax_option_names[0]); ?>" value="1" <?php echo (intval(esc_attr( get_option($tax_option_names[0]) )) == 1 ? 'checked' : ''); ?>/></label>
                  <input type="text" placeholder="<?php echo ucwords(esc_attr($taxonomy->labels->name)); ?>" name="<?php echo esc_attr($tax_option_names[1]); ?>" id="<?php echo esc_attr($tax_option_names[1]); ?>" value="<?php echo esc_attr( get_option($tax_option_names[1]) ); ?>"/>
                </td>
              </tr>
              <?php } } ?>
              </tbody>
            </table>

            <p><strong><?php _e('Select grouping type:', 'sitemap-by-click5'); ?></strong></p>
            <table class="form-table">
              <tbody>
                <tr>
                  <select id="click5_sitemap_display_style" name="click5_sitemap_display_style" style="margin-left: 15px;">
                    <option value="group" <?php echo (esc_attr( get_option('click5_sitemap_display_style') ) == 'group' ? 'selected' : ''); ?>><?php _e('Split and group by post types', 'sitemap-by-click5'); ?></option>
                    <option value="merge" <?php echo (esc_attr( get_option('click5_sitemap_display_style') ) == 'merge' ? 'selected' : ''); ?>><?php _e('Merge into one list', 'sitemap-by-click5'); ?></option>
                  </select>
                </tr>
              </tbody>
            </table>

            <p><strong><?php _e('Display in Columns:', 'sitemap-by-click5'); ?></strong></p>
            <table class="form-table">
              <tbody>
                <tr>
                  <select id="click5_sitemap_display_columns" name="click5_sitemap_display_columns" style="margin-left: 15px;width: 224px;">
                    <option value="1" <?php echo (esc_attr( get_option('click5_sitemap_display_columns') ) == '1' ? 'selected' : ''); ?>>1 Column</option>
                    <option value="2" <?php echo (esc_attr( get_option('click5_sitemap_display_columns') ) == '2' ? 'selected' : ''); ?>>2 Columns</option>
                    <option value="3" <?php echo (esc_attr( get_option('click5_sitemap_display_columns') ) == '3' ? 'selected' : ''); ?>>3 Columns</option>
                    <option value="4" <?php echo (esc_attr( get_option('click5_sitemap_display_columns') ) == '4' ? 'selected' : ''); ?>>4 Columns</option>
                  </select>
                </tr>
              </tbody>
            </table>

            <p><strong><?php _e('Pagination:', 'sitemap-by-click5'); ?></strong></p>
            <table class="form-table">
              <tbody>
                <tr>
                  <label style="padding-left: 15px;"><?php _e('Items per page:', 'sitemap-by-click5'); ?>&nbsp;</label>
                  <input type="number" min="0" name="click5_sitemap_html_pagination_items_per_page" id="click5_sitemap_html_pagination_items_per_page" value="<?php echo intval(esc_attr( get_option('click5_sitemap_html_pagination_items_per_page') )); ?>"/>
                  <span style="width: auto; padding-left: 35px; color: #999; font-style: italic;">* <?php _e('Leave that field <strong>empty</strong> (or set it to <strong>0</strong>) to <strong>disable</strong> pagination.', 'sitemap-by-click5'); ?></span>
                </tr>
              </tbody>
            </table>
            <p><strong><?php _e('URLs options:', 'sitemap-by-click5'); ?></strong></p>
            <table class="form-table">
              <tbody>
                <tr>
                  <label for="click5_sitemap_url_target_blanc" style="margin-left: 15px;">
                    <input type="checkbox" name="click5_sitemap_url_target_blanc" id="click5_sitemap_url_target_blanc" value="1" <?php echo (intval(esc_attr( get_option('click5_sitemap_url_target_blanc') )) == 1 ? 'checked' : ''); ?>><?php _e('Open links in new tab', 'sitemap-by-click5'); ?></label>
                </tr>
              </tbody>
            </table>
            <p><strong><?php _e('Grouping Options:', 'sitemap-by-click5'); ?></strong></p>
            <table class="form-table">
              <tbody>
                <tr>
                  <p><?php _e('Enable Grouping for Post Type:', 'sitemap-by-click5'); ?></p>
                  <div class="disable_wrapper">
                  <?php
                    foreach($post_types as $single_type) {
                    $single_type = get_post_type_object($single_type);
                    $option_name_treat = 'click5_sitemap_html_blog_treat_'.$single_type->name;
                      ?>
                      <p style="padding-left: 15px;"><label><input type="checkbox" class="not-reset" name="<?php echo esc_attr($option_name_treat); ?>" id="<?php echo esc_attr($option_name_treat); ?>" value="1" <?php echo (intval(esc_attr( get_option($option_name_treat) )) == 1 ? 'checked' : ''); ?>/><?php echo esc_attr($single_type->label); ?></label></p>
                      <?php
                    }
                  ?>
                  </div>
                </tr>
                <tr>
                  <p><?php _e('Group by:', 'sitemap-by-click5'); ?></p>
                  <div class="disable_wrapper">
                  <select id="click5_sitemap_html_blog_group_by" class="not-reset" name="click5_sitemap_html_blog_group_by" style="margin-left: 15px;">
                    <option value="disabled" <?php echo (esc_attr( get_option('click5_sitemap_html_blog_group_by') ) == 'disabled' ? 'selected' : ''); ?>><?php _e('Disabled', 'sitemap-by-click5'); ?></option>
                    <option value="archives" <?php echo (esc_attr( get_option('click5_sitemap_html_blog_group_by') ) == 'archives' ? 'selected' : ''); ?>><?php _e('Archives', 'sitemap-by-click5'); ?></option>
                    <option value="categories" <?php echo (esc_attr( get_option('click5_sitemap_html_blog_group_by') ) == 'categories' ? 'selected' : ''); ?>><?php _e('Categories', 'sitemap-by-click5'); ?></option>
                    <option value="tags" <?php echo (esc_attr( get_option('click5_sitemap_html_blog_group_by') ) == 'tags' ? 'selected' : ''); ?>><?php _e('Tags', 'sitemap-by-click5'); ?></option>
                  </select>
                  </div>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="postbox" id="order-sitemap">
          <h3 class="hndle"><span><?php _e('HTML Sitemap Order', 'sitemap-by-click5'); ?></span></h3>
          <p style="padding-left: 12px; padding-right: 12px;"><?php _e('Grab & drop elements listed below to setup HTML Sitemap display order.', 'sitemap-by-click5'); ?></p>
          <div class="inside">
            <button id="btnSaveOrder" class="button button-secondary"><?php _e('Save Order', 'sitemap-by-click5'); ?></button>
            <button id="btnResetOrder" class="button button-primary" style="margin-left: 10px;"><?php _e('Reset Order', 'sitemap-by-click5'); ?></button>
          </div>
        </div>
        <div class="postbox">
          <h3 class="hndle"><span><?php _e('Blacklist (HTML Sitemap)', 'sitemap-by-click5'); ?></span></h3>
          <div class="inside" id="click5_sitemap_blacklist_container" class="html">
            <div id="loader_blacklisted" class="loader-container"><div class="sbl-circ-ripple"></div></div>
            <ul id="click5_sitemap_already_blacklisted" class="results">
            </ul>
            <a href="#" id="btnClearBlacklist" class="click5_sitemap_clear_blacklist"><?php _e('Clear blacklist', 'sitemap-by-click5'); ?></a>
            <hr/>
            <table class="form-table">
              <tbody>
              <tr>
                <th scope="row">
                <?php _e('Search for posts/pages', 'sitemap-by-click5'); ?></th>
                <td>
                    <input type="text" id="page_search" placeholder="Search" />
                    <label>&nbsp;<?php _e('Type:', 'sitemap-by-click5'); ?>&nbsp;</label>
                    <select id="page_type">
                      <option value="all" selected><?php _e('All', 'sitemap-by-click5'); ?></option>
                      <?php
                        foreach($post_types as $single_type) {
                          $single_type = get_post_type_object($single_type);
                      ?>
                        <option value="<?php echo esc_attr($single_type->name); ?>"><?php echo esc_attr($single_type->label); ?></option>
                      <?php } ?>
                    </select>
                    <input type="hidden" id="all_types" value="<?php echo esc_attr(implode(',', $post_types)); ?>" />
                </td>
              </tr>
              </tbody>
            </table>
            <hr/>
            <div id="loader_results" class="loader-container"><div class="sbl-circ-ripple"></div></div>
            <ul id="results" class="results"></ul>
          </div>
        </div>
      </div>
    </div>
    <?php elseif( $active_tab == 'seo' ): ?>
    <?php settings_fields( 'click5_sitemap_seo' ); ?>
    <?php do_settings_sections( 'click5_sitemap_seo' ); ?>
    <div id="poststuff">
      <div id="post-body-content">
        <div class="postbox">
          <h3 class="hndle"><span><?php _e('XML Sitemap & Robots.txt', 'sitemap-by-click5'); ?></span></h3>
          <div class="inside">
              <table class="form-table">
                <tbody>
                    <tr>
                      <td class="click5_sitemap_options_wrapper">
                        <p style="width: 100%; margin-bottom: 10px;"><strong><?php _e('Your sitemap.xml:', 'sitemap-by-click5'); ?></strong></p>
                        <div id="loader_status_sitemap" class="loader-container"><div class="sbl-circ-ripple"></div></div>
                        <div id="click5_sitemap_url_container" style="width: 100%;">
                        <?php $sitemaps = glob(ABSPATH.'/*sitemap*.xml'); ?>
                        <?php if (count($sitemaps)): ?>
                        <?php
                          $resultArray = array();
                          foreach( $sitemaps as $sitemap ) {
                            if (strpos($sitemap, 'index.xml') !== false) {
                              array_unshift($resultArray, $sitemap);
                            } else {
                              $resultArray[] = $sitemap;
                            }
                          }
                          $sitemaps = $resultArray;
                        ?>
                        <?php foreach( $sitemaps as $sitemap ) { ?>
                        <a style="display: block; width: 100%;" target="_blank" class="click5_sitemap_urls" href="<?php echo esc_url(site_url().'/'.str_replace('-index', '', basename($sitemap))); ?>"><?php echo esc_url(site_url().'/'.str_replace('-index', '', basename($sitemap))); ?></a>
                        <?php } ?>
                        <?php else: ?>
                        <p class="sitemap_not_gen" style="width: 100%;"><?php _e('sitemap.xml not generated yet.', 'sitemap-by-click5'); ?></p>
                        <?php endif; ?>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td class="click5_sitemap_options_wrapper">
                        <p style="width: 100%; margin-bottom: 10px;"><strong><?php _e('Your robots.txt:', 'sitemap-by-click5'); ?></strong></p>
                        <div id="loader_status_robots" class="loader-container"><div class="sbl-circ-ripple"></div></div>
                        <div id="click5_sitemap_robots_txt_container" style="width: 100%; display: flex; flex-direction: column;">
                        <?php click5_sitemap_print_robots_txt(); ?>
                        </div>
                      </td>
                  </tr>
                </tbody>
              </table>
          </div>
        </div>
        <div class="postbox" id="general_seo">
          <h3 class="hndle"><span><?php _e('General XML Sitemap Settings', 'sitemap-by-click5'); ?></span></h3>
          <div class="inside" id="ajaxable">
              <table class="form-table">
                <tbody>
                    <tr>
                      <td class="click5_sitemap_options_wrapper">
                        <div>
                          <p style="width: 100%; margin-bottom: 10px;"><strong><?php _e('Generate SEO files:', 'sitemap-by-click5'); ?></strong></p>
                            <div style="display: flex; flex-direction: column;">
                                <label><input type="checkbox" value="1" name="click5_sitemap_seo_sitemap_xml" id="click5_sitemap_seo_sitemap_xml" <?php echo (intval(esc_attr( get_option('click5_sitemap_seo_sitemap_xml') )) == 1 ? 'checked' : ''); ?>/><?php _e('Generate sitemap.xml', 'sitemap-by-click5'); ?></label>
                                <label><input type="checkbox" value="1" name="click5_sitemap_seo_robots_txt" id="click5_sitemap_seo_robots_txt" <?php echo (intval(esc_attr( get_option('click5_sitemap_seo_robots_txt') )) == 1 ? 'checked' : ''); ?> /><?php _e('Generate robots.txt', 'sitemap-by-click5'); ?></label>
                                <label><input type="checkbox" value="1" name="click5_sitemap_seo_include_sitemap_xml" id="click5_sitemap_seo_include_sitemap_xml" <?php echo (intval(esc_attr( get_option('click5_sitemap_seo_include_sitemap_xml') )) == 1 ? 'checked' : ''); ?> /><?php _e('Include sitemap.xml url in robots.txt', 'sitemap-by-click5'); ?></label>
                            </div>
                        </div>
                      </td>
                      <td class="click5_sitemap_options_wrapper">
                        <div>
                          <p style="width: 100%; margin-bottom: 10px;"><strong><?php _e('Include post types:', 'sitemap-by-click5'); ?></strong></p>
                          <div style="display: flex; flex-direction: column;">
                          <?php
                            $post_types = click5_sitemap_get_post_types();
                            foreach($post_types as $single_type) {
                            $single_type = get_post_type_object($single_type);
                            $option_name = 'click5_sitemap_seo_post_type_'.$single_type->name;
                          ?>
                                <label for="<?php echo esc_attr($option_name); ?>"><input type="checkbox" name="<?php echo esc_attr($option_name); ?>" id="<?php echo esc_attr($option_name); ?>" value="1" <?php echo (intval(esc_attr( get_option($option_name) )) == 1 ? 'checked' : ''); ?>><?php echo esc_attr($single_type->label); ?></label>
                          <?php } ?>
                          </div>
                        </div>
                      </td>

                      <td class="click5_sitemap_options_wrapper">
                        <div>
                          <p style="width: 100%; margin-bottom: 10px;"><strong><?php _e('Include taxonomy sitemaps:', 'sitemap-by-click5'); ?></strong></p>
                          <div style="display: flex; flex-direction: column;">
                                <label for="<?php echo esc_attr('click5_sitemap_seo_xml_categories'); ?>"><input type="checkbox" name="<?php echo esc_attr('click5_sitemap_seo_xml_categories'); ?>" id="<?php echo esc_attr('click5_sitemap_seo_xml_categories'); ?>" value="1" <?php echo (intval(esc_attr( get_option('click5_sitemap_seo_xml_categories') )) == 1 ? 'checked' : ''); ?>><?php _e('Categories', 'sitemap-by-click5'); ?></label>
                                <label for="<?php echo esc_attr('click5_sitemap_seo_xml_tags'); ?>"><input type="checkbox" name="<?php echo esc_attr('click5_sitemap_seo_xml_tags'); ?>" id="<?php echo esc_attr('click5_sitemap_seo_xml_tags'); ?>" value="1" <?php echo (intval(esc_attr( get_option('click5_sitemap_seo_xml_tags') )) == 1 ? 'checked' : ''); ?>><?php _e('Tags', 'sitemap-by-click5'); ?></label>


                                <?php

                                $cpt_args = array(
                                  'public'   => true,
                                  '_builtin' => false,
                                );
                                $cpt_output = 'objects';
                                $cpt_operator = 'and';
                                $cpt_types = get_taxonomies( $cpt_args, $cpt_output, $cpt_operator ); 

                                foreach ( $cpt_types  as $cpt_type ) {  

                                $option_name = 'click5_sitemap_seo_xml_'.$cpt_type->name.'_tax';

                                ?>
                                      <label for="<?php echo esc_attr($option_name); ?>"><input type="checkbox" name="<?php echo esc_attr($option_name); ?>" id="<?php echo esc_attr($option_name); ?>" value="1" <?php echo (intval(esc_attr( get_option($option_name) )) == 1 ? 'checked' : ''); ?>><?php echo ucwords(esc_attr($cpt_type->labels->name)); ?></label>

                                <?php } ?>

                                <label for="<?php echo esc_attr('click5_sitemap_seo_xml_authors'); ?>"><input type="checkbox" name="<?php echo esc_attr('click5_sitemap_seo_xml_authors'); ?>" id="<?php echo esc_attr('click5_sitemap_seo_xml_authors'); ?>" value="1" <?php echo (intval(esc_attr( get_option('click5_sitemap_seo_xml_authors') )) == 1 ? 'checked' : ''); ?>><?php _e('Authors', 'sitemap-by-click5'); ?></label>

                                <?php 
                                  $getCustomCat = click5_sitemap_getCustomCategoriesCustomUrlsHTML();
                              
                                  foreach($getCustomCat as $item) {

                                    $cust_option_name = 'click5_sitemap_seo_xml_'.str_replace(' ', '_', $item).'_custom';

                                    ?>

                                    <label for="<?php echo esc_attr($cust_option_name); ?>"><input type="checkbox" name="<?php echo esc_attr($cust_option_name); ?>" id="<?php echo esc_attr($cust_option_name); ?>" value="1" <?php echo (intval(esc_attr( get_option($cust_option_name) )) == 1 ? 'checked' : ''); ?>><?php echo ucwords(esc_attr($item)); ?></label>

                                    <?php
                                  }
                                ?>
                          </div>
                        </div>
                      </td>


                    </tr>
                    <tr>
                      <td class="click5_sitemap_options_wrapper">
                        <p style="width: 100%; margin-bottom: 10px;"><strong><?php _e('XML Sitemap type:', 'sitemap-by-click5'); ?></strong></p>
                              <select name="click5_sitemap_seo_sitemap_type" id="click5_sitemap_seo_sitemap_type">
                                <option value="splitted" <?php echo (esc_attr( get_option('click5_sitemap_seo_sitemap_type') ) == 'splitted' ? 'selected' : ''); ?>><?php _e('Split and group by post types', 'sitemap-by-click5'); ?></option>
                                <option value="one_file" <?php echo (esc_attr( get_option('click5_sitemap_seo_sitemap_type') ) == 'one_file' ? 'selected' : ''); ?>><?php _e('Merge into one list', 'sitemap-by-click5'); ?></option>
                              </select>
                              <p style="width: 100%;">* <?php _e('<strong>Splitted</strong> sitemap type is recommended for websites which have a lot of subpages.', 'sitemap-by-click5'); ?></p>
                      </td>
                    </tr>
                    <tr>
                      <td class="click5_sitemap_options_wrapper">
                      <p style="width: 100%; margin-bottom: 10px;"><strong><?php _e('Automation:', 'sitemap-by-click5'); ?></strong></p>
                          <label style="width: 100%;"><input type="checkbox" name="click5_sitemap_seo_auto" id="click5_sitemap_seo_auto" value="1" <?php echo (intval(esc_attr( get_option('click5_sitemap_seo_auto') )) == 1 ? 'checked' : ''); ?>></input> <?php _e('Re-generate Sitemap files on each website structure change (ex. new page have been added or its content modified)', 'sitemap-by-click5'); ?></label>
                      </td>
                    </tr>
                    <tr>
                      <td>
                          <button id="generate_btn" class="button button-secondary"><?php _e('Re-generate XML Sitemap', 'sitemap-by-click5'); ?></button><div id="click5-ajax-loader" class="sbl-circ"></div>
                      </td>
                    </tr>
                </tbody>
              </table>
          </div>
        </div>
        <div class="postbox">
          <h3 class="hndle"><span><?php _e('Blacklist (XML Sitemap)', 'sitemap-by-click5'); ?></span></h3>
          <div class="inside" id="click5_sitemap_blacklist_container" class="seo">
            <div id="loader_blacklisted" class="loader-container"><div class="sbl-circ-ripple"></div></div>
            <ul id="click5_sitemap_already_blacklisted">
            </ul>
            <a href="#" id="btnClearBlacklist" class="click5_sitemap_clear_blacklist"><?php _e('Clear blacklist', 'sitemap-by-click5'); ?></a>
            <hr/>
            <table class="form-table">
              <tbody>
              <tr>
                <th scope="row">
                <?php _e('Search for posts/pages', 'sitemap-by-click5'); ?></th>
                <td>
                    <input type="text" id="page_search" placeholder="Search" />
                    <label>&nbsp;<?php _e('Type:', 'sitemap-by-click5'); ?>&nbsp;</label>
                    <select id="page_type">
                      <option value="all" selected><?php _e('All', 'sitemap-by-click5'); ?></option>
                      <?php
                        foreach($post_types as $single_type) {
                          $single_type = get_post_type_object($single_type);
                      ?>
                        <option value="<?php echo esc_attr($single_type->name); ?>"><?php echo esc_attr($single_type->label); ?></option>
                      <?php } ?>
                    </select>
                    <input type="hidden" id="all_types" value="<?php echo esc_attr(implode(',', $post_types)); ?>" />
                </td>
              </tr>
              </tbody>
            </table>
            <hr/>
            <div id="loader_results" class="loader-container"><div class="sbl-circ-ripple"></div></div>
            <ul id="results" class="results"></ul>
          </div>
        </div>
      </div>
    </div>
    <?php elseif( $active_tab == 'urls' ): ?>
    <?php settings_fields( 'click5_sitemap_urls' ); ?>
    <?php do_settings_sections( 'click5_sitemap_urls' ); ?>
    <div id="poststuff">
      <div id="post-body-content">
        <div class="postbox">
          <h3 class="hndle"><span><?php _e('Existing Custom URLs', 'sitemap-by-click5'); ?></span></h3>
          <div class="inside">
            <table class="form-table">
              <tbody>
              <tr valign="top">
                <td>
                  <div id="loader_custom_urls_list" class="loader-container"><div class="sbl-circ-ripple"></div></div>
                  <p id="click5_no_records_found" style="display: none;"><?php _e('No custom URLs saved yet.', 'sitemap-by-click5'); ?></p>
                  <ul id="custom_urls_list" class="results"></ul>
                  <a href="#" id="click5_clear_custom_list"><?php _e('Clear list', 'sitemap-by-click5'); ?></a>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="postbox" id="edit_custom_url">
          <h3 class="hndle"><span><?php _e('Edit URL', 'sitemap-by-click5'); ?></span></h3>
          <div class="inside">
            <table class="form-table">
              <tbody>
              <tr valign="top">
                <th scope="row"><?php _e('URL Title', 'sitemap-by-click5'); ?></th>
                <td>
                  <input type="text" name="edit_url_title" id="edit_url_title" placeholder="" style="width: 100%;" />
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><?php _e('URL', 'sitemap-by-click5'); ?></th>
                <td>
                  <input type="text" name="edit_url_url" id="edit_url_url" placeholder="Your custom URL" style="width: 100%;" />
                  <p class="description"><?php _e('For ex.', 'sitemap-by-click5'); ?> <strong>/blog/category/1</strong> or <strong>https://www.google.com/</strong></p>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="click5_sitemap_heading_text"><?php _e('URL Category', 'sitemap-by-click5'); ?></label>
                </th>
                <td>
                  <?php
                    $post_types = click5_sitemap_get_post_types();
                    $getCustomCategories = click5_sitemap_getCustomCategoriesCustomUrlsHTML(); 
                  ?>
                  <select id="edit_url_category_select">
                          <option value="custom" data-custom="true"><?php _e('Custom', 'sitemap-by-click5'); ?></option>
                          <?php
                            foreach($post_types as $post_type) {
                              $post_type = get_post_type_object($post_type);
                              echo '<option value="'.esc_attr($post_type->name).'">'.esc_attr($post_type->label).'</option>';
                            }
                            foreach($getCustomCategories as $item) {
                              echo '<option value="'.esc_attr($item).'" data-custom="true">'.esc_attr($item).'</option>';
                            }
                          ?>
                  </select>
                  <input type="hidden" id="edit_url_category_use_custom" name="edit_url_category_use_custom" value="true" />
                  <input type="text" name="edit_url_category_text" id="edit_url_category_text" placeholder="ex. `External URLs`" style="display: none;" />
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="click5_sitemap_heading_text"><?php _e('Open link in new tab', 'sitemap-by-click5'); ?></label>
                </th>
                <td>
                  <label><input type="checkbox" name="edit_url_open_new_tab" id="edit_url_open_new_tab" value="1"><?php _e('Yes', 'sitemap-by-click5'); ?></label>
                  <p class="description"><?php _e('Used for HTML Sitemap.', 'sitemap-by-click5'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="click5_sitemap_heading_text"><?php _e('Last modification date', 'sitemap-by-click5'); ?></label>
                </th>
                <td>
                  <input type="text" name="edit_url_last_mod_date" id="edit_url_last_mod_date" />
                  <p class="description"><?php _e('Used for XML Sitemap (you can leave that empty).', 'sitemap-by-click5'); ?></p>
                </td>
              </tr>
              <tr>
                <td>
                  <button id="saveURLbtn" class="button button-secondary"><?php _e('Save', 'sitemap-by-click5'); ?></button>
                  <button id="cancelURLbtn" class="button button-primary"><?php _e('Cancel', 'sitemap-by-click5'); ?></button>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="postbox">
          <h3 class="hndle"><span><?php _e('Add new URL', 'sitemap-by-click5'); ?></span></h3>
          <div class="inside">
            <table class="form-table">
              <tbody>
              <tr valign="top">
                <th scope="row"><?php _e('URL Title', 'sitemap-by-click5'); ?>&nbsp;<span style="color:#d80707">*</span></th>
                <td>
                  <input type="text" name="add_url_title" id="add_url_title" placeholder="" style="width: 100%;" />
                </td>
              </tr>
              <tr valign="top">
                <th scope="row"><?php _e('URL', 'sitemap-by-click5'); ?>&nbsp;<span style="color:#d80707">*</span></th>
                <td>
                  <input type="text" name="add_url_url" id="add_url_url" placeholder="Your custom URL" style="width: 100%;" />
                  <p class="description"><?php _e('For ex.', 'sitemap-by-click5'); ?> <strong>/blog/category/1</strong> or <strong>https://www.google.com/</strong></p>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="click5_sitemap_heading_text"><?php _e('URL Category', 'sitemap-by-click5'); ?>&nbsp;<span style="color:#d80707">*</span></label>
                </th>
                <td>
                  <?php
                    $post_types = click5_sitemap_get_post_types(); 
                    $getCustomCategories = click5_sitemap_getCustomCategoriesCustomUrlsHTML(); 
                  ?>
                  <select id="add_url_category_select">
                          <option value="custom" data-custom="true"><?php _e('Custom', 'sitemap-by-click5'); ?></option>
                          <?php
                            foreach($post_types as $post_type) {
                              $post_type = get_post_type_object($post_type);
                              echo '<option value="'.esc_attr($post_type->name).'">'.esc_attr($post_type->label).'</option>';
                            }
                            foreach($getCustomCategories as $item) {
                              echo '<option value="'.esc_attr($item).'" data-custom="true">'.esc_attr($item).'</option>';
                            }
                          ?>
                  </select>
                  <input type="hidden" id="add_url_category_use_custom" name="add_url_category_use_custom" value="true" />
                  <input type="text" name="add_url_category_text" id="add_url_category_text" placeholder="ex. `External URLs`" style="display: none;" />
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="click5_sitemap_heading_text"><?php _e('Open link in new tab', 'sitemap-by-click5'); ?></label>
                </th>
                <td>
                  <label><input type="checkbox" name="add_url_open_new_tab" id="add_url_open_new_tab" value="1"><?php _e('Yes', 'sitemap-by-click5'); ?></label>
                  <p class="description"><?php _e('Used for HTML Sitemap.', 'sitemap-by-click5'); ?></p>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="click5_sitemap_heading_text"><?php _e('Last modification date', 'sitemap-by-click5'); ?></label>
                </th>
                <td>
                  <input type="text" class="ll-skin-nigran" name="add_url_last_mod_date" id="add_url_last_mod_date" placeholder="<?php echo date('m/d/Y') ?>" />
                  <p class="description"><?php _e('Used for XML Sitemap (you can leave that empty).', 'sitemap-by-click5'); ?></p>
                </td>
              </tr>
              <tr>
                <td>
                  <button id="addNewURLbtn" class="button button-secondary"><?php _e('Add URL', 'sitemap-by-click5'); ?></button>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="postbox" id="order-sitemap" style="display:none">
          <div class="inside"></div>
        </div>

      </div>
    </div>
    <?php endif; ?>

    <?php 

      if($active_tab != 'urls'){
        submit_button();
      }
     
    ?>

</form>
</div>
<div class="content-right">
      <div id="poststuff">
        <div id="post-body-content">
            <div class="postbox">
              <h3 class="hndle"><span>Plugin Support</span></h3>
              <div class="inside">
                <p>Visit our <a href="http://wordpress.org/support/plugin/sitemap-by-click5" target="_blank" rel="nofollow">community forum</a> to find answers to common issues, ask questions, submit bug reports, feature suggestions and other tips about our plugin.</p>
                <p>Please consider supporting us by <a href="https://wordpress.org/support/plugin/sitemap-by-click5/reviews/?filter=5" target="_blank" rel="nofollow">rating this plugin</a>. Thanks!</p>
              </div>
            </div>
            <div class="postbox with-image">
              <a href="https://click5crm.com/?utm_source=sitemap-plugin&utm_medium=sidebar&utm_campaign=wp-plugins" target="_blank" rel="nofollow">
                <img src="<?php echo plugin_dir_url( __FILE__ ).'assets/banner-300x515-sitemap-plugin.png'; ?>" alt="click5crm">
              </a>
              <!-- <h3 class="hndle"><span>Custom WordPress Development</span></h3>
              <div class="inside">
                <p>Visit <a href="https://www.click5interactive.com/?utm_source=sitemap-plugin&utm_medium=sidebar&utm_campaign=wp-plugins" target="_blank" rel="nofollow">click5 Interactive</a> website to learn more about our Custom Website Design & WordPress Development services.</p>
              </div> -->
            </div>
        </div>
      </div>
</div>
</div>
<?php }

function click5_sitemap_init_admin_scripts() {
  //libraries
  $screen = get_current_screen();
  $version = CLICK5_SITEMAP_DEV_MODE ? time() : CLICK5_SITEMAP_VERSION;

  if(strpos($screen->base, 'sitemap-by-click5') !== false) {

    wp_enqueue_script( 'click5_sitemap_js_sortable', plugins_url('/js/admin/jquery-sortable.js', __FILE__), array(), $version, true);
  
    wp_enqueue_script( 'click5_sitemap_js_datepicker', plugins_url('/js/admin/datepicker.min.js', __FILE__), array(), $version);
    wp_enqueue_script( 'click5_sitemap_js_admin', plugins_url('/js/admin/index.js', __FILE__), array(), $version);
    wp_enqueue_script( 'click5_sitemap_js_admin_html', plugins_url('/js/admin/html.js', __FILE__), array(), $version);
    wp_enqueue_script( 'click5_sitemap_js_admin_seo', plugins_url('/js/admin/seo.js', __FILE__), array(), $version);
    wp_enqueue_script( 'click5_sitemap_js_admin_urls', plugins_url('/js/admin/urls.js', __FILE__), array(), $version);
    wp_enqueue_style( 'click5_sitemap_css_datepicker', plugins_url('/css/admin/datepicker.min.css', __FILE__), array(), $version);
    wp_enqueue_style( 'click5_sitemap_css_admin', plugins_url('/css/admin/index.css', __FILE__), array(), $version);
    wp_enqueue_style( 'click5_sitemap_css_sortable', plugins_url('/css/admin/sortable.css', __FILE__), array(), $version);
    

  }
  
  wp_localize_script( 'jquery', 'c5resturl', array('wpjson' => get_rest_url()) );
  wp_localize_script( 'jquery', 'c5homeurl', array('home' => get_home_url()) );

}

add_action('admin_enqueue_scripts','click5_sitemap_init_admin_scripts');



remove_action( 'template_redirect', array('WPSEO_Sitemaps_Router', 'template_redirect'), 0 );
function click5_sitemap_sitemap_xml_redirection() {

  if(boolval(esc_attr( get_option('click5_sitemap_seo_sitemap_xml')))){
  
    $current_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

    $b_should_redirect = false;

    

    if(preg_match('/\/(.*)?sitemap((?!-.+).)*\.(htm|html|xml)(\.gz)?/', $current_url)) {
      $b_should_redirect = true;
    }
    if(preg_match('/(.+|)sitemap_index.xml/', $current_url)) {
      $b_should_redirect = true;
    }

    if ($b_should_redirect) {
      wp_safe_redirect( home_url( '/sitemap-index.xml' ), 301, 'click5 Sitemap XML' );
      exit;
    }

  }
}
add_action( 'template_redirect', 'click5_sitemap_sitemap_xml_redirection', 0);


delete_option('click5_sitemap_seo_xml_custom_taxonomies');
//unregister_setting('click5_sitemap_seo', 'click5_sitemap_seo_xml_custom_taxonomies');

// uninstall hook

function click5_sitemap_uninstallFunction() {
  delete_option('click5_sitemap_url_target_blanc');
  delete_option('click5_sitemap_blacklisted_array');
  delete_option('click5_sitemap_urls_list');
  delete_option('click5_sitemap_display_style');
  delete_option('click5_sitemap_display_columns');
  delete_option('click5_sitemap_seo_blacklisted_array');
  delete_option('click5_sitemap_seo_sitemap_type');
  delete_option('click5_sitemap_seo_robots_txt');
  delete_option('click5_sitemap_seo_include_sitemap_xml');
  delete_option('click5_sitemap_seo_auto');
  delete_option('click5_sitemap_order_list');
  delete_option('click5_sitemap_order_list_nested');

  delete_option('click5_sitemap_seo_xml_categories');
  delete_option('click5_sitemap_seo_xml_tags');
  delete_option('click5_sitemap_seo_xml_authors');

  delete_option('click5_sitemap_seo_robots_backup');

  $cpt_args = array('public'   => true, '_builtin' => false);
  $cpt_output = 'names';
  $cpt_operator = 'and';
  $cpt_types = get_taxonomies( $cpt_args, $cpt_output, $cpt_operator ); 
  foreach ( $cpt_types  as $cpt_type ) { 
    delete_option('click5_sitemap_seo_xml_'.$cpt_type.'_tax');
  }

  $getCustomCat = click5_sitemap_getCustomCategoriesCustomUrlsHTML();                       
  foreach($getCustomCat as $item) {
    delete_option('click5_sitemap_seo_xml_'.str_replace(' ', '_', $item).'_custom');
  }
  
  $post_types = click5_sitemap_get_post_types();
  foreach($post_types as $single_type) {
    $single_type = get_post_type_object($single_type);
    delete_option('click5_sitemap_display_'.$single_type->name);
    delete_option('click5_sitemap_use_custom_name_'.$single_type->name);
    delete_option('click5_sitemap_custom_name_text_'.$single_type->name);
    delete_option('click5_sitemap_seo_post_type_'.$single_type->name);
  }

  $current_user = wp_get_current_user();
  delete_option('click5_sitemap_authentication_token_'.wp_get_current_user()->user_login);

  //unregister

	unregister_setting( 'click5_sitemap', 'click5_sitemap_post_template_HTML' );
  unregister_setting( 'click5_sitemap', 'click5_sitemap_is_multiple_time' );

  $post_types = click5_sitemap_get_post_types();
  foreach($post_types as $single_type) {
    $single_type = get_post_type_object($single_type);
    $option_name = 'click5_sitemap_display_'.$single_type->name;
    $option_names = array('click5_sitemap_use_custom_name_'.$single_type->name, 'click5_sitemap_custom_name_text_'.$single_type->name);

    unregister_setting('click5_sitemap', $option_name);
    unregister_setting('click5_sitemap', $option_names[0]);
    unregister_setting('click5_sitemap', $option_names[1]);
    //seo
    unregister_setting('click5_sitemap_seo', 'click5_sitemap_seo_post_type_'.$single_type->name);
  }

  unregister_setting('click5_sitemap_seo', 'click5_sitemap_seo_xml_categories');
  unregister_setting('click5_sitemap_seo', 'click5_sitemap_seo_xml_tags');
  unregister_setting('click5_sitemap_seo', 'click5_sitemap_seo_xml_authors');

  $cpt_args = array('public'   => true, '_builtin' => false);
  $cpt_output = 'names';
  $cpt_operator = 'and';
  $cpt_types = get_taxonomies( $cpt_args, $cpt_output, $cpt_operator ); 
  foreach ( $cpt_types  as $cpt_type ) { 
    unregister_setting('click5_sitemap_seo', 'click5_sitemap_seo_xml_'.$cpt_type.'_tax');
  }

  $getCustomCat = click5_sitemap_getCustomCategoriesCustomUrlsHTML();                       
  foreach($getCustomCat as $item) {
    unregister_setting('click5_sitemap_seo', 'click5_sitemap_seo_xml_'.str_replace(' ', '_', $item).'_custom');
  }

  unregister_setting('click5_sitemap', 'click5_sitemap_display_style');
  unregister_setting('click5_sitemap', 'click5_sitemap_display_columns');

  unregister_setting('click5_sitemap', 'click5_sitemap_url_target_blanc');

  unregister_setting('click5_sitemap_blacklist', 'click5_sitemap_blacklisted_array');
  unregister_setting('click5_sitemap_blacklist_seo', 'click5_sitemap_seo_blacklisted_array');


  //seo

  unregister_setting('click5_sitemap_seo', 'click5_sitemap_seo_sitemap_xml');
  unregister_setting('click5_sitemap_seo', 'click5_sitemap_seo_robots_txt');
  unregister_setting('click5_sitemap_seo', 'click5_sitemap_seo_include_sitemap_xml');
  unregister_setting('click5_sitemap_seo', 'click5_sitemap_seo_sitemap_type');
  unregister_setting('click5_sitemap_seo', 'click5_sitemap_seo_auto');


  //order list
  unregister_setting('click5_sitemap_html', 'click5_sitemap_order_list');
  unregister_setting('click5_sitemap_html', 'click5_sitemap_order_list_nested');
  unregister_setting('click5_sitemap_html', 'click5_sitemap_html_pagination_items_per_page');

  unregister_setting('click5_sitemap_urls', 'click5_sitemap_urls_list');

  //for authentication purposes
  unregister_setting('click5_sitemap_authentication', 'click5_sitemap_authentication_token_'.wp_get_current_user()->user_login);
}

register_uninstall_hook(__FILE__, 'click5_sitemap_uninstallFunction');




















