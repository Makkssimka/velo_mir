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
        session_destroy();
        wp_redirect($url);
        die();
    }
}
add_action('init', 'myResetSession', 1);

//Установка сеcсий
function myAddSession() {
    if (isset($_GET['session_add'])) {
        myCleanSession();
        $data = get_url_data();
        foreach ($data as $data_key => $data_value) {
            $_SESSION[$data_key] = json_encode($data_value);
        }
        $url = get_current_url();
        wp_redirect($url);
        die();
    }
}

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

add_action('init', 'myAddSession', 1);