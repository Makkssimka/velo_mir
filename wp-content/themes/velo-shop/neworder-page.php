<?php
/* Template Name: New Order */

global $post;
$order_number = $_GET['order'];

?>

<?php get_header(); ?>
<div class="content-main article favorites">
    <h1><?= $post->post_title; ?></h1>
    <div class="article-subheader"><?= $post->post_excerpt; ?></div>
    <div class="article-content">
        <div class="article-text">

        </div>
        <div class="article-navbar">
            <?php expert_widget() ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>
