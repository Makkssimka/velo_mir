<?php

//Добавляем в избранное
add_action('wp_ajax_add_favorites', 'add_favorites_callback');
add_action('wp_ajax_nopriv_add_favorites', 'add_favorites_callback');

function add_favorites_callback(){
    $status_list = array(
        'ADDED_IN_FAVORITES' => 1,
        'REMOVED_FROM_FAVORITES' => 0
    );
    $id = $_POST['id'];
    $session = isset($_SESSION['favorites'])?json_decode($_SESSION['favorites']):array();

    if (in_array($id, $session)) {
        $index = array_search($id, $session);
        array_splice($session, $index, 1);
        $status = $status_list['REMOVED_FROM_FAVORITES'];
    } else {
        array_push($session, $id);
        $status = $status_list['ADDED_IN_FAVORITES'];
    }

    $_SESSION['favorites'] = json_encode($session);
    echo json_encode(array(
        'status' => $status,
        'counter' => count($session)
    ));

    wp_die();
}

//Дабавляем в сравнение
add_action('wp_ajax_add_compare', 'add_compare_callback');
add_action('wp_ajax_nopriv_add_compare', 'add_compare_callback');

function add_compare_callback(){
    $status_list = array(
        'ADDED_IN_COMPARE' => 1,
        'REMOVED_FROM_COMPARE' => 0
    );
    $id = $_POST['id'];
    $session = isset($_SESSION['compare'])?json_decode($_SESSION['compare']):array();

    if (in_array($id, $session)) {
        $index = array_search($id, $session);
        array_splice($session, $index, 1);
        $status = $status_list['REMOVED_FROM_COMPARE'];
    } else {
        array_push($session, $id);
        $status = $status_list['ADDED_IN_COMPARE'];
    }

    $_SESSION['compare'] = json_encode($session);
    echo json_encode(array(
        'status' => $status,
        'counter' => count($session)
    ));

    wp_die();
}

//Получаем количество велосипедов для главной страницы
add_action('wp_ajax_get_products_count', 'get_products_count_callback');
add_action('wp_ajax_nopriv_get_products_count', 'get_products_count_callback');

function get_products_count_callback(){
    $query = $_POST['query'];

    //начало создания запроса
    $result = '';
    $args = array(
        'paginate' => false,
        'limit' => -1,
        'tax_query' => array(
            'relation' => 'AND'
        )
    );

    //перебираем все отправленные данные
    foreach($query as $item){
        //если не цена добавляем критерии, если цена добавляем фильтер по цене
        if ($item['type'] != 'price') {
            $filter = array(
                'taxonomy' => "pa_".$item['type'],
                'field' => 'slug',
                'terms' => $item['value']
            );
            array_push($args['tax_query'], $filter);
        } else {
            $args['price_rage'] = array(
                $item['value']['from'],
                $item['value']['to']
            );
        }
    }

    //получаем и отправляем количество продуктов
    echo(count(wc_get_products($args)));
    wp_die();
}


