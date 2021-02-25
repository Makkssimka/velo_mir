<?php
    $total_cart = WC()->cart;
?>
<div class="cart-total">
    <div class="cart-total-wrapper">

        <div class="cart-total-subtotal">
            Стоимость товара: <span><?= $total_cart->get_cart_subtotal() ?></span>
        </div>
        <div class="cart-total-my-coupon">
            Ваш купон: <span>нет</span>
        </div>
        <div class="cart-total-sale">
            Скидка: <span><?= wc_price($total_cart->get_cart_discount_total()) ?></span>
        </div>
        <hr>
        <div class="cart-total-header">У меня есть промокод:</div>
        <div class="cart-total-coupon">
            <input type="text" placeholder="промокод">
            <a href="#" class="btn btn-blue">Применить</a>
        </div>
        <hr>
        <div class="cart-total-header cart-total-sum">Итого: <span><?= $total_cart->get_cart_total() ?><span></div>
        <div class="cart-total-submit">
            <a href="#" class="btn btn-green">Оформить заказ</a>
        </div>
    </div>
</div>