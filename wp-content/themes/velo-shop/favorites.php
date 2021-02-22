<?php
/* Template Name: Favorites */

global $post;

$favorites_ids = (isset($_SESSION['favorites'])) ? json_decode($_SESSION['favorites']) : array();

if ($favorites_ids) {
    $bikes = wc_get_products(array(
        'include' => $favorites_ids
    ));
}

?>

<?php get_header(); ?>

<div class="content-main article favorites">
    <h1><?= $post->post_title; ?></h1>
    <div class="article-subheader"><?= $post->post_excerpt; ?></div>
    <div class="article-content">
        <div class="article-text">
            <?php if (count($favorites_ids)) : ?>
                <div class="favorites-list">
                <?php foreach ($bikes as $bike) : ?>
                        <?= bike_widget($bike, true) ?>
                <?php endforeach ?>
                </div>
            <?php else : ?>
                <div class="favorites-empty">
                    <img src="<?= get_asset_path('images', 'not_found.svg') ?>" alt="">
                    <p>Вы еще не выбрали избранные товары</p>
                </div>
            <?php endif ?>
        </div>
        <div class="article-navbar">
            <?php expert_widget() ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
