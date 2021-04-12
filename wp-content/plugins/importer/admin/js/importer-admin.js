(function( $ ) {
	'use strict';

	$(document).ready(function () {
		$("#sku-generator").click(function (e) {
			e.preventDefault();
			let btn = $(this).addClass("button-disabled");
			btn.addClass("button-disabled");
			$.post(ajaxurl, {action: "sku_generated"}, function (data) {
				addTr("Добавлено артикулов",data);
				btn.removeClass("button-disabled");
			});
		})
	});

	function addTr(msg,count) {
		let table = $("#sku-table");
		let tr = `<tr>
					<td class="first-td-importer">
						<span class="importer-title">${ msg }</span>
					</td>
					<td>
						<span class="importer-title">${ count }</span>
					</td>
				</tr>`;
		table.append(tr);
	}

})( jQuery );
