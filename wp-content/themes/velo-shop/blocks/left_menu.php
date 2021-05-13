<div class="left-menu">
    <div class="logo-menu">
        <?= get_custom_logo(); ?>
    </div>
    <nav>
        <?php wp_nav_menu(['theme_location' => 'left_menu', 'container' => false]);?>
        <?php wp_nav_menu(['theme_location' => 'location_menu', 'container' => false]); ?>
    </nav>
</div>