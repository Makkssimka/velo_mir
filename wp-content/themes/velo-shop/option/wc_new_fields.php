<?php

add_action( 'woocommerce_product_options_advanced', 'product_options_custom_fields' );

function product_options_custom_fields() {
    ?>

    <div class="options_group hide_if_external hide_if_grouped">
        <?php woocommerce_wp_textarea_input( array(
                   'id'            => 'slider_text', // Идентификатор поля
                   'label'         => 'Подпись для слайдера', // Заголовок поля
                   'placeholder'   => 'Ввод текста', // Надпись внутри поля
                   'class'         => 'short', // Произвольный класс поля
                   'name'          => 'slider_text', // Имя поля
                   'rows'          => '5', //Высота поля в строках текста.
        )); ?>
    </div>
    <div class="options_group hide_if_external hide_if_grouped">
        <?php woocommerce_wp_text_input( array(
            'id'            => 'youtube_link', // Идентификатор поля
            'label'         => 'Ссылка на обзор ютуб', // Заголовок поля
            'placeholder'   => 'https://www.youtube.com/embed/jDBZ5VULL4Q', // Надпись внутри поля
            'class'         => 'short', // Произвольный класс поля
            'name'          => 'youtube_link', // Имя поля
        )); ?>
    </div>
<?php }

add_action( 'woocommerce_process_product_meta', 'custom_fields_save', 10 );

function custom_fields_save( $post_id ) {
    $woocommerce_slider_text = $_POST['slider_text'];
    $woocommerce_youtube_link = $_POST['youtube_link'];
    if ( !empty($woocommerce_slider_text) ) {
        update_post_meta($post_id, 'slider_text', esc_attr($woocommerce_slider_text));
    }
    if ( !empty($woocommerce_youtube_link) ) {
        update_post_meta($post_id, 'youtube_link', esc_attr($woocommerce_youtube_link));
    }
}