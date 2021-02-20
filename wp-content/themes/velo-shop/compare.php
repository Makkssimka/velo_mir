<?php
/* Template Name: Compare */

global $post;

$compare_ids = (isset($_SESSION['compare'])) ? json_decode($_SESSION['compare']) : array();

if ($compare_ids) {
    $bikes = wc_get_products(array(
        'include' => $compare_ids
    ));

    $image_table = "<tr><td></td>";
    $price_table = "<tr><td></td>";
    $title_table = "<tr><td></td>";
    $add_table = "<tr><td></td>";
    $brand_table = "<tr class='compare-option'><td>Производитель</td>";
    $type_velo_table = "<tr class='compare-option'><td>Тип велосипеда</td>";
    $size_wheel_table = "<tr class='compare-option'><td>Размер колес</td>";
    $size_frame_table = "<tr class='compare-option'><td>Варианты размера рамы</td>";
    $material_table = "<tr class='compare-option'><td>Материал</td>";
    $speed_table = "<tr class='compare-option'><td>Колическтво скоростей</td>";
    $tormoz_table = "<tr class='compare-option'><td>Тип тормоза</td>";
    $color_table = "<tr class='compare-option'><td>Варианты цветов</td>";
    $remove_table = "<tr><td></td>";

    foreach ($bikes as $bike) {
        $image_table .= "<td><img src='".$bike->get_available_variations()[0]['image']['url']."'></td>";
        $price_table .= "<td class='compare-price'>".wc_price($bike->get_price())."</td>";
        $title_table .= "<td class='compare-title'>".$bike->get_title()."</td>";
        $add_table .= "<td class='compare-btn'><a href='#' class='btn btn-blue'>В корзину</a></td>";
        $brand_table .= "<td>".$bike->get_attribute('brand')."</td>";
        $type_velo_table .= "<td>".$bike->get_attribute('type_velo')."</td>";
        $size_wheel_table .= "<td>".get_wheel_size_string($bike->get_attribute('wheel_size'))."</td>";
        $size_frame_table .= "<td>".get_frame_size_one_string($bike->get_attribute('frame_size'))."</td>";
        $material_table .= "<td>".$bike->get_attribute('material')."</td>";
        $speed_table .= "<td>".$bike->get_attribute('speed')."</td>";
        $tormoz_table .= "<td>".$bike->get_attribute('tormoz')."</td>";
        $color_table .= "<td>".$bike->get_attribute('color')."</td>";
        $remove_table .= "<td class='compare-remove'><a href='?compare_remove&id=".$bike->get_id()."'>убрать из списка</a></td>";
    }

    $image_table .= "</tr>";
    $price_table .= "</tr>";
    $title_table .= "</tr>";
    $add_table .= "</tr>";
    $brand_table .= "</tr>";
    $type_velo_table .= "</tr>";
    $size_wheel_table .= "</tr>";
    $size_frame_table .= "</tr>";
    $material_table .= "</tr>";
    $speed_table .= "</tr>";
    $tormoz_table .= "</tr>";
    $color_table .= "</tr>";
    $remove_table .= "</tr>";
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
                    <table>
                        <?= $image_table ?>
                        <?= $title_table ?>
                        <?= $price_table ?>
                        <?= $add_table ?>
                        <?= $color_table ?>
                        <?= $brand_table ?>
                        <?= $type_velo_table ?>
                        <?= $size_wheel_table ?>
                        <?= $size_frame_table ?>
                        <?= $material_table ?>
                        <?= $speed_table ?>
                        <?= $tormoz_table ?>
                        <?= $remove_table ?>
                    </table>
                <?php else : ?>
                    <div class="compare-empty">
                        <img src="<?= get_asset_path('images', 'not_found.svg') ?>" alt="">
                        <p>Вы еще не выбрали товары для сравнения</p>
                    </div>
                <?php endif ?>
                </div>
            </div>
            <div class="article-navbar">
                <?php expert_widget() ?>
            </div>
        </div>
    </div>

<?php get_footer(); ?>