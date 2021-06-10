<?php
$telephone_array = explode(', ', get_option('telephone_num'));
$time_job_array = time_to_array(get_option('time_job'));
$vk_url = get_option('vk');
$inst_url = get_option('instagram');
?>

<div class="bottom-sidebar">
    <div class="left-bottom-sidebar">
        <div class="logo-bottom-siderbar">
            <?= get_custom_logo(); ?>
        </div>
        <ul>
            <?php foreach ($telephone_array as $telephone) : ?>
            <li><a href="tel:+7 <?= $telephone ?>" class="bottom-sidebar-tel">+7 <?= $telephone ?></a></li>
            <?php endforeach; ?>
            <li class="bottom-sidebar-time">
            <?php foreach ($time_job_array as $time_job) : ?>
            <div>
                <?= $time_job['label'] ?> <span><?= $time_job['time'] ?></span>
            </div>
            <?php endforeach; ?>
            </li>
            <?php if (get_option('call_show')) : ?>
            <li><a href="#" class="open-modal">Обратный звонок</a></li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="right-bottom-sidebar">
        <div class="bottom-sidear-menu">
            <?php wp_nav_menu(['theme_location' => 'bottom_main_menu', 'container' => false ]);?>
        </div>
        <div class="bottom-sidear-submenu">
            <?php wp_nav_menu(['theme_location' => 'bottom_menu', 'container' => false ]);?>
        </div>
        <div class="iocns-bottom-sidebar">
            <div class="icons-block-bottom-sidebar">
                <p>Присоединяйтесь в соцсетях:</p>
                <ul>
                    <li><a target="_blank" href="<?= $inst_url ?>"><i class="lab la-instagram"></i></a></li>
                    <li><a target="_blank" href="<?= $vk_url ?>"><i class="lab la-vk"></i></a></li>
                </ul>
            </div>
            <div class="icons-block-bottom-sidebar">
                <p>Принимаем к оплате:</p>
                <ul>
                    <li><i class="lab la-cc-mastercard"></i></li>
                    <li><i class="lab la-cc-visa"></i></li>
                    <li><i class="lab la-cc-jcb"></i></li>
                    <li><i class="lab la-cc-apple-pay"></i></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="copy-bottom-sidebar">
        2007-2020 &copy; ВелоМир Все права защищены
    </div>
</div>