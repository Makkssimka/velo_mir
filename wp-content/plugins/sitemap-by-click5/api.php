<?php

function click5_sitemap_send_notification($message, $type) {
  return array('notification' => true, 'message' => $message, 'type' => $type);
}

function click5_sitemap_get_post_types($at_least = 1) {
  $post_types = get_post_types(array('public' => true), 'names');
  $result_post_types = array();

  foreach($post_types as $post_type) {
    $posts = get_posts(array('post_type' => $post_type, 'post_status' => 'publish', 'numberposts' => $at_least));
    if (count($posts) == $at_least) {
      $result_post_types[] = $post_type;
    }
  }

  return $result_post_types;
}

function click5_sitemap_getCustomUrlsHTML($type = null) {
  $result = array();

  $custom_urls_list = (array)json_decode(get_option('click5_sitemap_urls_list'));

  foreach($custom_urls_list as $url_item) {
    $url_item = $url_item;
    if ($type && $url_item->category->name !== $type) {
      continue;
    }
    if ($url_item->enabledHTML) {
      $result[] = $url_item;
    }
  }

  return $result;
}

function click5_sitemap_getCustomCategoriesCustomUrlsHTML() {
  $result = array();

  $custom_urls_list = (array)json_decode(get_option('click5_sitemap_urls_list'));

  foreach($custom_urls_list as $url_item) {
    $url_item = $url_item;
    if ($url_item->enabledHTML && $url_item->category->use_custom && !in_array($url_item->category->name, $result)) {
      $result[] = $url_item->category->name;
    }
  }

  return $result;
}

function click5_sitemap_getCustomUrlsXML($type = null) {
  $result = array();

  $custom_urls_list = (array)json_decode(get_option('click5_sitemap_urls_list'));

  foreach($custom_urls_list as $url_item) {
    $url_item = $url_item;
    if ($type && $url_item->category->name !== $type) {
      continue;
    }

    if ($url_item->enabledXML) {
      $result[] = $url_item;
    }
  }

  return $result;
}

function click5_sitemap_getCustomCategoriesCustomUrlsXML() {
  $result = array();

  $custom_urls_list = (array)json_decode(get_option('click5_sitemap_urls_list'));

  foreach($custom_urls_list as $url_item) {
    $url_item = $url_item;

    if ($url_item->enabledXML && $url_item->category->use_custom && !in_array($url_item->category->name, $result)) {
      $result[] = $url_item->category->name;
    }
  }

  return $result;
}


function click5_sitemap_sort_default_order(&$arr, $compareDates = true) {
  usort($arr, function($a, $b) {
    return strcmp(strtolower($a['title']), strtolower($b['title']));
  });
}

function click5_sitemap_sort_default_date(&$arr, $compareDates = true) {
  usort($arr, function($a, $b) {
    $_buffA = explode('__', $a['ID']);
    $_buffB = explode('__', $b['ID']);

    if (!$_buffA[1] || !$_buffB[1]) {
      return 0;
    } else {
      return strtotime('01-'.$_buffB[1]) - strtotime('01-'.$_buffA[1]);
    }
  });
}



function click5_sitemap_get_order_list_populate_subchildren($blacklist, &$item) {
  $posts = get_children(array('post_parent' => $item['ID'], 'post_status' => 'publish', 'numberposts' => -1));
  if (count($posts)) {
   $item['children'] = array();

    foreach($posts as $post) {
      if (in_array($post->ID, $blacklist)) {
        continue;
      }

      $newChild = array('ID' => $post->ID, 'title' => $post->post_title);
      click5_sitemap_get_order_list_populate_subchildren($blacklist, $newChild);
      $item['children'][] = $newChild;
    }

    if (count($item['children'])) {
      click5_sitemap_sort_default_order($item['children']);
    }
  }
}

function click5_sitemap_get_safe_term($id, $term) {
  $page = get_post($id);
  if (!$page) {
    return null;
  }

  $new_term = $term;

  if ( class_exists( 'WooCommerce' ) ) {
    if($page->post_type == 'product' && $term == 'category'){
      $new_term = 'product_cat';
    } elseif($page->post_type == 'product' && $term == 'post_tag'){
      $new_term = 'product_tag';
    }
  }

  $termArr = get_the_terms($id, $new_term);

  if (!$termArr) {
    return null;
  }

  if (!$termArr[0]) {
    return null;
  }

  return array('term_id' => $termArr[0]->term_id, 'name' => $termArr[0]->name);
}

function click5_sitemap_remap_to_groups_single(&$arrayToplevel, &$noTerm, $childToplevel, $groupingType, $postType) {
  $childToplevel = (array)$childToplevel;
  if ($groupingType !== 'archives') {
    $current_page_term = click5_sitemap_get_safe_term($childToplevel['ID'], $groupingType == 'categories' ? 'category' : 'post_tag');
    if ($current_page_term == null) {
      $noTerm[] = $childToplevel;
    } else {
      $bItemHasBeenAssigned = false;
      foreach($arrayToplevel as &$potentialParent) {
        if ($potentialParent['ID'] == 'g_'.$postType.'__'.$current_page_term['term_id']) {
          $potentialParent['children'][] = $childToplevel;
          $bItemHasBeenAssigned = true;
        }
      }
      if(!$bItemHasBeenAssigned) {
        $arrayToplevel[] = array(
          'ID' => 'g_'.$postType.'__'.$current_page_term['term_id'],
          'title' => $current_page_term['name'],
          'children' => array($childToplevel)
        );
      }
    }
  } else {
    //archives here
    //file_put_contents(dirname(__FILE__).'/logs.txt', $childToplevel['title'].PHP_EOL, FILE_APPEND);
    $monthName = get_the_date('F Y', $childToplevel['ID']);
    $monthId = get_the_date('m-Y', $childToplevel['ID']);

    if (!empty($monthName) && !empty($monthId)) {
      $bItemHasBeenAssigned = false;
        foreach($arrayToplevel as &$potentialParent) {
          if ($potentialParent['ID'] == 'g_'.$postType.'__'.$monthId) {
            $potentialParent['children'][] = $childToplevel;
            $bItemHasBeenAssigned = true;
          }
        }
        if(!$bItemHasBeenAssigned) {
          $arrayToplevel[] = array(
            'ID' => 'g_'.$postType.'__'.$monthId,
            'title' => $monthName,
            'children' => array($childToplevel)
          );
        }
    } else {
      $noTerm[] = $childToplevel;
    }
  }
}

