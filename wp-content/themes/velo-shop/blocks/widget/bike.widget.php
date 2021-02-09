<?php

function bike_widget($bike) {
    $bike_colors = wc_get_product_terms( $bike->get_id(), 'pa_color');
    $bike_frame_sizes = wc_get_product_terms( $bike->get_id(), 'pa_frame_size');
    $bike_wheel_size = $bike->get_attribute('wheel_size');

    $image = $bike->get_available_variations()[0]['image']['url'];

    $favorites_array = isset($_SESSION['favorites'])?json_decode($_SESSION['favorites']):array();
    $is_favorites = in_array($bike->get_id(), $favorites_array)?'added-item':'';

    $compare_array = isset($_SESSION['compare'])?json_decode($_SESSION['compare']):array();
    $is_compare = in_array($bike->get_id(), $compare_array)?'added-item':'';

    if($bike->get_stock_status() == 'instock') {
        $have = '<div class="widget-bike-have widget-bike-have-item" > В наличии </div >';
    } else {
        $have = '<div class="widget-bike-have widget-bike-nohave-item" > Нет в наличии </div >';
    }

    $bike_colors_list = '<ul>';
    foreach ($bike_colors as $color) {
        $variant_color = explode('-', $color->description);
        if (count($variant_color) == 1) {
            $bike_colors_list .= '
            <li class="title-show" data-title="'.$color->name.'">
                <span style="background-color: '.$variant_color[0].';"></span>
            </li>
            ';
        } else {
            $bike_colors_list .= '
            <li class="title-show" data-title="'.$color->name.'">
                <span style="background-color:'.$variant_color[0].';"></span>
                <span class="semi-color" style="background-color:'.$variant_color[1].';"></span>
            </li>
            ';
        }
    }
    $bike_colors_list .= '</ul>';

    echo '
        <div class="widget-bike-item">
            <div class="widget-bike-have">'.$have.'</div>
            <div class="widget-bike-image-wrapper">
                <a href="'.get_permalink($bike->get_id()) .'">
                    <img src="'.$image.'">
                </a>
            </div>
            <div class="widget-bike-variation-list">
                <div class="widget-bike-color-variation">
                    '.$bike_colors_list.'
                </div>
            </div>
            <a href="'.get_permalink($bike->get_id()) .'" class="widget-bike-name">
                Велосипед <span>'.$bike->get_name().', колесо '.get_wheel_size_string($bike_wheel_size).'</span>
            </a>
            <div class="widget-bike-price">
                <div class="widget-bike-price-num">'.wc_price($bike->get_price()).'</div>
                <ul>
                    <li><a href="#" class="add-compare title-show '.$is_compare.'" data-title="в сравнение" data-id="'.$bike->get_id().'"><i class="las la-balance-scale-left"></i></a></li>
                    <li><a href="#" class="add-favorites title-show '.$is_favorites.'" data-title="в избранное" data-id="'.$bike->get_id().'"><i class="lar la-star"></i></a></li>
                </ul>
            </div>
            <div class="widget-bike-button">
                <a href="#" class="btn btn-blue">В корзину</a>
                <a href="'. get_permalink($bike->get_id()) .'" class="btn btn-green">Подробнее</a>
            </div>
        </div>
    ';
}