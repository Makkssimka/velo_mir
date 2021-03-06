<?php
$cart = WC()->cart;

$cart_count = $cart->get_cart_contents_count();

$telephone_array = explode(', ', get_option('telephone_num'));
$time_job_array = time_to_array(get_option('time_job'));

$favorites_array = isset($_SESSION['favorites'])?json_decode($_SESSION['favorites']):array();
$compare_array = isset($_SESSION['compare'])?json_decode($_SESSION['compare']):array();

?>
<div class="top-sidebar">
    <div class="mobile-top-sidebar">
        <div class="mobile-menu-open-wrapper">
            <a href="#">
                <i class="las la-bars"></i>
            </a>
        </div>
        <div class="mobile-top-sidebar-logo">
            <?= get_custom_logo(); ?>
        </div>
        <div class="mobile-top-sidebar-cart">
            <a href="/cart">
                <i class="las la-shopping-cart"></i>
                <div id="cart_mobile" class="label-number <?= $cart_count?'':'hidden-block' ?>">
                    <?= $cart_count ?>
                </div>
            </a>
        </div>
    </div>
    <div class="left-top-sidebar">
        <ul>
            <?php foreach ($telephone_array as $telephone) : ?>
            <li><a href="tel:+7 <?= $telephone ?>" class="top-sidebar-tel">+7 <?= $telephone ?></a></li>
            <?php endforeach; ?>
            <li class="top-sidebar-time">
                <?php foreach ($time_job_array as $time_job) : ?>
                <span class="top-sidebar-row">
                    <?= $time_job['label'] ?> <span><?= $time_job['time'] ?></span>
                </span>
                <?php endforeach; ?>
            </li>
            <?php if (get_option('call_show')) : ?>
            <li class="top-side-bar-call"><a href="#" class="open-modal">Обратный звонок</a></li>
            <?php endif; ?>
        </ul>
        <?php wp_nav_menu(['theme_location' => 'top_menu', 'container' => false, 'menu_class' => 'top-sidebar-menu']);?>
    </div>
    <div class="right-top-sidebar">
        <ul>
            <li><a href="/compare">
                    <i class="las la-balance-scale-left"></i>
                    <span>Сравнение</span>
                        <div id="compare" class="label-number <?= count($compare_array)?'':'hidden-block' ?>">
                            <?= count($compare_array) ?>
                        </div>
                </a></li>
            <li><a href="/favorites">
                    <i class="lar la-star"></i>
                    <span>Избранное</span>
                    <div id="favorites" class="label-number <?= count($favorites_array)?'':'hidden-block' ?>">
                        <?= count($favorites_array) ?>
                    </div>
                </a></li>
            <li><a href="/cart">
                    <i class="las la-shopping-cart"></i>
                    <span>Корзина</span>
                    <div id="cart" class="label-number <?= $cart_count?'':'hidden-block' ?>">
                        <?= $cart_count ?>
                    </div>
                </a></li>
        </ul>
    </div>
</div>