function click5_sitemap_get_order_list($_style = 'group') {
  $orderList = array();

  $post_types = click5_sitemap_get_post_types();
  $enabledTypesArray = array();
  $TaxenabledTypesOriginalArray = array();
  $TaxenabledTypesArray = array();

  //$_style = esc_attr( get_option('click5_sitemap_display_style') );
  foreach($post_types as $single_type) {
    $single_type = get_post_type_object($single_type);
    $option_name = 'click5_sitemap_display_'.$single_type->name;

    if (boolval(esc_attr( get_option($option_name) ))) {
      $enabledTypesArray[] = $single_type->name;
    }
  }

  if (boolval(esc_attr( get_option('click5_sitemap_display_cat_tax') ))) {
    $TaxenabledTypesOriginalArray[] = 'categories';
  }

  if (boolval(esc_attr( get_option('click5_sitemap_display_tag_tax') ))) {
    $TaxenabledTypesOriginalArray[] = 'tags';
  }

  $tax_args=array(
    'public'   => true,
    '_builtin' => false
  );
  $output = 'names'; // or objects
  $operator = 'and';
  $taxonomies=get_taxonomies($tax_args,$output,$operator);

  if  ($taxonomies) {
    foreach ($taxonomies  as $taxitem ) {

      $tax_option_name = 'click5_sitemap_display_'.$taxitem;

      if (boolval(esc_attr( get_option($tax_option_name) ))) {
        $TaxenabledTypesArray[] = $taxitem;
      }
     }  
  }


  $blacklistArray = json_decode(get_option('click5_sitemap_blacklisted_array'));
  $blacklist = array_column($blacklistArray ? $blacklistArray : array(), 'ID');
  $groupingType = esc_attr(get_option('click5_sitemap_html_blog_group_by'));
  if ($_style === 'group') {


    foreach($enabledTypesArray as $type) {
      $typeObject = array();
      $typeObject['ID'] = $type;
      if (intval(esc_attr(get_option('click5_sitemap_use_custom_name_'.$type)))) {
        $typeObject['title'] = esc_attr(get_option('click5_sitemap_custom_name_text_'.$type));
      } else {
        $typeObject['title'] = get_post_type_object($type)->label;
      }
      $typeObject['children'] = array();

      $posts = get_posts(array('post_parent' => 0, 'post_type' => $type, 'post_status' => 'publish', 'numberposts' => -1));

      foreach($posts as $post) {
        if (in_array($post->ID, $blacklist)) {
          continue;
        }
        $newChild = array('ID' => $post->ID, 'title' => $post->post_title);
        click5_sitemap_get_order_list_populate_subchildren($blacklist, $newChild);
        $typeObject['children'][] = $newChild;
      }

      $customLinks = click5_sitemap_getCustomUrlsHTML($type);

      foreach($customLinks as $customLink) {
        $typeObject['children'][] = array('ID' => $customLink->ID, 'title' => $customLink->title);
      }

      click5_sitemap_sort_default_order($typeObject['children']);

      $postTypeGroupingEnabled = boolval(esc_attr(get_option('click5_sitemap_html_blog_treat_'.$type)));

      if ($groupingType !== 'disabled' && $postTypeGroupingEnabled) {
        $newChildrenToplevel = array();
        $noTerm = array();
        foreach($typeObject['children'] as $childToplevel) {
          click5_sitemap_remap_to_groups_single($newChildrenToplevel, $noTerm, $childToplevel, $groupingType, $type);
        }

        if ($groupingType !== 'archives') {
          click5_sitemap_sort_default_order($newChildrenToplevel);
          click5_sitemap_sort_default_order($noTerm);
        } else {
          click5_sitemap_sort_default_date($newChildrenToplevel);
          click5_sitemap_sort_default_order($noTerm);
        }

        $typeObject['children'] = array();

        foreach($newChildrenToplevel as $grouped) {
          $typeObject['children'][] = $grouped;
        }

        foreach($noTerm as $not_grouped) {
          $typeObject['children'][] = $not_grouped;
        }
      }

      $orderList[] = $typeObject;

      click5_sitemap_sort_default_order($orderList);
    }


    foreach($TaxenabledTypesOriginalArray as $type) {
      $typeObject = array();
      $typeObject['ID'] = $type . '_tax';

      if($type == 'categories'){

        $short_type = 'cat';
        $tax_items = get_categories();

      } elseif($type == 'tags'){

        $short_type = 'tag';
        $tax_items = get_tags();
        
      }

      if (intval(esc_attr(get_option('click5_sitemap_use_custom_name_'.$short_type.'_tax')))) {
        $typeObject['title'] = esc_attr(get_option('click5_sitemap_custom_name_text_'.$short_type.'_tax'));
      } else {
        $typeObject['title'] = ucwords($type);
      }
      $typeObject['children'] = array();


      foreach ( $tax_items as $term) {

        $newChild = array('ID' => $term->term_id.'_'.$short_type.'_tax', 'title' => $term->name);
        $typeObject['children'][] = $newChild;
        
      }

      $orderList[] = $typeObject;
      click5_sitemap_sort_default_order($orderList);

    }


    foreach($TaxenabledTypesArray as $type) {
      $typeObject = array();
      $typeObject['ID'] = $type;

      $original_name = get_taxonomy($type); 

      if (intval(esc_attr(get_option('click5_sitemap_use_custom_name_'.$type)))) {
        $typeObject['title'] = esc_attr(get_option('click5_sitemap_custom_name_text_'.$type));
      } else {
        $typeObject['title'] = ucwords($original_name->label);
      }
      $typeObject['children'] = array();


      $tax_items = get_terms($type);

      foreach ( $tax_items as $term) {

        $newChild = array('ID' => $term->term_id.'_tax', 'title' => $term->name);
        $typeObject['children'][] = $newChild;
        
      }

      $orderList[] = $typeObject;

      click5_sitemap_sort_default_order($orderList);

    }


    $customCategories = click5_sitemap_getCustomCategoriesCustomUrlsHTML();
    foreach($customCategories as $category) {
      $typeObject = array();
      $typeObject['ID'] = $category;
      $typeObject['title'] = $category;
      $typeObject['children'] = array();
      $customPosts = click5_sitemap_getCustomUrlsHTML($category);

      foreach($customPosts as $custompost) {
        $typeObject['children'][] = array('ID' => $custompost->ID, 'title' => $custompost->title);
      }

      click5_sitemap_sort_default_order($typeObject['children']);

      $orderList[] = $typeObject;
    }

  } else if ($_style === 'merge') {
    $posts = get_posts(array('post_type' => $enabledTypesArray, 'post_status' => 'publish', 'numberposts' => -1));
    $customPosts = click5_sitemap_getCustomUrlsHTML();
    foreach($posts as $post) {
      if (in_array($post->ID, $blacklist)) {
        continue;
      }
      $orderList[] = array('ID' => $post->ID, 'title' => $post->post_title);
    }
    foreach($customPosts as $post) {
      $orderList[] = array('ID' => $post->ID, 'title' => $post->title);
    }

    click5_sitemap_sort_default_order($orderList, false);
  }

  set_old_order();

  return $orderList;
}

function click5_sitemap_HTML_get_nestedElements(){
  return json_decode(get_option('click5_sitemap_order_list_nested'));
}

//recursive function
function click5_sitemap_HTML_display_children_sublist(&$html, $item) {
  if(isset($item['children'])){
    $item['children'] = (array)$item['children'];
    if(count($item['children'])) {
      $html .= '<ol class="dd-list">';

      $nestedElements = click5_sitemap_HTML_get_nestedElements();

      foreach($item['children'] as $child) {
        
        if (!empty($nestedElements)) {
          
          $isInArray = false;
          foreach($nestedElements as $element){ 
            if($element->element == $child['ID']){
              $isInArray = true;
            }
          }

          $isParentInArray = false;
          foreach($nestedElements as $element){ 
            if($element->parent == $child['ID'] && $element->toOriginalNested == false){
              $isParentInArray = true;
            }
          }

          if(!$isInArray){
            $html .= '<li class="dd-item sub-item" data-value="'.esc_attr($child['ID']).'" name="'.esc_attr($item['ID']).'"><div class="dd-handle">'.esc_attr($child['title']).'</div>';
            click5_sitemap_HTML_display_children_sublist($html, $child);
          }
          
          if($isParentInArray){
            $html .= '<ol class="dd-list">';
            foreach($nestedElements as $value){ 
              if($value->parent == $child['ID']){

                if($value->original_parent){
                  $custom_class = 'original-nested';
                } else {
                  $custom_class = 'custom-nested';
                }

                $html .= '<li class="dd-item sub-item '.$custom_class.'" data-value="'.esc_attr($value->element).'"><div class="dd-handle">'.esc_attr($value->title).'</div></li>';
              }
            }
            $html .= '</ol>';
          }

        } else {
          $html .= '<li class="dd-item sub-item" data-value="'.esc_attr($child['ID']).'" name="'.esc_attr($item['ID']).'"><div class="dd-handle">'.esc_attr($child['title']).'</div>';
          click5_sitemap_HTML_display_children_sublist($html, $child);
        }

        $html .= '</li>';
      }

      if (!empty($nestedElements)) {
        $isMainParentInArray = false;
        foreach($nestedElements as $element){ 
          if($element->parent == $item['ID']){
            $isMainParentInArray = true;
          }
        }

        if($isMainParentInArray){
          foreach($nestedElements as $value){ 
            if($value->parent == $item['ID']){

              if($value->original_parent){
                $custom_class = 'original-nested';
              } else {
                $custom_class = 'custom-nested';
              }

              $html .= '<li class="dd-item sub-item '.$custom_class.'" data-value="'.esc_attr($value->element).'"><div class="dd-handle">'.esc_attr($value->title).'</div></li>';
            }
          }
        }
      }


      $html .= '</ol>';

    }
  }
}

