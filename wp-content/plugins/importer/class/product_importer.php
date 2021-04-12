<?php


class ProductImporter
{
    private $id;
    private $name;
    private $sku;
    private $price;
    private $quantity;

    public function __construct($id, $name, $sku, $price, $quantity)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sku = $sku;
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

    public function save(){
        $post = array(
            'post_author' => 1,
            'post_content' => '',
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

        update_post_meta( $post_id, '_sku', 'undefined');
        update_post_meta( $post_id, '_stock_quantity',  $this->quantity);

        update_post_meta( $post_id, '_visibility', 'visible' );
        update_post_meta( $post_id, '_downloadable', 'no');
        update_post_meta( $post_id, '_virtual', 'no');
    }

    public function update(){
        global $wpdb;
        $table_name = $wpdb->prefix."posts";

        $post = $wpdb->get_row("SELECT * FROM $table_name WHERE `post_content_filtered` = '$this->id'");
        $post_id = $post->ID;
        update_post_meta( $post_id, '_regular_price', $this->price*1.2);
        update_post_meta( $post_id, '_price', $this->price);
        update_post_meta( $post_id, '_sale_price', $this->price);

        update_post_meta( $post_id, '_stock_quantity',  $this->quantity);
    }
}