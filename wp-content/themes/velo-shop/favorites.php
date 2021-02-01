<?php
/* Template Name: Favorites */

global $post;

?>

<?php get_header(); ?>

<div class="content-main article">
    <h1><?= $post->post_title; ?></h1>
    <div class="article-subheader"><?= $post->post_excerpt; ?></div>
</div>

<?php get_footer(); ?>
