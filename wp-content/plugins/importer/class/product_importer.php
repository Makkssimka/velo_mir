<?php


class ProductImporter
{
    private $id;
    private $name;
    private $price;
    private $quantity;

    public function __construct($id, $name, $price, $quantity)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function save()
    {
        $post = array(
            'post_author' => 1,
            'post_content' => $this->get_description(),
            'post_excerpt' => $this->get_post_excerpt(),
            'post_status' => "publish",
            'post_title' => $this->name, // Название товара
            'post_type' => "product",
            'post_content_filtered' => $this->id,
        );

        $post_id = wp_insert_post($post);
        wp_set_object_terms($post_id, "simple", 'product_type');

        update_post_meta( $post_id, '_regular_price', $this->price*1.2);
        update_post_meta( $post_id, '_price', $this->price);
        update_post_meta( $post_id, '_sale_price', $this->price);

        update_post_meta( $post_id, '_sku', SkuImporter::getGeneratedItemSku());
        update_post_meta( $post_id, '_stock_quantity',  $this->quantity);

        update_post_meta( $post_id, '_visibility', 'visible' );
        update_post_meta( $post_id, '_downloadable', 'no');
        update_post_meta( $post_id, '_virtual', 'no');

        update_post_meta( $post_id, '_product_attributes', $this->get_attribute_array());
    }

    public function update()
    {
        global $wpdb;
        $table_name = $wpdb->prefix."posts";

        $post = $wpdb->get_row("SELECT * FROM $table_name WHERE `post_content_filtered` = '$this->id'");
        $post_id = $post->ID;
        update_post_meta( $post_id, '_regular_price', $this->price*1.2);
        update_post_meta( $post_id, '_price', $this->price);
        update_post_meta( $post_id, '_sale_price', $this->price);

        update_post_meta( $post_id, '_stock_quantity',  $this->quantity);
    }

    private function get_attribute_array()
    {
        $attributes = array('brand', 'color', 'frame_size', 'material', 'speed', 'tormoz', 'type_velo', 'wheel_size');
        $attr_array = array();

        foreach ($attributes as $attribute) {
            $attr_array[$attribute] = array(
                'name' => 'pa_'.$attribute,
                'is_visible' => '1',
                'is_taxonomy' => '1'
            );
        }

        return $attr_array;
    }

    private function get_description()
    {
        return
            'Год выпуска - ;'.PHP_EOL.
            'Максимальный вес пользователя - ;'.PHP_EOL.
            'Вес, кг - ;'.PHP_EOL.
            'Страна производства - Россия;'.PHP_EOL.
            'Материал рамы - ;'.PHP_EOL.
            'Амортизация - ;'.PHP_EOL.
            'Складная конструкция - Нет;'.PHP_EOL.
            'Конструкция вилки - ;'.PHP_EOL.
            'Покрышки - ;'.PHP_EOL.
            'Тип переднего тормоза - ;'.PHP_EOL.
            'Тип заднего тормоза - ;'.PHP_EOL.
            'Регулировка седла - Да;'.PHP_EOL.
            'Регулировка руля - Да;'.PHP_EOL.
            'Материал руля - ';
    }

    private function get_post_excerpt()
    {
        return 'Велосипед, предназначенный для детей в возрасте от двух до четырех лет, без переключения передач. Технические особенности: стальная рама Hi-Ten, жесткая стальная вилка, одинарные алюминиевые обода, ножные педальные тормоза, защита цепи, съемные боковые колеса, багажник, длинные крылья, мягкая накладка на руле, звонок. Подходит для обучения и прогулочного катания в городских условиях.';
    }
}