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

function get_frame_size_string($frame_size, $is_prefix = true){
    $prefix = $is_prefix?', рама ':'';
    if (strlen($frame_size) == 3) {
        return $prefix.$frame_size.' мм';
    } elseif (strlen($frame_size) < 3 && ctype_digit($frame_size)) {
        return $prefix.$frame_size.'"';
    } elseif (!$frame_size) {
        return '';
    } else {
        return $prefix.$frame_size;
    }
}

function get_wheel_size_string($wheel_size){
    if (strlen($wheel_size) == 3) {
        return $wheel_size.' мм';
    } else {
        return $wheel_size.'"';
    }
}