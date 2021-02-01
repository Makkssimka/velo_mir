<?php
/* Template Name: Catalog */

get_header();

$products_per_page = 20;
$offset_page = isset($_GET['page_count'])?$_GET['page_count']:1;
$args = array(
    'paginate' => true,
    'limit' => $products_per_page,
    'offset' => $offset_page-1,
    'order_by' => 'post_date',
    'order' => 'DESC',
    'tax_query' => array(
        'relation' => 'AND'
    )
);

// Получаем значения фильтра из сессий
$price = isset($_SESSION['price']) ? json_decode($_SESSION['price']) : '';
if ($price) {
    $args['price_rage'] = $price;
}

$sort = isset($_SESSION['sort']) ? json_decode($_SESSION['sort'])[0] : '';

switch ($sort) {
    case 'new':
        $args['orderby'] = 'post_date';
        break;
    case 'low':
        $args['orderby'] = 'meta_value_num';
        $args['meta_key'] = '_price';
        $args['order'] = 'ASC';
        break;
    case 'costly':
        $args['orderby'] = 'meta_value_num';
        $args['meta_key'] = '_price';
        $args['order'] = 'DESC';
        break;
    case 'popular':
        $args['orderby'] = 'meta_value_num';
        $args['meta_key'] = 'total_sales';
        $args['order'] = 'DESC';
        break;
    default:
        $args['orderby'] = 'post_date';
}

foreach ($_SESSION as $key => $value) {
    if ($key == 'sort' || $key == 'price' || $key == 'have' || $key == 'favorites' || $key == 'compare') continue;
    $query = array(
        'taxonomy' => "pa_$key",
        'field' => 'slug',
        'terms' => json_decode($value)
    );
    array_push($args['tax_query'], $query);
}

$products_obj = wc_get_products($args);
$products = $products_obj->products;

?>

<div class="content-main catalog">
    <?php require_once "blocks/catalog/filter_catalog.php" ?>
    <div class="catalog-list-wrapper">
        <div class="catalog-list-header">
            <h1>Каталог велосипедов</h1>
            <?php require_once "blocks/catalog/select_sort.php" ?>
        </div>
        <div class="catalog-list-body">
           <?php foreach ($products as $key => $bike): ?>
               <?php bike_widget($bike, true) ?>
               <?php if ($key == 3 || $key == 13): ?>
                   <?php catalog_banner_widget($key) ?>
               <?php endif; ?>
           <?php endforeach; ?>
        </div>
        <?php require "blocks/catalog/pagination.php" ?>
    </div>
</div>

<?php get_footer(); ?>