<?php


class FilesImporter
{
    private $offers;
    private $imports;
    private $counter = 0;
    private $ids = array();
    private $old_ids = array();
    private $test_counter = 0;
    private $price_change_counter = 0;
    private $quantity_change_counter = 0;
    private $new_file_counter = 0;
    private $update_file_counter = 0;
    private $list = array();
    private $price_list = array();
    private $categories_list = array();
    private $attr_name_list = array();
    private $attr_value_list = array();
    private $products_list = array();

    public function __construct()
    {
        $upload_path = IMPORTER_PLUGIN_PATH."upload/";
        $this->offers = simplexml_load_file($upload_path."offers0_1.xml");
        $this->imports = simplexml_load_file($upload_path."import0_1.xml");
    }

    public function get_product()
    {
        $this->generate_products_list();

        foreach ($this->products_list as $key => $product) {

            // Для первичной загрузки товаров
            if ($product['price'] <= 0 || $product['quantity'] <= 0) continue;

            $id = $key;
            $name = $product['name'];
            $price = $product['price'];
            $quantity = $product['quantity'];
            $brand = $product['brand'];
            $type_velo = isset($product['type_velo']) ? $product['type_velo'] : 'горные';
            $wheel_size = isset($product['wheel_size']) ? $product['wheel_size'] : '24';
            $material = isset($product['material']) ? $product['material'] : 'сталь';
            $speed = isset($product['speed']) ? $product['speed'] : '1';
            $tormoz = isset($product['tormoz']) ? $product['tormoz'] : 'барабанные';
            $frame_size = isset($product['frame_size']) ? $product['frame_size'] : '';

            $this->list[$id] = new ProductImporter(
                $id,
                $name,
                $price,
                $quantity,
                $brand,
                $type_velo,
                $wheel_size,
                $material,
                $speed,
                $tormoz,
                $frame_size
            );

            $this->counter++;
            $this->ids[] = $id;
        }
    }

    public function generate_price_list()
    {
        foreach ($this->offers->ПакетПредложений->Предложения->Предложение as $product) {
            $id = (string) $product->Ид;
            $price = (string) $product->Цены->Цена->ЦенаЗаЕдиницу;
            $quantity = (string) $product->Количество;

            $this->price_list[$id] = [
                'price' => $price,
                'quantity' => $quantity
            ];
        }
    }

    public function generate_categories_list()
    {
        foreach ($this->imports->Классификатор->Группы->Группа->Группы->Группа as $category) {
            $brand = explode(' ', $category->Наименование, 2);
            $brand = strtolower($brand[1]);
            $brand = str_replace(' ', '_', $brand);

            foreach ($category->Группы->Группа as $type_velo) {
                $id = (string) $type_velo->Ид;
                $type_velo = explode(' ', $type_velo->Наименование);
                $type_velo = mb_strtolower($type_velo[1]);

                $this->categories_list[$id] = [
                    'brand' => $brand,
                    'type_velo' => $type_velo
                ];
            }
        }
    }

    public function generate_attributes_list()
    {
        foreach ($this->imports->Классификатор->Свойства->Свойство as $attribute) {
            $id_name = (string) $attribute->Ид;
            $val_name = (string) $attribute->Наименование;

            switch ($val_name) {
                case 'Диаметр колес' :
                    $val_name = 'wheel_size';
                    break;
                case 'Количество скоростей' :
                    $val_name = 'speed';
                    break;
                case 'Тормоза' :
                    $val_name = 'tormoz';
                    break;
                case 'Материал рамы' :
                    $val_name = 'material';
                    break;
                case 'Размер рамы' :
                    $val_name = 'frame_size';
                    break;
            }

            $this->attr_name_list[$id_name] = $val_name;

            if (!$attribute->ВариантыЗначений) continue;

            foreach ($attribute->ВариантыЗначений->Справочник as $value) {
                $id_value = (string) $value->ИдЗначения;
                $val_value = (string) $value->Значение;
                $val_value = str_replace('.', '-', mb_strtolower($val_value));
                $val_value = str_replace(' ', '-', $val_value);
                $this->attr_value_list[$id_value] = $val_value;
                $this->attr_value_list[$id_value] = $val_value;
            }
        }
    }

    public function generate_products_list()
    {
        $this->generate_price_list();
        $this->generate_categories_list();
        $this->generate_attributes_list();

        foreach ($this->imports->Каталог->Товары->Товар as $product) {
            $id = (string) $product->Ид;
            $name = (string) $product->Наименование;
            $group_id = (string) $product->Группы->Ид;

            $this->products_list[$id] = [
                'name' => $name,
                'price' => $this->price_list[$id]['price'],
                'quantity' => $this->price_list[$id]['quantity'],
                'brand' => $this->categories_list[$group_id]['brand'],
                'type_velo' => $this->categories_list[$group_id]['type_velo'],
            ];

            if (!$product->ЗначенияСвойств) continue;

            foreach ($product->ЗначенияСвойств->ЗначенияСвойства as $attr) {
                $id_attr = (string) $attr->Ид;
                $id_attr_val = (string) $attr->Значение;
                $this->products_list[$id][$this->attr_name_list[$id_attr]] = $this->attr_value_list[$id_attr_val];
            }
        }
    }

    public function test_product()
    {
        // Получаем из базы продукты из списка
        $query_args = array(
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'pa_1c-id',
                    'field'    => 'slug',
                    'terms'    => $this->ids,
                    'compare' => 'IN'
                )
            )
        );

        $old_product = wc_get_products($query_args);
        $this->test_counter = count($old_product);

        // Проверяем совпадение цен и количество товаров
        foreach ($old_product as $item) {
            // Собираем список уже добавленных товаров
            $this->old_ids[] = $item->get_attribute('1c-id');

            $new_product = $this->list[$item->get_attribute('1c-id')];

            // Проверяем цены
            if ($item->get_price() != $new_product->getPrice()) {
                $this->price_change_counter++;
            }

            // Проверяем количество
            if ($item->get_stock_quantity() != $new_product->getQuantity()) {
                $this->quantity_change_counter++;
            }
        }
    }

    public function add_new_product()
    {
        set_time_limit(2400);

        foreach ($this->list as $item) {

            if (in_array($item->getId(), $this->old_ids)) {
                $item->update();
                $this->update_file_counter++;
            } else {
                $item->save();
                $this->new_file_counter++;
            };
        }
    }

    public function get_counter()
    {
        return $this->counter;
    }

    public function get_test_counter()
    {
        return $this->test_counter;
    }

    public function get_price_change_counter()
    {
        return $this->price_change_counter;
    }

    public function get_new_file_counter()
    {
        return $this->new_file_counter;
    }

    public function get_update_file_counter()
    {
        return $this->update_file_counter;
    }

    public function get_quantity_change_counter()
    {
        return $this->quantity_change_counter;
    }

    public function get_new_counter()
    {
        return $this->counter - $this->test_counter;
    }

    public function is_update()
    {
        if ($this->get_new_counter() || $this->get_price_change_counter() || $this->get_quantity_change_counter()) {
            return true;
        } else {
            return false;
        }
    }

}
