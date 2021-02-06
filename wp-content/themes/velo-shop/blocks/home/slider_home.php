<?php

$ids_array = explode(', ', get_option('ids_slider'));
$products_slider = wc_get_products(array('include' => $ids_array));

?>
<div class="home-slider-wrapper" style="background-image: url(<?= get_asset_path('images', 'slider-home.jpg') ?>);">
    <div class="home-slider-nav home-slider-left" data-action="prev"><i class="las la-angle-left"></i></div>
    <div class="home-slider-nav home-slider-right" data-action="next"><i class="las la-angle-right"></i></div>
    <ul id="home-slider" class="owl-carousel owl-theme">
        <? foreach ($products_slider as $product_slider): ?>
            <?php
            $category = current(get_the_terms( $product_slider->get_id(), 'product_cat'));
            $price = $product_slider->get_price();
            $price_regular = $product_slider->get_variation_regular_price();
            $sale = round(100 - $price/$price_regular*100);
            $image_url = wp_get_attachment_url($product_slider->get_image_id());
            ?>
            <li>
                <div class="home-slider-desc">
                    <div class="home-slider-header"><?= $product_slider->get_name() ?><span>-<?= $sale ?>%</span></div>
                    <div class="home-slider-subheader"><?= $category->name ?></div>
                    <div class="home-slider-description"><?= $product_slider->get_meta('slider_text', true); ?></div>
                    <div class="home-slider-option">
                        <div class="home-slider-price-wrapper">
                            <div class="home-slider-newprice"><?= price_form($price) ?> руб</div>
                            <div class="home-slider-oldprice"><?= price_form($price_regular) ?> руб</div>
                        </div>
                        <div class="home-slider-button-wrapper">
                            <a href="#" class="btn btn-orange">Перейти к товару</a>
                        </div>
                    </div>
                </div>
                <div class="home-slider-image">
                    <img src="<?= $image_url ?>" alt="">
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>