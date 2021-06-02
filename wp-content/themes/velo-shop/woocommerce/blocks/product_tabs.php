<?php

// Получаем все характеристики продукта и исключаем без значения
$desc_array = array();
$description = explode(";", $product->get_description());
foreach ($description as $row) {
    $column = explode(" - ", $row);
    if (count($column) < 2 || !$column[1]) continue;
    $desc_array[] = array(
        'name' => $column[0],
        'value' => $column[1]
    );
}

// Выбираем все аттрибуты продукта
$attribute_array = array();
foreach( $product->get_attributes() as $taxonomy => $attribute){
    if (!isset($attribute->get_terms()[0]) || $attribute->get_taxonomy_object()->attribute_label == "1с Ид") continue;
    $value = $attribute->get_terms()[0]->name;
    $name = $attribute->get_taxonomy_object()->attribute_label;
    $attribute_array[] = array(
        'name' => $name,
        'value' => $value
    );
}

$table_array = array_merge($attribute_array, $desc_array);
?>

<div class="product-tabs">
    <div class="product-tabs-header">
        <ul>
            <li data-tab="1" class="active"><a href="#">
                    <i class="las la-list"></i> Характеристики
                </a></li>
            <li data-tab="2"><a href="#">
                    <i class="las la-file-alt"></i> Описание
                </a></li>
            <li data-tab="3"><a href="#">
                    <i class="las la-film"></i> Видеообзор
                </a></li>
        </ul>
    </div>
    <div class="product-tabs-body">
        <ul>
            <li data-tabcontent="1" class="active">
                <table class="product-tabs-table">
                    <tbody>
                        <?php foreach ($table_array as $desc_item): ?>
                        <tr>
                            <th><?= $desc_item['name'] ?>:</th>
                            <td><?= $desc_item['value'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </li>
            <li data-tabcontent="2">
                <?= $product->get_short_description(); ?>
            </li>
            <li data-tabcontent="3" class="product-tabs-video">
                <?php if ($product->get_meta('youtube_link', true)) : ?>
                <iframe src="<?= $product->get_meta('youtube_link', true); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                <?php else : ?>
                <p>Видео-обзор пока не добавлен на сайт</p>
                <?php endif ?>
            </li>
        </ul>
    </div>
</div>