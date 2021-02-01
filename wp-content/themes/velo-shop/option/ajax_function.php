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

