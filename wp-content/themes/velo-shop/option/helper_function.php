<?php

function time_to_array($time) {
    $result_array = array();
    $first_array = explode('?', $time);
    foreach ($first_array as $item){
        $item_array = explode('/', $item);
        $result_array[] = [
            'label' => $item_array[0],
            'time'  => $item_array[1]
        ];
    }
    return $result_array;
}

function get_asset_path($folder, $file){
    return get_template_directory_uri()."/assets/$folder/$file";
}

function get_current_url(){
    $url = $_SERVER['REQUEST_URI'];
    $url = explode('?', $url);
    return $url[0];
}

function get_current_request($request){
    $url = $_SERVER['REQUEST_URI'];
    $url = explode('?', $url);
    $url = $url[0];

    return $url."?".http_build_query($request);
}

function get_url_data(){
    $query = $_SERVER['QUERY_STRING'];
    $query_array = explode('&', $query);
    $result_array = array();
    foreach ($query_array as $index => $value_query) {
        if ($index == 0) continue;
        $value_query_array = explode('=', $value_query);
        $result_array[$value_query_array[0]] = explode(',', $value_query_array[1]);
    }
    return $result_array;
}

function price_form($price){
    return number_format($price, 0, "", " ");
}

function get_max_and_min_price(){
    global $wpdb;

    $min_price = (int)$wpdb->get_var("SELECT MIN(min_price) FROM velo_wc_product_meta_lookup WHERE min_price > 0");
    $max_price = (int)$wpdb->get_var("SELECT MAX(min_price) FROM velo_wc_product_meta_lookup WHERE min_price > 0");

    return array(
        'min' => $min_price,
        'max' => $max_price,
    );
}

function terms_sort($array, $sort_by = "name")
{
    if ($sort_by == "name") {
        usort($array, function ($a, $b) {
            return $a->name > $b->name ? 1 : -1;
        });
    } else {
        usort($array, function ($a, $b) {
            return $a->count < $b->count ? 1 : -1;
        });
    }



    return $array;
}

function send_telegram($order_number){
    $token = '1655959307:AAGzDwGYkWVkBGs9-2J_fAr6Q__-IrrUbGM';
    $ids_user = explode(',', get_option('telegram_ids'));
    $message = "<b>Новый заказ:</b> №".$order_number;
    $mode = "html";

    foreach ($ids_user as $id_user) {
        $id_user = trim($id_user);
        file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$id_user&text=$message&parse_mode=$mode");
    }
}

function get_color_link($product_id){
    $colors = wc_get_product_terms($product_id, "pa_color" );

    if (!$colors) return '';

    if (count($colors) == 2) {
        $color_name = mb_strtolower($colors[0]->name."-".$colors[1]->name);
        return '<a class="title-show title-margin-30" data-title="'.$color_name.'" href="'.get_permalink($product_id).'">
                   <span style="background-color:'.$colors[0]->description.';"></span>
                   <span class="product-two-color" style="background-color:'.$colors[1]->description.';"></span>
                </a>';
    } else {
        $color_name = mb_strtolower($colors[0]->name);
        return '<a class="title-show title-margin-30" data-title="'.$color_name.'" href="'.get_permalink($product_id).'">
                   <span style="background-color:'.$colors[0]->description.';"></span>
                </a>';
    }
}

function get_image_link($product){
    if ($product->get_image_id()) {
        $default_image_link = wp_get_attachment_url($product->get_image_id());
    } else {
        $default_image_link = get_asset_path("images", "noimage.jpg");
    }

    return $default_image_link;
}