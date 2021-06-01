<?php


class FilesImporter
{
    private $offers_file;
    private $counter = 0;
    private $ids = array();
    private $old_ids = array();
    private $test_counter = 0;
    private $price_change_counter = 0;
    private $quantity_change_counter = 0;
    private $new_file_counter = 0;
    private $update_file_counter = 0;
    private $list = array();

    public function __construct()
    {
        $upload_path = IMPORTER_PLUGIN_PATH."upload/";
        $this->offers_file = $upload_path."offers0_1.xml";
    }

    public function get_product()
    {
        $offers = simplexml_load_file($this->offers_file);

        foreach ($offers->ПакетПредложений->Предложения->Предложение as $item) {
            $id = (string) $item->Ид;
            $name = (string) $item->Наименование;
            $price = (string) $item->Цены->Цена->ЦенаЗаЕдиницу;
            $quantity = (string) $item->Количество;

            // Для первичной загрузки товаров
            //if ($quantity <= 0 || $price <= 0) continue;

            $this->list[(string) $item->Ид] = new ProductImporter(
                $id,
                $name,
                $price,
                $quantity
            );

            $this->counter++;
            $this->ids[] = (string) $item->Ид;
        }
    }

    public function get_counter()
    {
        return $this->counter;
    }

    public function test_product()
    {
        // Получаем из базы продукты из списка
        $query_args = array(
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
        if ($this->get_new_file_counter() || $this->get_price_change_counter() || $this->get_quantity_change_counter()) {
            return true;
        } else {
            return false;
        }
    }

}
