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
$filter_value = isset($_SESSION['filter']) ? json_decode($_SESSION['filter']) : '';

if (property_exists($filter_value, 'price')) {
    $args['price_rage'] = $filter_value->price;
}

$sort = isset($_SESSION['sort']) ? $_SESSION['sort'] : '';

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

if (property_exists($filter_value, 'have')) {
    $args['stock_status'] = $filter_value->have;
}

if ($filter_value) {
    foreach ($filter_value as $key => $value) {
        if (in_array($key, array('price', 'have'))) continue;
        $query = array(
            'taxonomy' => "pa_$key",
            'field' => 'slug',
            'terms' => $value
        );
        array_push($args['tax_query'], $query);
    }
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
            <?php if (count($products)) : ?>
            <?php foreach ($products as $key => $bike): ?>
               <?php bike_widget($bike, false) ?>
               <?php if ($key == 3 || $key == 13): ?>
                   <?php catalog_banner_widget($key) ?>
               <?php endif; ?>
            <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-query">По данным параметрам не найдено велосипедов</p>
            <?php endif ?>
        </div>
        <?php require "blocks/catalog/pagination.php" ?>
    </div>
</div>

<?php get_footer(); ?>