<?php
global  $post;
global $product;

$categories = get_the_category($post->id);
$product_category_link = "";

if ($product) {
    $product_category = get_term($product->get_category_ids()[0], 'product_cat');
    $product_category_link = '<li><i class="las la-angle-right"></i></li><li><a href="/bikes-catalog?session_filter&type_velo='.$product_category->slug.'">'.$product_category->name.'</a></li>';
}

if (!is_front_page()): ?>
<div class="breadcrumbs">
    <ul>
        <li><a href="/">Главная</a></li>
        <?php foreach ($categories as $category): ?>
            <li><i class="las la-angle-right"></i></li>
            <li class="breadcrumbs-inactive"><?= $category->name; ?></li>
        <?php endforeach; ?>
        <?= $product_category_link ?>
        <li><i class="las la-angle-right"></i></li>
        <li class="breadcrumbs-inactive"><?= $post->post_title; ?></li>
    </ul>
</div>
<?php endif; ?>