function sortByOrder($a, $b) {
  return $a->order > $b->order;
}

function set_old_order() {

  $styleSett = esc_attr( get_option('click5_sitemap_display_style') );
  $_style = !empty($styleSett) ? $styleSett : 'group';

  if ($_style === 'group') {

    if(get_option('click5_sitemap_order_list_old') && get_option('click5_sitemap_order_list')){
    $get_order_old = json_decode(get_option('click5_sitemap_order_list_old'));
    $get_order_new = json_decode(get_option('click5_sitemap_order_list'));

    foreach($get_order_old as $value){ 
      foreach($get_order_new as $value_new){ 
        if($value->ID == $value_new->ID){

          $value_new->order = $value->order;

          if($value->parent != false){
            $value_new->parent = $value->parent;
          }

        }
      }
    }

    usort($get_order_new, 'sortByOrder');

    return update_option('click5_sitemap_order_list', json_encode($get_order_new));

    }
  }
}

function click5_sitemap_HTML_sitemap_display_order_list() {

    $html = '';
    $styleSett = esc_attr( get_option('click5_sitemap_display_style') );
    $_style = !empty($styleSett) ? $styleSett : 'group';

    set_old_order();
  
    $arrayOrderList = click5_sitemap_get_order_list($_style);
  
    if ($_style === 'group') {
      foreach($arrayOrderList as $orderItem) {
        $html .= '<li class="dd-item group" data-value="'.esc_attr($orderItem['ID']).'"><div class="dd-handle">'.esc_attr($orderItem['title']).'</div>';
        click5_sitemap_HTML_display_children_sublist($html, $orderItem);
      }
    } else if ($_style === 'merge') {
      foreach($arrayOrderList as $orderItem) {
        $html .= '<li class="dd-item" data-value="'.esc_attr($orderItem['ID']).'"><div class="dd-handle">'.esc_attr($orderItem['title']).'<div></li>';
      }
    }

  return $html;
  
}
require('HTMLsitemap.php');

function click5_sitemap_display_sitemap() {

  $version = CLICK5_SITEMAP_DEV_MODE ? time() : CLICK5_SITEMAP_VERSION;

  wp_enqueue_style( 'click5_sitemap_css_front', plugins_url('/css/front/index.css', __FILE__), array(), $version );

  $HTMLSitemap = new HTMLSitemapList();
  $paged = 1;
  if ( get_query_var('paged') ) {
      $paged = get_query_var('paged');
  } elseif ( get_query_var('page') ) { // 'page' is used instead of 'paged' on Static Front Page
      $paged = get_query_var('page');
  } else {
      $paged = 1;
  }

  $HTMLSitemap->set_paged($paged);
  return $HTMLSitemap->display();
}

add_shortcode( 'click5_sitemap', 'click5_sitemap_display_sitemap' );

function click5_sitemap_checkIsAValidDate($myDateString){
    return (bool)strtotime($myDateString);
}


function click5_sitemap_generate_CreateXML($name, $items, $custom_items = null) {
  $blacklistSeoIDArray = json_decode(get_option('click5_sitemap_seo_blacklisted_array'));
  $blacklistSeoID = array_column($blacklistSeoIDArray ? $blacklistSeoIDArray : array(), 'ID');

  $file_xml = '<?xml version="1.0" encoding="UTF-8"?>';
  $file_xml .= '<?xml-stylesheet type="text/xsl" href="'.esc_url(plugins_url('', __FILE__)).'/css/front/template.xsl" ?>';

  $file_xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

  $lastmod = date('Y-m-d');

  foreach($items as $page) {
    if(in_array($page->ID, $blacklistSeoID)) {
      continue;
    }

    $permalink = get_permalink($page->ID);

    $file_xml .= '<url>';

    $page_mod = date('Y-m-d', strtotime($page->post_modified));
    if ($page_mod > $lastmod) {
      $lastmod = $page_mod;
    }

    

    $file_xml .= '<loc>'.esc_url($permalink).'</loc>';
    $file_xml .= '<lastmod>'.esc_attr($page_mod).'</lastmod>';

    $file_xml .= '</url>';
  }

  if ($custom_items) {
    foreach($custom_items as $custom_page) {
      $file_xml .= '<url>';
      $file_xml .= '<loc>'.esc_url($custom_page->url).'</loc>';
        if (click5_sitemap_checkIsAValidDate($custom_page->last_mod)) {
          $file_xml .= '<lastmod>'.date('Y-m-d', strtotime(esc_attr($custom_page->last_mod))).'</lastmod>';
        }
      
      $file_xml .= '</url>';
    }
  }

  $file_xml .= '</urlset>';

  clearstatcache();
  if (file_exists(ABSPATH.'/'.$name)) {
    unlink(ABSPATH.'/'.$name);
  }

  click5_ping_sitemap_to_google(site_url().'/'.$name, 'post_'.$name);

  if (file_put_contents(ABSPATH.'/'.basename($name), $file_xml)) {
    return array('url' => esc_url(site_url().'/'.basename($name)), 'lastmod' => $lastmod);
  } else {
    return false;
  }
}


function click5_sitemap_generate_category_list($name) {
  
  $file_xml = '<?xml version="1.0" encoding="UTF-8"?>';
  $file_xml .= '<?xml-stylesheet type="text/xsl" href="'.esc_url(plugins_url('', __FILE__)).'/css/front/template.xsl" ?>';

  $file_xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

  $lastmod = date('Y-m-d');

  $categories = get_categories();

  if($categories){
    foreach($categories as $category) {

      $permalink = get_category_link($category->term_id);

      $file_xml .= '<url>';
      $file_xml .= '<loc>'.$permalink.'</loc>';
      $file_xml .= '<lastmod>'.esc_attr($lastmod).'</lastmod>';
      $file_xml .= '</url>';

    }

    $file_xml .= '</urlset>';

    clearstatcache();
    if (file_exists(ABSPATH.'/'.$name)) {
      unlink(ABSPATH.'/'.$name);
    }

    click5_ping_sitemap_to_google(site_url().'/'.$name, 'categories');

    if (file_put_contents(ABSPATH.'/'.basename($name), $file_xml)) {
      return array('url' => esc_url(site_url().'/'.basename($name)), 'lastmod' => $lastmod);
    } else {
      return false;
    }
  } else {
    update_option('click5_sitemap_seo_xml_categories', false);
  }
}


function click5_sitemap_generate_tag_list($name) {
  
  $file_xml = '<?xml version="1.0" encoding="UTF-8"?>';
  $file_xml .= '<?xml-stylesheet type="text/xsl" href="'.esc_url(plugins_url('', __FILE__)).'/css/front/template.xsl" ?>';

  $file_xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

  $lastmod = date('Y-m-d');

  $categories = get_tags();

  if($categories){

    foreach($categories as $category) {

      $permalink = get_tag_link($category->term_id);

      $file_xml .= '<url>';
      $file_xml .= '<loc>'.$permalink.'</loc>';
      $file_xml .= '<lastmod>'.esc_attr($lastmod).'</lastmod>';
      $file_xml .= '</url>';

    }

    $file_xml .= '</urlset>';

    clearstatcache();
    if (file_exists(ABSPATH.'/'.$name)) {
      unlink(ABSPATH.'/'.$name);
    }

    click5_ping_sitemap_to_google(site_url().'/'.$name, 'tags');

    if (file_put_contents(ABSPATH.'/'.basename($name), $file_xml)) {
      return array('url' => esc_url(site_url().'/'.basename($name)), 'lastmod' => $lastmod);
    } else {
      return false;
    }

  } else {
    update_option('click5_sitemap_seo_xml_tags', false);
  }
}


