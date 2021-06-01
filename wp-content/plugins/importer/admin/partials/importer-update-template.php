<div class="wrap importer-wrap">
    <h1 class="wp-heading-inline">Ручная проверка и обновление базы товаров</h1>
    <p>Перед обновлением сделайте обмен с сайтом в 1С</p>
    <form method="get">
        <input type="hidden" name="page" value="import-1c">
        <p>
            <button type="submit" name="action" class="button button-primary" value="update-import">Импортировать данные</button>
            <button type="submit" name="action" class="button" value="test-import">Проверить изменения</button>
        </p>
        <?php if (isset($_GET['action']) && $_GET['action'] == "update-import") : ?>
            <table class="widefat striped">
                <tr>
                    <td class="first-td-importer">
                        <span class="importer-title">Обработано товаров</span>
                    </td>
                    <td>
                        <span class="importer-title"><?= $files_import->get_counter() ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="first-td-importer">
                        <span class="importer-title">Новых товаров найдено</span>
                    </td>
                    <td>
                        <span class="importer-title"><?= $files_import->get_new_file_counter() ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="first-td-importer">
                        <span class="importer-title">Добавлено новых товаров</span>
                    </td>
                    <td>
                        <span class="importer-title"><?= $files_import->get_new_file_counter() ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="first-td-importer">
                        <span class="importer-title">Обновлено товаров</span>
                    </td>
                    <td>
                        <span class="importer-title"><?= $files_import->get_update_file_counter() ?></span>
                    </td>
                </tr>
            </table>
        <?php endif ?>
        <?php if (isset($_GET['action']) && $_GET['action'] == "test-import") : ?>
            <table class="widefat striped">
                <tr>
                    <td class="first-td-importer">
                        <span class="importer-title">Обработано товаров</span>
                    </td>
                    <td>
                        <span class="importer-title"><?= $files_import->get_counter() ?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="importer-title">Из них есть в базе</span>
                    </td>
                    <td>
                        <span class="importer-title"><?= $files_import->get_test_counter() ?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="importer-title">Из них новых</span>
                    </td>
                    <td>
                        <span class="importer-title"><?= $files_import->get_new_counter() ?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="importer-title">Из них измененна цена</span>
                    </td>
                    <td>
                        <span class="importer-title"><?= $files_import->get_price_change_counter() ?></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="importer-title">Из них измененно количество</span>
                    </td>
                    <td>
                        <span class="importer-title"><?= $files_import->get_quantity_change_counter() ?></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                    <?php if ($files_import->is_update()) :?>
                        <span class="status-not">Требуется обновление базы товаров,</span> нажмите кнопку "Импортировать данные"
                    <?php else: ?>
                        <span class="status-ok">База данных товаров актуальна</span> никаких действий не требуется
                    <?php endif; ?>
                    </td>
                </tr>
            </table>
        <?php endif ?>
    </form>
</div>