<?php

function filter_item_widget($slug, $title, $attribute, $open_label=false){
    $list_item = '';
    $open_block = '';
    $item_value_list = array();

    if (isset($_SESSION[$slug])) {
        $item_value_list = json_decode($_SESSION[$slug]);
    }

    foreach ($attribute as $value) {
        $slug_val = isset($value->slug)?$value->slug:$value['slug'];
        $name_val = isset($value->name)?$value->name:$value['name'];
        $count_val = isset($value->count)?' ('.$value->count.')':'';

        $is_checked = in_array($slug_val, $item_value_list)?'checked':'';
        $list_item .= '
            <div>
                <input type="checkbox" name="'.$slug.'" id="'.$slug_val.'" '.$is_checked.'>
                <label for="'.$slug_val.'">'.$name_val.$count_val.'</label>
            </div>';
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