function click5_sitemap_generate_custom_taxonomy_list($name, $cpt) {
  
  $file_xml = '<?xml version="1.0" encoding="UTF-8"?>';
  $file_xml .= '<?xml-stylesheet type="text/xsl" href="'.esc_url(plugins_url('', __FILE__)).'/css/front/template.xsl" ?>';
  $file_xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
  $lastmod = date('Y-m-d');

  $taxonomy_objects = get_terms($cpt);

  if($taxonomy_objects) {
    
    foreach ( $taxonomy_objects as $term) {
  
      $termlink = get_term_link ( $term );
        
      if( strpos($termlink, '?') === false ){
        $file_xml .= '<url>';
        $file_xml .= '<loc>'.$termlink.'</loc>';
        $file_xml .= '<lastmod>'.esc_attr($lastmod).'</lastmod>';
        $file_xml .= '</url>';
      }
  
    }

    $file_xml .= '</urlset>';

    clearstatcache();
    if (file_exists(ABSPATH.'/'.$name)) {
      unlink(ABSPATH.'/'.$name);
    }

    click5_ping_sitemap_to_google(site_url().'/'.$name, 'tax_'.$name);

    if (file_put_contents(ABSPATH.'/'.basename($name), $file_xml)) {
      return array('url' => esc_url(site_url().'/'.basename($name)), 'lastmod' => $lastmod);
    } else {
      return false;
    }
  } else {

    update_option('click5_sitemap_seo_xml_'.$cpt.'_tax', false);
    
  }
}


function click5_sitemap_generate_author_list($name) {

  $post_types = get_post_types(array('public' => true), 'names');
  
  $file_xml = '<?xml version="1.0" encoding="UTF-8"?>';
  $file_xml .= '<?xml-stylesheet type="text/xsl" href="'.esc_url(plugins_url('', __FILE__)).'/css/front/template.xsl" ?>';

  $file_xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

  $lastmod = date('Y-m-d');

  $users = get_users();

  if ( ! empty( $users ) ) {
      foreach( $users as $user ) {

        if ( count_user_posts( $user->ID, $post_types ) >= 1 ) {

          $permalink = get_author_posts_url($user->ID);

          $file_xml .= '<url>';
          $file_xml .= '<loc>'.$permalink.'</loc>';
          $file_xml .= '<lastmod>'.esc_attr($user->user_registered).'</lastmod>';
          $file_xml .= '</url>';

        }
      }
  }

  $file_xml .= '</urlset>';

  clearstatcache();
  if (file_exists(ABSPATH.'/'.$name)) {
    unlink(ABSPATH.'/'.$name);
  }

  click5_ping_sitemap_to_google(site_url().'/'.$name, 'authors');

  if (file_put_contents(ABSPATH.'/'.basename($name), $file_xml)) {
    return array('url' => esc_url(site_url().'/'.basename($name)), 'lastmod' => $lastmod);
  } else {
    return false;
  }
  
}


function click5_sitemap_generate_CreateRootXML($name, $items) {
  $file_xml = '<?xml version="1.0" encoding="UTF-8"?>';
  $file_xml .= '<?xml-stylesheet type="text/xsl" href="'.esc_url(plugins_url('', __FILE__)).'/css/front/index-template.xsl" ?>';

  $file_xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

  $lastmod = date('Y-m-d');

  foreach($items as $page) {
    if($page){
      $file_xml .= '<sitemap>';
      $file_xml .= '<loc>'.esc_url($page['url']).'</loc>';
      $file_xml .= '<lastmod>'.esc_attr($page['lastmod']).'</lastmod>';
  
      $file_xml .= '</sitemap>';
    }
  }

  $file_xml .= '</sitemapindex>';
  clearstatcache();
  if (file_exists(ABSPATH.'/'.$name)) {
    unlink(ABSPATH.'/'.$name);
  }

  click5_ping_sitemap_to_google(site_url().'/'.$name, 'root');

  if (file_put_contents(ABSPATH.'/'.basename($name), $file_xml)) {
    return array('url' => esc_url(site_url().'/'.basename($name)), 'lastmod' => $lastmod);
  } else {
    return false;
  }
}

function click5_sitemap_pluralName($name) {
  return (substr($name, -1) == 's') ? $name : $name.'s';
}


function click5_sitemap_generate_sitemap_XML_DoWork() {
  click5_sitemap_generate_delete_existing_sitemaps();

  if(boolval(esc_attr( get_option('click5_sitemap_seo_sitemap_xml')))){

    $sitemap_type = esc_attr( get_option('click5_sitemap_seo_sitemap_type'));
    $post_types = array();
    $_post_types = click5_sitemap_get_post_types();
    foreach($_post_types as $post_type) {
      if(boolval(esc_attr( get_option('click5_sitemap_seo_post_type_'.$post_type)))) {
        $post_types[] = $post_type;
      }
    }
    if ($sitemap_type == 'splitted') {
      $sitemapsCreated = array();
      foreach($post_types as $post_type) {
        $pages = get_posts(array('post_status' => 'publish', 'numberposts' => -1, 'post_type' => $post_type));
        $result = click5_sitemap_generate_CreateXML('sitemap-'.$post_type.'.xml', $pages, click5_sitemap_getCustomUrlsXML($post_type));
        if (is_array($result)) {
          $sitemapsCreated[] = $result;
        }
      }

      if(boolval(esc_attr( get_option('click5_sitemap_seo_xml_categories')))){
        $sitemapsCreated[] = click5_sitemap_generate_category_list('sitemap-category.xml');
      }

      if(boolval(esc_attr( get_option('click5_sitemap_seo_xml_tags')))){
        $sitemapsCreated[] = click5_sitemap_generate_tag_list('sitemap-tag.xml');
      }


      $cpt_args = array(
        'public'   => true,
        '_builtin' => false,
      );
      $cpt_output = 'names';
      $cpt_operator = 'and';
      $cpt_types = get_taxonomies( $cpt_args, $cpt_output, $cpt_operator ); 
      
      foreach ( $cpt_types  as $cpt_type ) { 

        if(boolval(esc_attr( get_option('click5_sitemap_seo_xml_'.$cpt_type.'_tax')))){
    
          $sitemapsCreated[] = click5_sitemap_generate_custom_taxonomy_list('sitemap-'.$cpt_type.'-tax.xml', $cpt_type);

        }
      
      }


      if(boolval(esc_attr( get_option('click5_sitemap_seo_xml_authors')))){
        $sitemapsCreated[] = click5_sitemap_generate_author_list('sitemap-author.xml');
      }

      $customCategoriesXML = click5_sitemap_getCustomCategoriesCustomUrlsXML();
      sort($customCategoriesXML);
      foreach($customCategoriesXML as $custom_category) {

        if(boolval(esc_attr( get_option('click5_sitemap_seo_xml_'.str_replace(' ', '_', $custom_category).'_custom')))){

          $subname = strtolower(str_replace(' ', '-', $custom_category));
          $result = click5_sitemap_generate_CreateXML('sitemap-'.$subname.'.xml', array(), click5_sitemap_getCustomUrlsXML($custom_category));
          if (is_array($result)) {
            $sitemapsCreated[] = $result;
          }

        }

        
      }
      click5_sitemap_generate_CreateRootXML('sitemap-index.xml', $sitemapsCreated);
    } else if ($sitemap_type == 'one_file') {
      $pages = get_posts(array('post_status' => 'publish', 'numberposts' => -1, 'post_type' => $post_types));
      $custom_urls_list = click5_sitemap_getCustomUrlsXML();
      click5_sitemap_generate_CreateXML('sitemap-index.xml', $pages, $custom_urls_list);
    }

    return '/sitemap-index.xml';

  }

  if (boolval(esc_attr(get_option('click5_sitemap_seo_robots_txt')))) {
    click5_sitemap_generate_robots_txt();
  } else {
    if (file_exists(ABSPATH.'/robots.txt')) {
      
      if(boolval(esc_attr(get_option('click5_sitemap_seo_robots_backup'))) && file_exists(ABSPATH.'/robots-click5-backup.txt') ){
        unlink(ABSPATH.'/robots.txt');

        //Backup old robots.txt file
        if (file_exists(ABSPATH.'/robots-click5-backup.txt')) {
          rename(ABSPATH.'/robots-click5-backup.txt', ABSPATH.'/robots.txt');
        }

      } elseif (empty(get_option('click5_sitemap_seo_robots_backup'))){
        unlink(ABSPATH.'/robots.txt');
      }
    }
  }
}

