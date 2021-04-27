<?php
/* Template Name: Compare */

global $post;

$compare_ids = (isset($_SESSION['compare'])) ? json_decode($_SESSION['compare']) : array();

if ($compare_ids) {
    $bikes = wc_get_products(array(
        'include' => $compare_ids
    ));

}

?>

<?php get_header(); ?>

    <div class="content-main article compare">
        <h1><?= $post->post_title; ?></h1>
        <div class="article-subheader"><?= $post->post_excerpt; ?></div>
        <div class="article-content">
            <div class="article-text">
                <div class="compare-wrapper">
                    <?php if (count($compare_ids)) : ?>
                        <div class="compare-desc">
                            <div class="compare-desc-item">Производитель <i class="las la-tag"></i></div>
                            <div class="compare-desc-item">Тип <i class="las la-biking"></i></div>
                            <div class="compare-desc-item">Размер колес <i class="las la-ruler-horizontal"></i></div>
                            <div class="compare-desc-item">Размер рамы <i class="las la-ruler-vertical"></i></div>
                            <div class="compare-desc-item">Материал <i class="las la-hammer"></i></div>
                            <div class="compare-desc-item">Скорости <i class="las la-tachometer-alt"></i></div>
                            <div class="compare-desc-item">Тормоз <i class="las la-shield-alt"></i></div>
                            <div class="compare-desc-item">Цвет <i class="las la-brush"></i></div>
                            <div class="compare-desc-item"></div>
                        </div>
                        <?php foreach ($bikes as $bike) : ?>
                        <div class="compare-item">
                            <div class="compare-img">
                                <img src="<?= get_image_link($bike) ?>" alt="">
                            </div>
                            <div class="compare-price">
                                <?= wc_price($bike->get_price()) ?>
                            </div>
                            <div class="compare-title">
                                <a href="<?= get_permalink($bike->get_id()) ?>" target="_blank"><?= $bike->get_name() ?></a>
                            </div>
                            <div class="compare-btn">
                                <?= add_cart_btn($bike, 'btn-blue') ?>
                            </div>
                            <div class="compare-value">
                                <?= $bike->get_attribute('brand') ?>
                            </div>
                            <div class="compare-value">
                                <?= $bike->get_attribute('type_velo') ?>
                            </div>
                            <div class="compare-value">
                                <?= $bike->get_attribute('wheel_size') ?>
                            </div>
                            <div class="compare-value">
                                <?= $bike->get_attribute('frame_size') ?>
                            </div>
                            <div class="compare-value">
                                <?= $bike->get_attribute('material') ?>
                            </div>
                            <div class="compare-value">
                                <?= $bike->get_attribute('speed') ?>
                            </div>
                            <div class="compare-value">
                                <?= $bike->get_attribute('tormoz') ?>
                            </div>
                            <div class="compare-value">
                                <?= $bike->get_attribute('color') ?>
                            </div>
                            <div class="compare-value compare-remove">
                                <a href='?compare_remove&id=".$bike->get_id()."'>убрать из списка</a>
                            </div>
                        </div>
                        <?php endforeach ?>
                    <?php else : ?>
                        <div class="empty">
                            <img src="<?= get_asset_path('images', 'empty_page.svg') ?>">
                            <div class="empty-head">Нет товаров для сравнения!</div>
                            <p>Вы не выбрали ни одного товара для сравенния</p>
                            <div class="empty-more-btn">
                                <a class="btn btn-blue" href="/bikes-catalog">Каталог</a>
                                <a class="btn btn-green" href="/">На главную</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="article-navbar">
                <?php expert_widget() ?>
            </div>
        </div>
    </div>

<?php get_footer(); ?>