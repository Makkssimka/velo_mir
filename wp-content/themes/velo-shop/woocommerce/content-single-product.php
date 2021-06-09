<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}


$favorites_array = isset($_SESSION['favorites'])?json_decode($_SESSION['favorites']):array();
$is_favorites = in_array($product->get_id(), $favorites_array)?'Добавлен в избранное':'В избранное';

$compare_array = isset($_SESSION['compare'])?json_decode($_SESSION['compare']):array();
$is_compare = in_array($product->get_id(), $compare_array)?'Добавлен к сравнению':'В сравнение';

?>

<div id="product-<?php the_ID(); ?>" class="content-main product">
    <h1>Велосипед <?= $product->get_name() ?></h1>
    <div class="product-sku">Артикул <?= $product->get_sku() ?></div>
    <div class="product-header">
        <?php require_once "blocks/product_gallery.php" ?>
        <div class="product-price">
            <div class="product-item-price-wrapper">
                <div class="product-have">
                    <?php if($product->get_stock_status() == 'instock') : ?>
                        <div class="product-have-item">В наличии</div>
                    <?php else : ?>
                        <div class="product-have-item">Нет в наличии</div>
                    <?php endif; ?>
                </div>
                <div class="product-price-item">
                    <div class="product-old-price"><?= wc_price($product->get_regular_price()) ?></div>
                    <div class="product-new-price"><?= wc_price($product->get_price()) ?></div>
                </div>
            </div>
            <?php require_once  "blocks/product_variation.php" ?>
            <div class="product-button">
                <?= add_cart_btn($product) ?>
                <div>
                    <?= add_one_click_btn($product) ?>
                </div>
            </div>
            <div class="product-action">
                <ul>
                    <li><a class="add-compare" data-id="<?php the_ID(); ?>"href="#">
                            <i class="las la-balance-scale-left"></i>
                            <span class="result-text"><?= $is_compare ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="add-favorites" data-id="<?php the_ID(); ?>" href="#">
                            <i class="lar la-star"></i>
                            <span class="result-text"><?= $is_favorites ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="product-more-info">
                Возможна доставка и самовывоз. Оплата возможна наличным и безналичным расчетом. <a href="#">Подробнее...</a>
            </div>
        </div>
    </div>
    <?php require_once "blocks/product_option.php" ?>
    <div id="description" class="product-desription">
        <div class="product-decription-left">
            <?php require_once "blocks/product_tabs.php" ?>
        </div>
        <div class="product-decription-right">
            <?php expert_widget() ?>
        </div>
    </div>
    <div class="product-similar">
        <?php require_once "blocks/product_similar.php" ?>
    </div>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
