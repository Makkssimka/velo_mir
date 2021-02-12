<?php

function myStartSession() {
    if (!session_id()) {
        ini_set('session.gc_maxlifetime', 60*60*24*7);
        ini_set('session.cookie_lifetime', 0);
        session_set_cookie_params(0);
        session_start();
    }
}

add_action('init', 'myStartSession', 1);

//Сброс сессий
function myResetSession() {
    if (isset($_GET['session_reset'])) {
        $url = get_current_url();
        unset($_SESSION['filter']);
        wp_redirect($url);
        die();
    }
}
add_action('init', 'myResetSession', 1);

//Установка сеcсий
function filterAddSession() {
    if (isset($_GET['session_filter'])) {
        myCleanSession();
        $data = get_url_data();
        $filter_array = array();
        foreach ($data as $data_key => $data_value) {
            $filter_array[$data_key] = $data_value;
        }
        $_SESSION['filter'] = json_encode($filter_array);
        $url = get_current_url();
        wp_redirect($url);
        die();
    }
}
add_action('init', 'filterAddSession', 1);

//Установка сесии сортировки
function sortAddSession(){
    if (isset($_GET['session_sort'])) {
        $sort = $_GET['session_sort'];
        $_SESSION['sort'] = $sort;

        $url = get_current_url();
        wp_redirect($url);
        die();
    }
}
add_action('init', 'sortAddSession', 1);

//Регистрация дополнительных запросов
require_once "wc_custom_query.php";

//Очитка сесии
function myCleanSession() {
    $session_data = $_SESSION;
    foreach ($session_data as $key=>$value) {
        if ($key == 'sort') continue;
        unset($_SESSION[$key]);
    }
}