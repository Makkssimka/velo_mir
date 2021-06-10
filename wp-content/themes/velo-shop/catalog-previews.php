<?php
/* Template Name: Catalog Previews*/

get_header();

?>

<div class="content-main article compare">
    <h1><?= $post->post_title; ?></h1>
    <div class="article-subheader"><?= $post->post_excerpt; ?></div>
    <div class="article-content">
        <div class="list-categories">
            <a href="/bikes-catalog?session_filter&type_velo=детские" class="block-categories">
                <div class="images-categories">
                    <img src="<?= get_asset_path("images", "kind_type.jpeg") ?> " alt="">
                </div>
                <div class="header-categories">Детские</div>
            </a>
            <a href="/bikes-catalog?session_filter&type_velo=дорожные" class="block-categories">
                <div class="images-categories">
                    <img src="<?= get_asset_path("images", "way_type.jpeg") ?> " alt="">
                </div>
                <div class="header-categories">Дорожные</div>
            </a>
            <a href="/bikes-catalog?session_filter&type_velo=складные" class="block-categories">
                <div class="images-categories">
                    <img src="<?= get_asset_path("images", "fold_type.jpeg") ?> " alt="">
                </div>
                <div class="header-categories">Складные</div>
            </a>
            <a href="/bikes-catalog?session_filter&type_velo=горные" class="block-categories">
                <div class="images-categories">
                    <img src="<?= get_asset_path("images", "gorn_type.jpeg") ?> " alt="">
                </div>
                <div class="header-categories">Горные</div>
            </a>
            <a href="/bikes-catalog?session_filter&type_velo=двухподвесы" class="block-categories">
                <div class="images-categories">
                    <img src="<?= get_asset_path("images", "two_type.jpeg") ?> " alt="">
                </div>
                <div class="header-categories">Двухподвесные</div>
            </a>
            <a href="/bikes-catalog?session_filter&type_velo=женские" class="block-categories">
                <div class="images-categories">
                    <img src="<?= get_asset_path("images", "woman_type.jpeg") ?> " alt="">
                </div>
                <div class="header-categories">Женские</div>
            </a>
            <a href="/bikes-catalog?session_filter&type_velo=fat-bike" class="block-categories">
                <div class="images-categories">
                    <img src="<?= get_asset_path("images", "fat_type.jpeg") ?> " alt="">
                </div>
                <div class="header-categories">Fat-bike</div>
            </a>
            <a href="/bikes-catalog?session_filter&type_velo=bmx" class="block-categories">
                <div class="images-categories">
                    <img src="<?= get_asset_path("images", "bmx_type.jpeg") ?> " alt="">
                </div>
                <div class="header-categories">BMX</div>
            </a>
            <a href="/bikes-catalog?session_filter&type_velo=dirt" class="block-categories">
                <div class="images-categories">
                    <img src="<?= get_asset_path("images", "dirt_type.jpeg") ?> " alt="">
                </div>
                <div class="header-categories">Dirt</div>
            </a>
            <a href="/bikes-catalog?session_filter&type_velo=электро" class="block-categories">
                <div class="images-categories">
                    <img src="<?= get_asset_path("images", "electro_type.webp") ?> " alt="">
                </div>
                <div class="header-categories">Электровелосипеды</div>
            </a>
        </div>
    </div>
</div>

<?php get_footer(); ?>

