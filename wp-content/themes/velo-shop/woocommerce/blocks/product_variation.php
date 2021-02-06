<?php

$sizes = wc_get_product_terms($product->get_id(), 'pa_frame_size');
$colors = wc_get_product_terms($product->get_id(), 'pa_color');

$size = $default_variable->get_attribute('frame_size');
$color = $default_variable->get_attribute('color');

// Размеры колес данной модели велосипеда
$products = wc_get_products(array(
    'title' => $product->get_title()
));

?>

<?php if (count($sizes)): ?>
    <div class="product-size">
        <p>размер рамы:</p>
        <ul>
            <?php foreach($sizes as $value) : ?>
                <li class="<?= $size == $value->name?'active':'' ?>">
                    <a href="<?= get_current_request(array('size' => $value->slug)) ?>">
                        <?= get_frame_size_string($value->name, false) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif ?>

<div class="product-color">
    <p>цвет:</p>
    <ul>
        <?php foreach ($colors as $value) : ?>
            <li class="<?= in_array($value->slug, $colors_variation)?'':'inactive-element' ?> <?= $color == $value->name?'active':'' ?>">
                <?php $color_array = explode('-', $value->description);  ?>
                <?php if (count($color_array) == 1) : ?>
                    <a class="title-show title-margin-30" data-title="<?= $value->name ?>"
                       href="<?= get_current_request($size?array('size' => $size, 'color' => $value->slug):array('color' => $value->slug)) ?>">
                        <span style="background-color:<?= $color_array[0] ?>;"></span>
                    </a>
                <?php else : ?>
                    <a class="title-show title-margin-30" data-title="<?= $value->name ?>"
                       href="<?= get_current_request($size?array('size' => $size, 'color' => $value->slug):array('color' => $value->slug)) ?>">>
                        <span style="background-color:<?= $color_array[0] ?>;"></span>
                        <span class="product-two-color" style="background-color:<?= $color_array[1] ?>;"></span>
                    </a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<div class="product-size">
    <p>Размер колес:</p>
    <ul>
        <?php foreach($products as $value) : ?>
            <li class="<?= $product->get_id() == $value->get_id()?'active':'' ?>">
                <a href="<?= get_permalink($value->get_id()) ?>">
                    <?= get_wheel_size_string($value->get_attribute('wheel_size')) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>


