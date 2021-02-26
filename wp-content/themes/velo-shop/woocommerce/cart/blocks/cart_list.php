
<div class="product-cart-list">
    <table>
        <thead>
        <tr>
            <th>Продукт</th>
            <th>Название</th>
            <th>Количество</th>
            <th>Цена</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php do_action( 'woocommerce_before_cart_contents' ); ?>
        <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) : ?>
            <?php $product = wc_get_product($cart_item['variation_id']) ?>
            <?php $parent = wc_get_product($cart_item['product_id']) ?>
            <tr>
                <td class="cart-item-image">
                    <img src="<?= wp_get_attachment_url($product->get_image_id()) ?>">
                </td>
                <td class="cart-item-description">
                    <div class="cart-item-name">
                        <?= $product->get_title() ?>
                    </div>
                    <div class="cart-item-sku">
                        <?= $product->get_sku() ?>
                    </div>
                    <div class="cart-item-attribute">
                        <span>колеса:</span>
                        <?= get_wheel_size_string($parent->get_attribute('wheel_size')) ?>
                        <span>цвет:</span>
                        <?= $product->get_attribute('color') ?>
                        <?php if ($product->get_attribute('frame_size')) : ?>
                            <span>рама:</span>
                            <?= get_frame_size_string($product->get_attribute('frame_size'), false) ?>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="cart-item-quantity">
                    <div class="quantity-wrapper">
                        <span class="up-down-btn down"><i class="las la-minus"></i></span>
                        <input data-key="<?= $cart_item['key'] ?>" class="cart-counter" type="number" max="10" min="1" value="<?= $cart_item['quantity'] ?>">
                        <span class="up-down-btn up"><i class="las la-plus"></i></span>
                    </div>
                </td>
                <td class="cart-item-total">
                    <?= wc_price($cart_item['line_subtotal']) ?>
                </td>
                <td class="cart-item-remove">
                    <i data-key="<?= $cart_item['key'] ?>" class="las la-times item-remove"></i>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td></td>
            <td></td>
            <td class="cart-subtotal-desc">Общая сумма:</td>
            <td class="cart-subtotal"><?= WC()->cart->get_cart_subtotal() ?></td>
            <td></td>
        </tr>
        </tbody>
    </table>
</div>