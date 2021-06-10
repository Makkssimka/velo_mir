<?php


class ProductImporter
{
    private $id;
    private $name;
    private $price;
    private $quantity;
    private $brand;
    private $type_velo;
    private $wheel_size;
    private $material;
    private $speed;
    private $tormoz;

    public function __construct($id, $name, $price, $quantity, $brand, $type_velo, $wheel_size, $material, $speed, $tormoz)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->brand = $brand;
        $this->type_velo = $type_velo;
        $this->wheel_size = $wheel_size;
        $this->material = $material;
        $this->speed = $speed;
        $this->tormoz = $tormoz;
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

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function save()
    {
        $product = new WC_Product();

        $product->set_name($this->name);
        $product->set_description($this->get_description());
        $product->set_short_description($this->get_post_excerpt());

        $product->set_catalog_visibility('visible');
        $product->set_virtual('no');
        $product->set_downloadable('no');

        $category = get_term_by('slug', $this->type_velo, 'product_cat');
        $product->set_category_ids([$category->term_id]);

        $product->set_sku(SkuImporter::getGeneratedItemSku());

        $product->set_manage_stock(true);
        $product->set_stock_quantity($this->quantity);

        $product->set_stock_status($this->quantity ? 'instock' : 'outofstock');
        $product->set_stock_status('instock');


        $product->set_regular_price($this->price*1.2);
        $product->set_sale_price($this->price);

        $product->set_attributes($this->get_attributes_array());

        $product->save();
    }

    public function update()
    {
        // Получаем из базы продукты из списка
        $query_args = array(
            'tax_query' => array(
                array(
                    'taxonomy' => 'pa_1c-id',
                    'field'    => 'slug',
                    'terms'    => $this->id
                )
            )
        );

        $products = wc_get_products($query_args);
        $product = array_shift($products);

        $product->set_regular_price($this->price*1.2);
        $product->set_sale_price($this->price);
        $product->set_stock_quantity($this->quantity);

        $product->set_attributes($this->get_attributes_array());

        $product->save();
    }

    private function get_attributes_array()
    {
        $attributes = array(
            'brand' => $this->brand,
            'material' => $this->material,
            'speed' => $this->speed,
            'tormoz' => $this->tormoz,
            'type_velo' => $this->type_velo,
            'wheel_size' => $this->wheel_size,
            'color' => false,
            'frame_size' => isset($this->frame_size) ? $this->frame_size : false,
            '1c-id' => '1c'
        );

        $attr_array = array();

        foreach ($attributes as $key => $value) {
            $attribute_id = wc_attribute_taxonomy_id_by_name($key);
            $attribute = new WC_Product_Attribute();
            $attribute->set_id($attribute_id);
            $attribute->set_name( 'pa_'.$key );
            $attribute->set_visible( true );
            $attribute->set_variation( false );

            if ($value == '1c') {
                $attribute->set_options([$this->id]);
            } else if ($value) {
                $term = get_term_by('slug', $value, 'pa_'.$key);
                $attribute->set_options([$term->name]);
            } else {
                $attribute->set_options([]);
            }

            $attr_array[] = $attribute;
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
            'Материал руля - ;';
    }

    private function get_post_excerpt()
    {
        return 'Велосипед, предназначенный для детей в возрасте от двух до четырех лет, без переключения передач. Технические особенности: стальная рама Hi-Ten, жесткая стальная вилка, одинарные алюминиевые обода, ножные педальные тормоза, защита цепи, съемные боковые колеса, багажник, длинные крылья, мягкая накладка на руле, звонок. Подходит для обучения и прогулочного катания в городских условиях.';
    }
}