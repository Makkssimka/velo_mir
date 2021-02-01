<?php

$variation_size = isset($_GET['size'])?$_GET['size']:null;
$variation_color = isset($_GET['color'])?$_GET['color']:null;
$color_variation = array();

$default_attr_color = $default_variable->get_variation_attributes()['attribute_pa_color'];
$default_attr_size = $default_variable->get_variation_attributes()['attribute_pa_size'];

foreach ($variations as $index => $var) {
    $var = new WC_Product_Variation($var['variation_id']);
    $attr = $var->get_variation_attributes();
    if ($attr['attribute_pa_size'] == $variation_size) {
        $color_variation[] = $attr['attribute_pa_color'];
    } elseif (!$variation_size && $attr['attribute_pa_size'] == $default_attr_size) {
        $color_variation[] = $attr['attribute_pa_color'];
    }
}

$attr_color = wc_get_product_terms( $product->get_id(), 'pa_color');
$attr_size = wc_get_product_terms( $product->get_id(), 'pa_size');

?>

<div class="product-size">
    <p>размер колес:</p>
    <ul>
        <?php foreach($attr_size as $value) : ?>
            <li class="<?= $value->slug == $default_attr_size?'active':'' ?>">
                <a href="<?= $product->get_permalink()."?size=".$value->slug ?>"><?= $value->name ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<div class="product-color">
    <p>цвет:</p>
    <ul>
        <?php foreach ($attr_color as $value) : ?>
            <?php if (in_array($value->slug, $color_variation)) : ?>
                <li class="<?= $value->slug == $default_attr_color?'active':'' ?>">
                    <?php $color_array = explode('-', $value->description);  ?>
                    <?php if (count($color_array) == 1) : ?>
                        <a class="title-show title-margin-30" data-title="<?= $value->name ?>" href="<?= $product->get_permalink()."?size=".$default_attr_size.'&color='.$value->slug ?>">
                            <span style="background-color:<?= $color_array[0] ?>;"></span>
                        </a>
                    <?php else : ?>
                        <a class="title-show" data-top="-20" data-title="<?= $value->name ?>" href="<?= $product->get_permalink()."?size=".$default_attr_size.'&color='.$value->slug ?>">
                            <span style="background-color:<?= $color_array[0] ?>;"></span>
                            <span class="product-two-color" style="background-color:<?= $color_array[1] ?>;"></span>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