function click5_sitemap_generate_delete_existing_sitemaps() {
  $files_to_delete = glob(ABSPATH.'/*sitemap*.xml');
  foreach($files_to_delete as $file) {
    unlink($file);
  }
  return $files_to_delete;
}

function click5_sitemap_print_robots_txt($ret = false) {


  $path = ABSPATH;
  $robots_txt = ABSPATH.'/robots.txt';
  clearstatcache();
  if (!file_exists($robots_txt)) {
    if ($ret) {
      return 'robots.txt not generated yet.';
    } else {
      echo 'robots.txt not generated yet.';
    }
  } else {
    if ($ret) {
      return '<a href="'.esc_url(get_bloginfo('url').'/robots.txt').'" target="_blank" rel="nofollow">'.esc_url(get_bloginfo('url').'/robots.txt').'</a>'.'<textarea rows="7" disabled="disabled" style="margin-top: 15px; resize: none;">'.esc_attr(file_get_contents($robots_txt)).'</textarea>';
    } else {
      echo '<a href="'.esc_url(get_bloginfo('url').'/robots.txt').'" target="_blank" rel="nofollow">'.esc_url(get_bloginfo('url').'/robots.txt').'</a>'.'<textarea rows="7" disabled="disabled" style="margin-top: 15px; resize: none;">'.esc_attr(file_get_contents($robots_txt)).'</textarea>';
    }
  }
}

function click5_sitemap_generate_robots_txt() {

  //Check old robots.txt file
  if (file_exists(ABSPATH.'/robots.txt') && !file_exists(ABSPATH.'/robots-click5-backup.txt')) {
    rename(ABSPATH.'/robots.txt', ABSPATH.'/robots-click5-backup.txt');
  }

  $content = '';

  if (boolval(esc_attr(get_option('click5_sitemap_seo_include_sitemap_xml')))) {
    $content = 'Sitemap: '.esc_url(site_url().'/'.basename('sitemap.xml')).PHP_EOL;
  }

  $content .= 'User-agent: *'.PHP_EOL.PHP_EOL;

  $blacklistSeoIDArray = json_decode(get_option('click5_sitemap_seo_blacklisted_array'));
  $blacklistSeoID = array_column($blacklistSeoIDArray ? $blacklistSeoIDArray : array(), 'ID');

  /* foreach($blacklistSeoID as $blockedPage) {
    $permalink = str_replace(home_url(), '', get_permalink($blockedPage));
    $content .= 'Disallow: '.esc_url($permalink).PHP_EOL;
  } */
  $content .= PHP_EOL;

  $post_types = click5_sitemap_get_post_types();

  /* foreach($post_types as $post_type) {
    if(!boolval(esc_attr( get_option('click5_sitemap_seo_post_type_'.$post_type)))) {
      $pages = get_posts(array('post_status' => 'publish', 'numberposts' => -1, 'post_type' => $post_type));
      foreach($pages as $page) {
        $permalink = str_replace(home_url(), '', get_permalink($page->ID));
        if (!(strpos($content, $permalink) !== false)) {
          $content .= 'Disallow: '.esc_url($permalink).PHP_EOL;
        }
      }
    }
  } */

  $robots_txt = ABSPATH.'/robots.txt';

  file_put_contents($robots_txt, $content);
}

/*
if (node.firstElementChild) {
  let nodeObj = { ID: node.getAttribute('data-value'), children: [] }
  node.firstElementChild.childNodes.forEach(childNode => {
    nodeObj.children.push(getOrderElement(childNode, nodeObj));
  });
  return nodeObj;
} else {
  return { ID: node.getAttribute('data-value') };
}*/

function click5_sitemap_simulate_get_order_element($item) {
  $item = (array)$item;
  $newItem = array('ID' => $item['ID'], 'children' => array());

  if (isset($item['children'][0])) {
    foreach($item['children'] as &$subitem) {
      $newItem['children'][] = click5_sitemap_simulate_get_order_element($subitem);
    }
    return $newItem;
  } else {
    return array('ID' => $item['ID']);
  }
}

function click5_sitemap_reset_sitemap_order_inline() {
  $defaultOrder = click5_sitemap_get_order_list();
  $arraySerialized = array();
  foreach($defaultOrder as $item) {
    $arraySerialized[] = click5_sitemap_simulate_get_order_element($item);
  }

  $orderItems = click5_sitemap_order_list_setup_order_values($arraySerialized);

  update_option('click5_sitemap_order_list', json_encode($orderItems));

}

function click5_sitemap_auto_sitemap_XML( $post_id ) {
  click5_sitemap_reset_sitemap_order_inline();

  $is_enabled = boolval(esc_attr( get_option('click5_sitemap_seo_auto')));

  if (!$is_enabled) {
    return;
  }
	// If this is just a revision, don't send the email.
	if ( wp_is_post_revision( $post_id ) ) {
		return;
        }

  $result = click5_sitemap_generate_sitemap_XML_DoWork();
}

add_action( 'save_post', 'click5_sitemap_auto_sitemap_XML' );
add_action( 'save_post', 'set_old_order' );




// for API authentication
function click5_sitemap_requestAuthentication($request) {
  $token = $request->get_header('token');
  $user = $request->get_header('user');
  $saved_token = get_option('click5_sitemap_authentication_token_'.$user);
  $result = $saved_token ? ( $token ? ( strcmp($token, $saved_token) === 0 ) : false ) : false;

  return $result;
}



// API for loading pages dynamicaly

function click5_sitemap_API_request_pages( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  if (!isset($_GET['search'])) {
    return null;
  }

  $searchQuery = sanitize_text_field($_GET['search']);
  $allPostTypes = click5_sitemap_get_post_types();

  $postArray = array();

  if (isset($_GET['type'])) {
    $typeSelected = sanitize_text_field($_GET['type']);
    if (in_array($typeSelected, $allPostTypes)) {
      $postArray = get_posts(array('s' => $searchQuery, 'post_status' => 'publish', 'numberposts' => -1, 'post_type' => $typeSelected));
    } else if ($typeSelected === 'all') {
      $postArray = get_posts(array('s' => $searchQuery, 'post_status' => 'publish', 'numberposts' => -1, 'post_type' => $allPostTypes));
    }
  } else {
    $postArray = get_posts(array('s' => $searchQuery, 'post_status' => 'publish', 'numberposts' => -1, 'post_type' => $allPostTypes));
  }

  $urlMapped = array();
  $blacklistArray = json_decode(get_option('click5_sitemap_blacklisted_array'));
  $blacklist = array_column($blacklistArray ? $blacklistArray : array(), 'ID');
  $blacklistSeoArray = json_decode(get_option('click5_sitemap_seo_blacklisted_array'));
  $blacklistSeoID = array_column($blacklistSeoArray ? $blacklistSeoArray : array(), 'ID');

  $typeTab = sanitize_text_field($_GET['type_tab']);

  foreach($postArray as $postItem) {
    $postItem = (array) $postItem;

    if ($typeTab == 'html' && in_array($postItem['ID'], $blacklist)) {
      continue;
    } else if ($typeTab == 'seo' && in_array($postItem['ID'], $blacklistSeoID)) {
      continue;
    }

    $postItem['url'] = esc_url(get_permalink($postItem['ID']));
    $urlMapped[] = $postItem;
  }

  return $urlMapped;
}

function click5_sitemap_API_get_blacklisted( WP_REST_Request $request ) {
   if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  $array = json_decode(get_option('click5_sitemap_blacklisted_array'));
  $optionedArray = array();
  if ($array) {
    $blacklistSeoIDArray = json_decode(get_option('click5_sitemap_seo_blacklisted_array'));
    $blacklistSeoID = array_column($blacklistSeoIDArray ? $blacklistSeoIDArray : array(), 'ID');
    foreach($array as $arr_item) {
      $arr_item = (array)$arr_item;
      $arr_item['seo_blocked'] = in_array($arr_item['ID'], $blacklistSeoID);
      $optionedArray[] = $arr_item;
    }
  }

  return json_encode($optionedArray);
}

function click5_sitemap_validateID($ID) {
  return is_numeric($ID) || is_numeric(intval($ID));
}

