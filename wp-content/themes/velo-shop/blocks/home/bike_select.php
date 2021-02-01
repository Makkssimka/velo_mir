<?php

$brands = get_terms('pa_brand');
$types = get_terms('pa_type_velo');
$colors = get_terms('pa_color');
$sizes = get_terms('pa_size');
$amorts = get_terms('pa_amort');
$tormozs = get_terms('pa_tormaoz');

$min_price = (int)$wpdb->get_var("SELECT MIN(min_price) FROM velo_wc_product_meta_lookup WHERE min_price > 0");
$max_price = (int)$wpdb->get_var("SELECT MAX(min_price) FROM velo_wc_product_meta_lookup WHERE min_price > 0");

$products_count = count($products);

?>
<div class="home-bike-select">
    <div class="home-bike-select-wrapper">
        <h2>Какой велосипед <span>вам подходит?</span></h2>
        <div class="flex-wrapper">
            <div class="small-flex">
                <div class="select-input">
                    <div class="select-input-value">
                        <span></span>
                        <i class="las la-angle-down"></i>
                    </div>
                    <div class="select-input-option">
                        <div class="select-option-item option-item-select" data-value="">Бренд</div>
                        <?php foreach ($brands as $brand): ?>
                        <div class="select-option-item" data-value="<?= $brand->slug ?>"><?= $brand->name ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="small-flex">
                <div class="select-input">
                    <div class="select-input-value">
                        <span></span>
                        <i class="las la-angle-down"></i>
                    </div>
                    <div class="select-input-option">
                        <div class="select-option-item option-item-select" data-value="">Тип велосипеда</div>
                        <?php foreach ($types as $type): ?>
                        <div class="select-option-item" data-value="<?= $type->slug ?>"><?= $type->name ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="small-flex">
                <div class="select-input">
                    <div class="select-input-value">
                        <span></span>
                        <i class="las la-angle-down"></i>
                    </div>
                    <div class="select-input-option">
                        <div class="select-option-item option-item-select" data-value="">Цвет</div>
                        <?php foreach ($colors as $color): ?>
                        <div class="select-option-item" data-value="<?= $color->slug ?>"><?= $color->name ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-wrapper">
            <div class="small-flex">
                <div class="select-input">
                    <div class="select-input-value">
                        <span></span>
                        <i class="las la-angle-down"></i>
                    </div>
                    <div class="select-input-option">
                        <div class="select-option-item option-item-select" data-value="">Размер колес</div>
                        <?php foreach ($sizes as $size): ?>
                            <div class="select-option-item" data-value="<?= $size->slug ?>"><?= $size->name ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="small-flex">
                <div class="select-input">
                    <div class="select-input-value">
                        <span></span>
                        <i class="las la-angle-down"></i>
                    </div>
                    <div class="select-input-option">
                        <div class="select-option-item option-item-select" data-value="">Амортизаторы</div>
                        <?php foreach ($amorts as $amort): ?>
                            <div class="select-option-item" data-value="<?= $amort->slug ?>"><?= $amort->name ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="small-flex">
                <div class="select-input">
                    <div class="select-input-value">
                        <span></span>
                        <i class="las la-angle-down"></i>
                    </div>
                    <div class="select-input-option">
                        <div class="select-option-item option-item-select" data-value="">Тип тормаза</div>
                        <?php foreach ($tormozs as $tormoz): ?>
                            <div class="select-option-item" data-value="<?= $tormoz->slug ?>"><?= $tormoz->name ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="home-range-wrapper">
            <input type="text" id="homeRange" class="js-range-slider" data-min="<?= $min_price ?>" data-max="<?= $max_price ?>" data-from="<?= $min_price ?>" data-to="<?= $max_price ?>"/>
        </div>
        <div class="home-bike-select-btn">
            <a class="btn btn-blue" href="#">Показать <?= $products_count ?></a>
        </div>
        <div class="home-bike-select-desc">
            <p>или</p>
            <p>оставьте заявку и наши <span>специалисты проконсультируют вас</span></p>
        </div>
        <div class="home-bike-select-btn">
            <a class="btn btn-green open-modal" href="#">Оставить заявку</a>
        </div>
    </div>
</div>