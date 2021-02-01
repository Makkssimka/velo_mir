<?php

defined( 'ABSPATH' ) || exit;

global $product;
$attachment_ids = $product->get_gallery_image_ids();

$default_image_link = wp_get_attachment_url($default_variable->get_image_id());

?>

<div class="product-gallery">
    <div class="product-gallery-slider">
        <ul id="product-carousel" class="owl-carousel owl-theme">
            <li>
                <a href="<?= $default_image_link ?>" data-fslightbox>
                    <img src="<?= $default_image_link ?>" alt="">
                </a>
            </li>
            <?php foreach ($attachment_ids as $attachment_id){
                $image_link = wp_get_attachment_url( $attachment_id ); ?>
                <li>
                    <a href="<?= $image_link ?>" data-fslightbox>
                        <img src="<?= $image_link ?>" alt="">
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
    <div class="product-gallery-thumbnail">
        <ul>
            <li class="active" data-numb="0">
                <a href="#">
                    <img src="<?= $default_image_link ?>" alt="">
                </a>
            </li>
            <?php foreach ($attachment_ids as $key => $attachment_id){
                $image_link = wp_get_attachment_url( $attachment_id ); ?>
                <li data-numb="<?= $key+1 ?>">
                    <a href="#">
                        <img src="<?= $image_link ?>" alt="">
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>