function click5_sitemap_API_add_to_blacklisted( WP_REST_Request $request ) {
   if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  if (!isset($_POST['ID']) || !isset($_POST['post_title']) || !isset($_POST['post_type'])) {
    return false;
  }

  $postID = sanitize_key($_POST['ID']);

  //validate if an ID is numeric
  if (!click5_sitemap_validateID($postID)) {
    return false;
  }

  $postTitle = sanitize_text_field($_POST['post_title']);
  $postType = sanitize_text_field($_POST['post_type']);

  $new_item = array('ID' => $postID, 'post_title' => $postTitle, 'post_type' => $postType, 'url' => get_permalink($postID));
  $blacklist = json_decode(get_option('click5_sitemap_blacklisted_array'));

  if (!$blacklist) {
    $blacklist = array();
  }

  $blacklist[] = $new_item;

  update_option('click5_sitemap_blacklisted_array', json_encode($blacklist));

  return json_encode($new_item);
}

function click5_sitemap_API_clear_blacklist( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  update_option('click5_sitemap_blacklisted_array', json_encode(array()));
  return true;
}

function click5_sitemap_API_unblacklist ( WP_REST_Request $request ) {
   if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  if (!isset($_GET['ID'])) {
    return false;
  }

  $getID = sanitize_key($_GET['ID']);

  //validate if an ID is numeric
  if (!click5_sitemap_validateID($getID)) {
    return false;
  }

  $blacklist = json_decode(get_option('click5_sitemap_blacklisted_array'));

  $newBlacklist = array();

  foreach($blacklist as $blacklistItem) {
    if ($blacklistItem->ID !== $getID) {
      $newBlacklist[] = $blacklistItem;
    }
  }

  update_option('click5_sitemap_blacklisted_array', json_encode($newBlacklist));

  return $newBlacklist;
}

function click5_sitemap_API_generate_xml_sitemap ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  return click5_sitemap_generate_sitemap_XML_DoWork();
}

function click5_sitemap_API_seo_block_page (WP_REST_Request $request) {
   if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  if (!isset($_POST['ID']) || !isset($_POST['post_title']) || !isset($_POST['post_type'])) {
    return false;
  }

  $postID = sanitize_key($_POST['ID']);

  //validate if an ID is numeric
  if (!click5_sitemap_validateID($postID)) {
    return false;
  }

  $postTitle = sanitize_text_field($_POST['post_title']);
  $postType = sanitize_text_field($_POST['post_type']);


  $new_item = array('ID' => $postID, 'post_title' => $postTitle, 'post_type' => $postType, 'url' => get_permalink($postID));
  $blacklistSeo = json_decode(get_option('click5_sitemap_seo_blacklisted_array'));

  if (!$blacklistSeo) {
    $blackblacklistSeolist = array();
  }

  $blacklistSeo[] = $new_item;

  update_option('click5_sitemap_seo_blacklisted_array', json_encode($blacklistSeo));

  return json_encode($new_item);
}

function click5_sitemap_API_get_seo_block_list (WP_REST_Request $request) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  $array = get_option('click5_sitemap_seo_blacklisted_array');
  return $array ? $array : '[]';
}

function click5_sitemap_API_get_seo_unblock ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  if (!isset($_GET['ID'])) {
    return false;
  }

  $getID = sanitize_key($_GET['ID']);

  //validate if an ID is numeric
  if (!click5_sitemap_validateID($getID)) {
    return false;
  }

  $blacklist = json_decode(get_option('click5_sitemap_seo_blacklisted_array'));

  $newBlacklist = array();

  foreach($blacklist as $blacklistItem) {
    if ($blacklistItem->ID !== $getID) {
      $newBlacklist[] = $blacklistItem;
    }
  }

  update_option('click5_sitemap_seo_blacklisted_array', json_encode($newBlacklist));

  return $newBlacklist;
}

function click5_sitemap_API_get_seo_clear ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  update_option('click5_sitemap_seo_blacklisted_array', json_encode(array()));
  return true;
}

function click5_sitemap_API_generate_manual ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  $postBody = (array)(json_decode(stripslashes(file_get_contents("php://input"))));
  $options = (array) $postBody['options'];
  foreach($options as $option) {
    $option = (array) $option;
    $optionName = sanitize_key($option['name']);
    $optionValue = sanitize_text_field($option['value']);
    update_option($optionName, $optionValue);
  }

  return click5_sitemap_generate_sitemap_XML_DoWork();
}

function click5_sitemap_API_print_robots_txt ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }
  clearstatcache();

  if (boolval(esc_attr(get_option('click5_sitemap_seo_robots_txt')))) {

    
    click5_sitemap_generate_robots_txt();

    return click5_sitemap_print_robots_txt(true);
  } else {
    if (file_exists(ABSPATH.'/robots.txt')) {

      if(boolval(esc_attr(get_option('click5_sitemap_seo_robots_backup'))) && file_exists(ABSPATH.'/robots-click5-backup.txt') ){
        unlink(ABSPATH.'/robots.txt');

        //Backup old robots.txt file
        if (file_exists(ABSPATH.'/robots-click5-backup.txt')) {
          rename(ABSPATH.'/robots-click5-backup.txt', ABSPATH.'/robots.txt');
        }

      } elseif (empty(get_option('click5_sitemap_seo_robots_backup'))){
        unlink(ABSPATH.'/robots.txt');
      }    
    }
  }
  
}

function click5_sitemap_API_print_sitemap_urls ( WP_REST_Request $request ) {

  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  click5_sitemap_generate_sitemap_XML_DoWork();

  clearstatcache();
  $resultArray = array();
  $sitemaps = glob(ABSPATH.'/*sitemap*.xml');

  if(boolval(esc_attr( get_option('click5_sitemap_seo_sitemap_xml')))){

    foreach( $sitemaps as $sitemap ) {
      if (strpos($sitemap, 'index.xml') !== false) {
        array_unshift($resultArray, site_url().'/'.str_replace('-index', '', basename($sitemap)));
      } else {
        $resultArray[] = site_url().'/'.basename($sitemap);
      }
    }

    return $resultArray;
    
  }
}

function click5_sitemap_API_add_custom_url ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  $new_item = (array)(json_decode(stripslashes(file_get_contents("php://input"))));

  $custom_urls_list = json_decode(get_option('click5_sitemap_urls_list'));

  if (!$custom_urls_list) {
    $custom_urls_list = array();
  }

  $id = 1;
  foreach($custom_urls_list as $custom_url) {
    $numberArr = explode('_', $custom_url->ID);

    if (intval($numberArr[1]) >= $id) {
      $id = intval($numberArr[1]) + 1;
    }
  }

  $new_item['title'] = sanitize_text_field($new_item['title']);
  $new_item['url'] = esc_url_raw($new_item['url']);
  $new_item['category']->use_custom = rest_sanitize_boolean($new_item['category']->use_custom);
  $new_item['category']->name = sanitize_text_field($new_item['category']->name);
  $new_item['new_tab'] = rest_sanitize_boolean($new_item['new_tab']);
  $new_item['last_mod'] = sanitize_text_field($new_item['last_mod']);

  /* TODO: add validation */

  if (empty($new_item['title'])) {
    return click5_sitemap_send_notification('Title field can not be empty!', 'warning');
  }

  if (empty($new_item['url'])) {
    return click5_sitemap_send_notification('URL field can not be empty', 'warning');
  }

  if ($new_item['category']->use_custom) {
    if (empty($new_item['category']->name)) {
      return click5_sitemap_send_notification('Category Name field can not be empty when using custom URL Category', 'warning');
    }
  }


  $new_item['ID'] = 'c_'.$id;
  $new_item['enabledHTML'] = true;
  $new_item['enabledXML'] = true;

  $custom_urls_list[] = $new_item;

  update_option('click5_sitemap_urls_list', json_encode($custom_urls_list));


  $getCustomCat = click5_sitemap_getCustomCategoriesCustomUrlsXML();

  foreach($getCustomCat as $custom_category) {
    add_option('click5_sitemap_seo_xml_'.str_replace(' ', '_', $custom_category).'_custom', true);
  }

  click5_sitemap_generate_sitemap_XML_DoWork();

  return true;
}

