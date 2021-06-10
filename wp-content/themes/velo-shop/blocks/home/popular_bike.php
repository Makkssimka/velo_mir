<?php

$bikes_new = wc_get_products(array(
    'post_type'     => 'product',
    'post_status'   => 'publish',
    'orderby'       => 'post_date',
    'order'         => 'desc',
    'posts_per_page'      => 20
));

// обавить после первых продаж
//$popular_bike = wc_get_products(array(
//    'post_type'     => 'product',
//    'post_status'   => 'publish',
//    'orderby'       => 'total_sales',
//    'order'         => 'desc',
//    'posts_per_page'      => 10,
//));

?>

<div class="home-block">
    <h2>Новинки и <span>хиты продаж</span></h2>
    <p class="home-block-subheader">Лучшие предложения и новинки среди велосипедов. Ознакомьтесь с популярными товарами пользующихся наибольшим спросом среди наших покупателей.</p>
    <div class="home-list-product">
        <?php foreach ($bikes_new as $key => $bike): ?>
            <?php bike_widget($bike, false) ?>
            <?php if ($key == 2 || $key == 12) : ?>
                <?php catalog_banner_widget($key) ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <div class="home-block-button">
        <a href="/bikes-catalog?session_reset" class="btn btn-green">Весь каталог</a>
    </div>
</div>