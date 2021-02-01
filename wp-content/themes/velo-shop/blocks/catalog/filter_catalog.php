<?php

$options = array('hide_empty' => false);
$brands = get_terms('pa_brand', $options);
$types = get_terms('pa_type_velo', $options);
$tormozs = get_terms('pa_tormoz', $options);

$have = array(
    array(
        'slug' => 'have',
        'name' => 'В наличие'
    ),
    array(
        'slug' => 'nohave',
        'name' => 'Под заказ'
    )
);

?>
<div class="catalog-filter-wrapper">
    <div class="catalog-filter-header">
        <span>Фильтры</span> <a href="?session_reset"><i class="las la-times"></i> сбросить</a>
    </div>
    <div class="catalog-filter-body">
        <?php filter_item_widget('have', 'Наличие', $have) ?>
        <?php require "filter_rage_price.php" ?>
        <div class="catalog-filter-block">
            <div class="catalog-filter-label">Диаметр колес</div>
            <div class="catalog-filter-option">
                <div>
                    <input type="checkbox" name="diametr" id="10">
                    <label for="10">10"</label>
                </div>
                <div>
                    <input type="checkbox" name="diametr" id="12">
                    <label for="12">12"</label>
                </div>
                <div>
                    <input type="checkbox" name="diametr" id="14">
                    <label for="14">14"</label>
                </div>
                <div>
                    <input type="checkbox" name="diametr" id="16">
                    <label for="16">16"</label>
                </div>
                <div>
                    <input type="checkbox" name="diametr" id="18">
                    <label for="18">18"</label>
                </div>
                <div>
                    <input type="checkbox" name="diametr" id="20">
                    <label for="20">20"</label>
                </div>
                <div>
                    <input type="checkbox" name="diametr" id="24">
                    <label for="24">24"</label>
                </div>
                <div>
                    <input type="checkbox" name="diametr" id="26">
                    <label for="26">26"</label>
                </div>
                <div>
                    <input type="checkbox" name="diametr" id="27.5">
                    <label for="27.5">27.5"</label>
                </div>
                <div>
                    <input type="checkbox" name="diametr" id="28">
                    <label for="28">28"</label>
                </div>
                <div>
                    <input type="checkbox" name="diametr" id="29">
                    <label for="29">29"</label>
                </div>
            </div>
            <div>
                <a class="open-list-filter" href="#"><span>Развернуть</span> <i class="las la-angle-down"></i></a>
            </div>
        </div>
        <?php filter_item_widget('type_velo', 'Назначение', $types, true) ?>
        <?php filter_item_widget('brand','Производители', $brands, true) ?>
        <?php filter_item_widget('tormoz','Тип тормоза', $tormozs) ?>
    </div>
    <div class="catalog-filter-footer">
        <a id="filter_submit" href="#>"><i class="las la-filter"></i> применить</a>
    </div>
</div>