<?php

$products = wc_get_products(array('posts_per_page' => -1));
global $wpdb;

?>
<?php get_header(); ?>

<div class="content-main home">

    <?php require_once "blocks/home/slider_home.php" ?>

    <div class="home-option">
        <ul>
            <li>
                <img src="<?= get_asset_path('images', 'delivery_icon.svg') ?>" alt="">
                <span>Бесплатная доставка от 10 000 руб</span>
            </li>
            <li>
                <img src="<?= get_asset_path('images', 'repair_icon.svg') ?>" alt="">
                <span>Гарантийное обслуживание в течении 3 месяцев</span>
            </li>
            <li>
                <img src="<?= get_asset_path('images', 'return_icon.svg') ?>" alt="">
                <span>Обмен и возврат в течение 30 дней</span>
            </li>
            <li>
                <img src="<?= get_asset_path('images', 'sale_icon.svg') ?>" alt="">
                <span>Скидки и бонусы постоянным клиентам</span>
            </li>
        </ul>
    </div>
    <?php require_once "blocks/home/bike_select.php" ?>
    <?php require_once "blocks/home/info-grey.php" ?>
    <?php require_once "blocks/home/popular_bike.php" ?>
    <?php require_once "blocks/home/info-blue.php" ?>
    <?php require_once "blocks/home/article_home.php" ?>
</div>

<?php get_footer(); ?>