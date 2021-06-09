<?php

$products = wc_get_products(array('posts_per_page' => -1));
global $wpdb;

?>
<?php get_header(); ?>

<div class="content-main home">

    <?php require_once "blocks/home/slider_home.php" ?>
    <?php require_once "blocks/home/bike_select.php" ?>
    <?php require_once "blocks/home/info-grey.php" ?>
    <?php require_once "blocks/home/popular_bike.php" ?>
    <?php require_once "blocks/home/info-blue.php" ?>
    <?php require_once "blocks/home/article_home.php" ?>
</div>

<?php get_footer(); ?>