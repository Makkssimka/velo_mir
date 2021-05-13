<div class="mobile-menu">
    <div class="mobile-main-menu">
        <?php wp_nav_menu(['theme_location' => 'left_menu', 'container' => false]);?>
    </div>
    <div class="mobile-sub-menu">
        <div class="mobile-submenu-header">Ваши товары</div>
        <ul>
            <li><a href="/cart">Корзина</a></li>
            <li><a href="/favorites">Избранные</a></li>
            <li><a href="/compare">Сравнение</a></li>
        </ul>
    </div>
    <div class="mobile-sub-menu">
        <div class="mobile-submenu-header">Разделы сайта</div>
        <?php wp_nav_menu(['theme_location' => 'bottom_menu', 'container' => false ]);?>
    </div>
</div>