function click5_sitemap_API_get_custom_url_list ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  return json_decode(get_option('click5_sitemap_urls_list'));
}

function click5_sitemap_API_post_custom_url_clear ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  update_option('click5_sitemap_urls_list', json_encode($custom_urls_list));

  return true;
}

function click5_sitemap_API_get_custom_url_delete_one ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  if (!isset($_GET['ID'])) {
    return false;
  }

  $getID = sanitize_key($_GET['ID']);
  $idToRmvArray = explode('_', $getID);

  if(!click5_sitemap_validateID($idToRmvArray[1])) {
    return false;
  }

  $idToRmv = intval($idToRmvArray[1]);
  $custom_urls_list = (array) json_decode(get_option('click5_sitemap_urls_list'));

  $updated_urls_list = array();

  foreach($custom_urls_list as $custom_url) {
    $custom_url = (array)$custom_url;
    $custom_urlIDArray = explode('_', $custom_url['ID']);
    if (intval($custom_urlIDArray[1]) == $idToRmv) {
      continue;
    }

    $updated_urls_list[] = $custom_url;
  }

  update_option('click5_sitemap_urls_list', json_encode($updated_urls_list));

  click5_sitemap_generate_sitemap_XML_DoWork();

  return true;
}

function click5_sitemap_API_get_custom_url_toggle_HTML ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  if (!$request->get_params()['ID']) {
    return false;
  }

  if (!$request->get_params()['newVal']) {
    return false;
  }

  $idtoGetArray = explode('_', sanitize_key($request->get_params()['ID']));

  if (!click5_sitemap_validateID($idtoGetArray[1])) {
    return false;
  }

  $idToChange = intval($idtoGetArray[1]);

  // variable below doesn't need sanitization and validation since its casted to boolean.
  $newVal = $request->get_params()['newVal'] == 'true';

  $custom_urls_list = (array) json_decode(get_option('click5_sitemap_urls_list'));

  $updated_urls_list = array();

  foreach($custom_urls_list as $custom_url) {
    $custom_url = (array)$custom_url;
    //well value below is already stored in DB so no need to sanitize or validate
    $custom_urlIDArray = explode('_', $custom_url['ID']);
    if (intval($custom_urlIDArray[1]) == $idToChange) {
      $custom_url['enabledHTML'] = $newVal;
    }

    $updated_urls_list[] = $custom_url;
  }

  update_option('click5_sitemap_urls_list', json_encode($updated_urls_list));

  return true;
}

function click5_sitemap_API_get_custom_url_toggle_XML ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  if (!$request->get_params()['ID']) {
    return false;
  }

  if (!$request->get_params()['newVal']) {
    return false;
  }

  $idtoGetArray = explode('_', sanitize_key($request->get_params()['ID']));

  if (!click5_sitemap_validateID($idtoGetArray[1])) {
    return false;
  }

  $idToChange = intval($idtoGetArray[1]);
  $newVal = ($request->get_params()['newVal']) == 'true';

  $custom_urls_list = (array) json_decode(get_option('click5_sitemap_urls_list'));

  $updated_urls_list = array();

  foreach($custom_urls_list as $custom_url) {
    $custom_url = (array)$custom_url;
    $custom_urlIDArray = explode('_', $custom_url['ID']);
    if (intval($custom_urlIDArray[1]) == $idToChange) {
      $custom_url['enabledXML'] = $newVal;
    }

    $updated_urls_list[] = $custom_url;
  }

  update_option('click5_sitemap_urls_list', json_encode($updated_urls_list));

  return true;
}

function click5_sitemap_API_get_custom_url_single ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return click5_sitemap_send_notification('Failed to authenticate', 'error');
  }

  if (!$request->get_params()['ID']) {
    return false;
  }

  $idtoGetArray = explode('_', sanitize_key($request->get_params()['ID']));

  if(!click5_sitemap_validateID($idtoGetArray[1])) {
    return false;
  }

  $idToGet = intval($idtoGetArray[1]);
  $custom_urls_list = (array) json_decode(get_option('click5_sitemap_urls_list'));

  foreach($custom_urls_list as $custom_url) {
    $custom_urlIDArray = explode('_', $custom_url->ID);
    if (intval($custom_urlIDArray[1]) == $idToGet) {
      return $custom_url;
    }
  }

  return false;
}


function click5_sitemap_API_post_custom_url_save_edit ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  $edit_item = (array)(json_decode(stripslashes(file_get_contents("php://input"))));
  $idArray = explode('_', sanitize_key($request->get_params()['ID']));

  if (!click5_sitemap_validateID($idArray[1])) {
    return false;
  }

  /* TODO: finish */

  $idToUpdate = intval($idArray[1]);
  $custom_urls_list = (array) json_decode(get_option('click5_sitemap_urls_list'));
  $newArray = array();

  foreach($custom_urls_list as $custom_url) {
    $customUrlIDArray = explode('_', $custom_url->ID);

    if ($customUrlIDArray[1] == $idToUpdate) {
      $copy = (array)$custom_url;
      $custom_url = (array)$edit_item;

      $custom_url['title'] = sanitize_text_field($custom_url['title']);
      $custom_url['url'] = esc_url_raw($custom_url['url']);
      $custom_url['category']->use_custom = rest_sanitize_boolean($custom_url['category']->use_custom);
      $custom_url['category']->name = sanitize_text_field($custom_url['category']->name);
      $custom_url['new_tab'] = rest_sanitize_boolean($custom_url['new_tab']);
      $custom_url['last_mod'] = sanitize_text_field($custom_url['last_mod']);

      if (empty($custom_url['title'])) {
        return click5_sitemap_send_notification('Title field can not be empty!', 'warning');
      }

      if (empty($custom_url['url'])) {
        return click5_sitemap_send_notification('URL field can not be empty', 'warning');
      }

      if ($custom_url['category']->use_custom) {
        if (empty($custom_url['category']->name)) {
          return click5_sitemap_send_notification('Category Name field can not be empty when using custom URL Category', 'warning');
        }
      }

      $custom_url['enabledXML'] = $copy['enabledXML'];
      $custom_url['enabledHTML'] = $copy['enabledHTML'];
    }

    $newArray[] = $custom_url;
  }

  update_option('click5_sitemap_urls_list', json_encode($newArray));

  click5_sitemap_generate_sitemap_XML_DoWork();

  return true;
}

function click5_sitemap_order_list_recursive_children_setup(&$orderItems, $item) {
  if(isset($item->children)) {
        if (count($item->children)) {
          $orderValueChild = 100;
          foreach($item->children as $child) {
            $child = (object)$child;
            $child->ID = sanitize_key($child->ID);

            if (strpos($child->ID, 'c_') !== false) {
              // ID is from custom post
              if (!click5_sitemap_validateID(str_replace('c_', '', $child->ID))) {
                continue;
              }
            }
            else if (strpos($child->ID, 'g_') !== false) {
              //groups doesnt have to be validated
            }
            else {
              if (!click5_sitemap_validateID($child->ID)) {
                continue;
              }
            }

            $childItem = array(
              'ID' => $child->ID,
              'is_category' => false,
              'parent' => $item->ID,
              'order' => $orderValueChild
            );

            $orderItems[] = $childItem;
            $orderValueChild += 100;

            click5_sitemap_order_list_recursive_children_setup($orderItems, $child);
          }
        }
      }
}

function click5_sitemap_order_list_setup_order_values($newOrder) {
    $orderValue = 100;
    $orderItems = array();
    foreach($newOrder as $firstLevelOrder) {
      $firstLevelOrder = (object)$firstLevelOrder;
      $isCategory = ctype_digit($firstLevelOrder->ID) ? false : true;

      if ($isCategory) {
        $firstLevelOrder->ID = sanitize_text_field($firstLevelOrder->ID);

        if (empty($firstLevelOrder->ID)) {
          continue;
        }
        /* TODO: code function to validate category by existing categories */

      } else {
        $firstLevelOrder->ID = sanitize_key($firstLevelOrder->ID);

        if (strpos($firstLevelOrder->ID, 'c_') !== false) {
          // ID is from custom post

          if (!click5_sitemap_validateID(str_replace('c_', '', $firstLevelOrder->ID))) {
            continue;
          }
        } else if (strpos($firstLevelOrder->ID, 'g_') !== false) {
          //also skipping validation here for groups
        } else {
          if (!click5_sitemap_validateID($firstLevelOrder->ID)) {
            continue;
          }
        }
      }

      $orderItem = array(
        'ID' => $firstLevelOrder->ID,
        'is_category' => $isCategory,
        'order' => $orderValue
      );

      $orderItems[] = $orderItem;

      click5_sitemap_order_list_recursive_children_setup($orderItems, $firstLevelOrder);

      $orderValue += 100;//na wypadek gdyby powstaÅ‚y jakies strony ktore trzeba umiescic miedzy
    }

    return $orderItems;
}

