<?php

$count_page = $products_obj->max_num_pages;
$page_url = get_current_url();

?>

<div class="catalog-list-nav <?= $count_page == 1?'hidden-block':'' ?>">
    <ul>
        <?php if ($offset_page-1 > 0): ?>
        <li>
            <a href="<?= $page_url ?>">
                <i class="las la-angle-double-left"></i>
            </a>
        </li>
        <li>
            <a href="<?= $page_url.'?page_count='.($offset_page-1)?>">
                <i class="las la-angle-left"></i>
            </a>
        </li>
        <?php endif ?>
        <?php for ($count = 1; $count <= $count_page; $count++): ?>
            <?php if ($count == $offset_page): ?>
            <li>
                <a href="#" class="inactive"><?= $count ?></a>
            </li>
            <?php else: ?>
            <li>
                <a href="<?= $page_url.'?page_count='.$count ?>"><?= $count ?></a>
            </li>
            <?php endif ?>
        <?php endfor ?>
        <?php if ($offset_page+1 <= $count_page): ?>
        <li>
            <a href="<?= $page_url.'?page_count='.($offset_page+1)?>">
                <i class="las la-angle-right"></i>
            </a>
        </li>
        <li>
            <a href="<?= $page_url.'?page_count='.$count_page ?>">
                <i class="las la-angle-double-right"></i>
            </a>
        </li>
        <?php endif ?>
    </ul>
</div>