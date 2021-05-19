<div class="left-menu">
    <div class="logo-menu">
        <?= get_custom_logo(); ?>
    </div>
    <nav>
        <?php wp_nav_menu(['theme_location' => 'left_menu', 'container' => false]);?>
        <ul class="open-desktop-menu">
            <li>
                <a href="#"><span class="las la-arrow-right"></span></a>
            </li>
        </ul>
    </nav>
</div>