function click5_sitemap_API_post_update_sitemap_order ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  $rawInput = file_get_contents("php://input");

  $newOrder = (array)(json_decode(html_entity_decode($rawInput)));

  $orderItems = click5_sitemap_order_list_setup_order_values($newOrder);

  update_option('click5_sitemap_order_list', json_encode($orderItems));

  return $orderItems;
}


function click5_sitemap_API_post_update_sitemap_order_save_btn ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  $rawInput = file_get_contents("php://input");
  $newOrder = (array)(json_decode(html_entity_decode($rawInput)));
  $orderItems = click5_sitemap_order_list_setup_order_values($newOrder);

  
	$new_array = array();

  foreach($orderItems as $value){ 
    //if($value['is_category'] == true){

      if(isset($value['parent'])){
        $parent = $value['parent'];
      } else {
        $parent = false;
      }

      $new_array[] = array(
        'ID' => $value['ID'],
        'order' => $value['order'],
        'parent' => $parent
      );
    //}
  }

  return update_option('click5_sitemap_order_list_old', json_encode($new_array));
}

function click5_sitemap_API_post_update_nested_elements ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  $rawInput = file_get_contents("php://input");
  $nestedElements = (array)(json_decode(html_entity_decode($rawInput)));

  return update_option('click5_sitemap_order_list_nested', json_encode($nestedElements));
}


function click5_sitemap_API_get_sitemap_order ( WP_REST_Request $request ) {
  return (array) json_decode(get_option('click5_sitemap_order_list'));
}

function click5_sitemap_API_post_reset_sitemap_order ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  //update_option('click5_sitemap_order_list', '');
  
  return click5_sitemap_HTML_sitemap_display_order_list();

}


function click5_sitemap_API_post_total_reset_sitemap_order ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  update_option('click5_sitemap_order_list', '');
  update_option('click5_sitemap_order_list_old', '');
  update_option('click5_sitemap_order_list_nested', '');

  return click5_sitemap_HTML_sitemap_display_order_list();

}


function click5_sitemap_API_update_option_AJAX ( WP_REST_Request $request ) {
  if (!click5_sitemap_requestAuthentication($request)) {
    return false;
  }

  $postBody = (array)(json_decode(stripslashes(file_get_contents("php://input"))));

  $type = sanitize_text_field($postBody['type']);
  $optionName = sanitize_text_field($postBody['option_name']);
  $optionValue = sanitize_text_field($postBody['option_value']);

  //validate if we are targeting current plugin option
  if (!(strpos($optionName, 'click5_sitemap') !== false)) {
    return false;
  }

  if ($type === 'bool')
    update_option($optionName, boolval($optionValue));
  else
    update_option($optionName, $optionValue);

  return true;
}



function click5_sitemap_debug (WP_REST_Request $request) {
  return intval(esc_attr( get_option('click5_sitemap_html_pagination_items_per_page') ));
}

function click5_ping_sitemap_to_google($url, $name) {

  $ping_url = '';
  $sitemap_url = $url;
  
  $ping_url = "http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($sitemap_url);
  $search_response = wp_remote_get( $ping_url );
  if($Search_response['response']['code']=200)
  {
    update_option( 'google_ping_' . $name, 'success ' . $url );
  }
  else 
  {
    update_option( 'google_ping_' . $name, 'error ' . $url );
  }
}


add_action( 'rest_api_init', function () {
  register_rest_route( 'click5_sitemap/API', '/request_pages', array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_API_request_pages',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route( 'click5_sitemap/API', '/get_blacklisted', array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_API_get_blacklisted',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route( 'click5_sitemap/API', '/add_to_blacklisted', array(
    'methods' => 'POST',
    'callback' => 'click5_sitemap_API_add_to_blacklisted',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route( 'click5_sitemap/API', '/clear_blacklist', array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_API_clear_blacklist',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/unblacklist',array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_API_unblacklist',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/generate_xml_sitemap',array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_API_generate_xml_sitemap',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/seo_block_page',array(
    'methods' => 'POST',
    'callback' => 'click5_sitemap_API_seo_block_page',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/get_seo_block_list',array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_API_get_seo_block_list',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/get_seo_unblock',array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_API_get_seo_unblock',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/get_seo_clear',array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_API_get_seo_clear',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/generate_manual',array(
    'methods' => 'POST',
    'callback' => 'click5_sitemap_API_generate_manual',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/print_robots_txt',array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_API_print_robots_txt',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/print_sitemap_urls',array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_API_print_sitemap_urls',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/add_custom_url',array(
    'methods' => 'POST',
    'callback' => 'click5_sitemap_API_add_custom_url',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/get_custom_url_list',array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_API_get_custom_url_list',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/post_custom_url_clear',array(
    'methods' => 'POST',
    'callback' => 'click5_sitemap_API_post_custom_url_clear',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/get_custom_url_delete_one',array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_API_get_custom_url_delete_one',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/get_custom_url_toggle_HTML',array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_API_get_custom_url_toggle_HTML',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/get_custom_url_toggle_XML',array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_API_get_custom_url_toggle_XML',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/get_custom_url_single',array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_API_get_custom_url_single',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/post_custom_url_save_edit',array(
    'methods' => 'POST',
    'callback' => 'click5_sitemap_API_post_custom_url_save_edit',
    'permission_callback' => '__return_true',
  ) );

  register_rest_route('click5_sitemap/API', '/get_order_list_HTML',array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_HTML_sitemap_display_order_list',
    'permission_callback' => '__return_true',
  ) );
  register_rest_route('click5_sitemap/API', '/post_update_list_HTML',array(
    'methods' => 'POST',
    'callback' => 'click5_sitemap_API_post_update_sitemap_order',
    'permission_callback' => '__return_true',
  ) );
  register_rest_route('click5_sitemap/API', '/post_update_list_HTML_save_btn',array(
    'methods' => 'POST',
    'callback' => 'click5_sitemap_API_post_update_sitemap_order_save_btn',
    'permission_callback' => '__return_true',
  ) );
  register_rest_route('click5_sitemap/API', '/post_update_nested_elements',array(
    'methods' => 'POST',
    'callback' => 'click5_sitemap_API_post_update_nested_elements',
    'permission_callback' => '__return_true',
  ) );
  register_rest_route('click5_sitemap/API', '/get_nested_elements',array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_HTML_get_nestedElements',
    'permission_callback' => '__return_true',
  ) );
  register_rest_route('click5_sitemap/API', '/get_sitemap_order',array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_API_get_sitemap_order',
    'permission_callback' => '__return_true',
  ) );
  register_rest_route('click5_sitemap/API', '/reset_sitemap_order',array(
    'methods' => 'POST',
    'callback' => 'click5_sitemap_API_post_reset_sitemap_order',
    'permission_callback' => '__return_true',
  ) );
  register_rest_route('click5_sitemap/API', '/total_reset_sitemap_order',array(
    'methods' => 'POST',
    'callback' => 'click5_sitemap_API_post_total_reset_sitemap_order',
    'permission_callback' => '__return_true',
  ) );
  register_rest_route('click5_sitemap/API', '/update_option_AJAX',array(
    'methods' => 'POST',
    'callback' => 'click5_sitemap_API_update_option_AJAX',
    'permission_callback' => '__return_true',
  ) );





  register_rest_route('click5_sitemap/API', '/debug',array(
    'methods' => 'GET',
    'callback' => 'click5_sitemap_debug',
    'permission_callback' => '__return_true',
  ) );
});



?>