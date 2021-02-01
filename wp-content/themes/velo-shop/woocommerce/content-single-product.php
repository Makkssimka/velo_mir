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

$variations = $product->get_available_variations();
$variation_size = isset($_GET['size'])?$_GET['size']:null;
$variation_color = isset($_GET['color'])?$_GET['color']:null;
$default_variable = null;

foreach ($variations as $var) {
    $var = new WC_Product_Variation($var['variation_id']);

    $attr = $var->get_variation_attributes();
    if ($attr['attribute_pa_size'] == $variation_size && $attr['attribute_pa_color'] == $variation_color) {
        $default_variable = $var;
    } elseif ($attr['attribute_pa_size'] == $variation_size && !$variation_color) {
        $default_variable = $var;
    }
}

if(!$default_variable){
    $default_variable = wc_get_product($variations[0]['variation_id']);
}

$favorites_array = isset($_SESSION['favorites'])?json_decode($_SESSION['favorites']):array();
$is_favorites = in_array($product->get_id(), $favorites_array)?'Добавлен в избранное':'В избранное';

$compare_array = isset($_SESSION['compare'])?json_decode($_SESSION['compare']):array();
$is_compare = in_array($product->get_id(), $compare_array)?'Добавлен к сравнению':'В сравнение';

?>

<div id="product-<?php the_ID(); ?>" class="content-main product">
    <h1>Велосипед <?= $default_variable->get_name()  ?></h1>
    <div class="product-articul">Артикул <?= $default_variable->get_sku() ?></div>
    <div class="product-heder">
        <?php require_once "blocks/product_gallery.php" ?>
        <div class="product-price">
            <div class="product-have">
                <?php if($product->get_stock_status() == 'instock'){ ?>
                <div class="product-have-item">В наличии</div>
                <?php } else {?>
                <div class="product-have-item">Нет в наличии</div>
                <?php } ?>
            </div>
            <div class="product-price-item">
                <div class="product-old-price"><?= wc_price($default_variable->get_regular_price()) ?></div>
                <div class="product-new-price"><?= wc_price($default_variable->get_price()) ?></div>
            </div>
            <?php require_once  "blocks/product_variation.php" ?>
            <div class="product-button">
                <a class="btn btn-add btn-green" href="#">В корзину</a>
                <div>
                    <a class="product-one-click" href="#">купить в один клик</a>
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
    <div class="product-desription">
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
