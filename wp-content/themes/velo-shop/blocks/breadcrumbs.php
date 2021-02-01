<?php
global  $post;
$categories = get_the_category($post->id);

if (!is_front_page()): ?>
<div class="breadcrumbs">
    <ul>
        <li><a href="/">Главная</a></li>
        <?php foreach ($categories as $category): ?>
            <li><i class="las la-angle-right"></i></li>
            <li class="breadcrumbs-inactive"><?= $category->name; ?></li>
        <?php endforeach; ?>
        <li><i class="las la-angle-right"></i></li>
        <li class="breadcrumbs-inactive"><?= $post->post_title; ?></li>
    </ul>
</div>
<?php endif; ?>