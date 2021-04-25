<?php

defined( 'ABSPATH' ) || exit;

global $product;
$attachment_ids = $product->get_gallery_image_ids();

if ($product->get_image_id()) {
    $default_image_link = wp_get_attachment_url($product->get_image_id());
} else {
    $default_image_link = get_asset_path("images", "noimage.jpg");
}

?>

<div class="product-gallery">
    <div class="product-gallery-slider">
        <ul id="product-carousel" class="owl-carousel owl-theme">
            <li>
                <a href="<?= $default_image_link ?>" data-fslightbox>
                    <img src="<?= $default_image_link ?>" alt="">
                </a>
            </li>
            <?php foreach ($attachment_ids as $attachment_id) : ?>
                <?php $image_link = wp_get_attachment_url( $attachment_id ); ?>
                <li>
                    <a href="<?= $image_link ?>" data-fslightbox>
                        <img src="<?= $image_link ?>" alt="">
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="product-gallery-thumbnail">
        <ul>
            <li class="active" data-numb="0">
                <a href="#">
                    <img src="<?= $default_image_link ?>" alt="">
                </a>
            </li>
            <?php foreach ($attachment_ids as $key => $attachment_id) : ?>
                <?php $image_link = wp_get_attachment_url( $attachment_id ); ?>
                <li data-numb="<?= $key+1 ?>">
                    <a href="#">
                        <img src="<?= $image_link ?>" alt="">
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>