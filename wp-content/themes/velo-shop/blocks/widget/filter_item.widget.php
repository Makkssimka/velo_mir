<?php

function filter_item_widget($slug, $title, $attribute, $open_label=false){
    $list_item = '';
    $open_block = '';
    $item_value_list = array();

    $filter_value = isset($_SESSION['filter']) ? json_decode($_SESSION['filter']) : '';

    if (property_exists($filter_value, $slug)) {
        $item_value_list = $filter_value->$slug;
    }

    foreach ($attribute as $value) {
        $slug_val = isset($value->slug)?$value->slug:$value['slug'];
        $name_val = isset($value->name)?$value->name:$value['name'];
        $count_val = isset($value->count)?' <span>('.$value->count.')</span>':'';

        if ($slug == 'wheel_size') {
            $name_val = get_wheel_size_string($name_val);
        } elseif ($slug == 'frame_size') {
            $name_val = get_frame_size_string($name_val, false);
        }

        $is_checked = in_array($slug_val, $item_value_list)?'checked':'';
        $list_item .= '
            <div>
                <input type="checkbox" name="'.$slug.'" id="'.$slug.$slug_val.'" '.$is_checked.' value="'.$slug_val.'">';
        $list_item .= '<label for="'.$slug.$slug_val.'">'.$name_val.$count_val.'</label>';
        $list_item .= '</div>';
    }

    if($open_label) {
        $open_block = '
            <div>
                <a class="open-list-filter" href="#"><span>Развернуть</span> <i class="las la-angle-down"></i></a>
            </div>';
    }

    echo '
    <div class="catalog-filter-block">
        <div class="catalog-filter-label">'.$title.'</div>
        <div class="catalog-filter-option">
           '.$list_item.'
        </div>
        '.$open_block.'
    </div>
    ';
}
