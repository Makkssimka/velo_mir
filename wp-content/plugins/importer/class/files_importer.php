<?php


class FilesImporter
{
    private $offers_file;
    private $counter = 0;
    private $ids = array();
    private $old_ids = array();
    private $test_counter = 0;
    private $price_change_counter = 0;
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
        global $wpdb;
        $table_name = $wpdb->prefix."posts";

        // Получаем из базы продукты из списка
        $ids_string = implode("','", $this->ids);
        $old_product = $wpdb->get_results("SELECT * FROM $table_name WHERE `post_content_filtered` IN ('$ids_string')");
        $this->test_counter = count($old_product);

        // Проверяем совпадение цен
        foreach ($old_product as $item) {
            // Собираем список уже добавленных товаров
            $this->old_ids[] = $item->post_content_filtered;

            // Проверяем цены
            $product = wc_get_product($item->ID);
            $new_product = $this->list[$item->post_content_filtered];
            if ($product->get_price() != $new_product->getPrice()) {
                $this->price_change_counter++;
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

}
