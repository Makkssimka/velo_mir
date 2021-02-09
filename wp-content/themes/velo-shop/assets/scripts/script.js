"use strict";

jQuery(document).ready(function ($) {
  var selectList = $(".select-input");
  selectList.each(function (index, item) {
    var defOption = $(item).find(".option-item-select").text();
    $(item).find(".select-input-value span").text(defOption);
  });
  $('.select-input-value').click(function () {
    $('.open-input-select').removeClass('open-input-select');
    $(this).parent().addClass('open-input-select');
  });
  $(".select-option-item").click(function () {
    $(".option-item-select").removeClass('option-item-select');
    $(this).addClass('option-item-select');
    var valOption = $(this).text();
    var parent = $(this).parents('.select-input');
    parent.find(".select-input-value span").text(valOption);
    parent.removeClass('open-input-select');
    var result = $(this).data('value');
    parent.trigger("change", [result]);
  });
  $(document).click(function (e) {
    if ($(e.target).parents('.select-input').length) {
      return;
    } else {
      $('.open-input-select').removeClass('open-input-select');
    }
  });

  if ($('.product').length) {
    var owlGgallery = $('#product-carousel').owlCarousel({
      dots: false,
      items: 1
    });
    $('.product-gallery-thumbnail li').click(function (e) {
      e.preventDefault();
      $('.product-gallery-thumbnail .active').removeClass('active');
      $(this).addClass('active');
      var imageNum = $(this).data('numb');
      owlGgallery.trigger('to.owl.carousel', [imageNum]);
    });
    owlGgallery.on('changed.owl.carousel', function (e) {
      $('.product-gallery-thumbnail .active').removeClass('active');
      $('[data-numb="' + e.item.index + '"]').addClass('active');
    }); // Tabs script

    $('.product-tabs-header li').click(function (e) {
      e.preventDefault();
      $('.product-tabs-header .active').removeClass('active');
      $(this).addClass('active');
      var tabs = $(this).data('tab');
      $('.product-tabs-body .active').removeClass('active');
      $('[data-tabcontent=' + tabs + ']').addClass('active');
    }); // Similar owl carousel

    var owlSimilar = $('#similar-carousel').owlCarousel({
      dots: false,
      items: 5,
      margin: 10,
      loop: true
    });
    $('.similar-caroussel-nav').click(function (e) {
      e.preventDefault();
      var nav = $(this).data('nav');

      if (nav == "left") {
        owlSimilar.trigger('prev.owl.carousel');
      } else {
        owlSimilar.trigger('next.owl.carousel');
      }
    });
  } // Index page script


  if ($('.home').length) {
    var owlHome = $('#home-slider').owlCarousel({
      dots: false,
      items: 1,
      loop: true,
      autoplay: true,
      autoplayHoverPause: true,
      smartSpeed: 500
    });
    $('.home-slider-nav').click(function () {
      var action = $(this).data('action');

      if (action == "prev") {
        owlHome.trigger('prev.owl.carousel');
      } else {
        owlHome.trigger('next.owl.carousel');
      }
    });
    var priceFrom = $('#homeRange').data('price-from');
    var priceTo = $('#homeRange').data('price-to'); // отслеживаем изменения в выборе велосипедов

    $('#homeRange').ionRangeSlider({
      skin: 'round',
      type: 'double',
      step: 10,
      onFinish: function onFinish(data) {
        updateResult('price', {
          from: data.from,
          to: data.to
        });
      }
    });
    $('.home-bike-select .select-input').on('change', function (e, data) {
      var type = $(this).data('type');
      updateResult(type, data);
    });
  } //заносим данные в массив объектов


  var result_select = [];

  function updateResult(type, value) {
    // ищем есть ли в массиве уже такой объект
    var typeIndex = result_select.findIndex(function (val) {
      return val.type == type;
    }); // если пердается пустое значение удаляем объект

    if (typeIndex != -1 && !value) {
      result_select.splice(typeIndex, 1);
      getProductsCount(); // вызываем функцию запроса на сервер

      return false;
    } // если есть обновляем, если нет создаем


    if (typeIndex != -1) {
      result_select[typeIndex].type = type;
      result_select[typeIndex].value = value;
    } else {
      result_select.push({
        type: type,
        value: value
      });
    }

    getProductsCount(); // вызываем функцию запроса на сервер
  } // Функция запроса на сервер


  function getProductsCount() {
    $.ajax({
      type: 'POST',
      url: window.wp_data.ajax_url,
      dataType: "json",
      data: {
        action: 'get_products_count',
        query: result_select
      },
      beforeSend: function beforeSend() {
        $('.home-bike-select-wrapper').addClass('inactive-element');
        $('.show-bike-select-btn .btn').addClass('invisible-element');
        $('.load-progress').removeClass('invisible-element');
      },
      success: function success(response) {
        var result = response;
        $('.count-product span').text(result);

        if (result == 0) {
          $('.count-product').addClass('inactive-element');
        } else {
          $('.count-product').removeClass('inactive-element');
        }

        $('.home-bike-select-wrapper').removeClass('inactive-element');
        $('.show-bike-select-btn .btn').removeClass('invisible-element');
        $('.load-progress').addClass('invisible-element');
      }
    });
  } // Catalog page script


  if ($('.catalog').length) {
    // filter open
    var openFilterText = 'Развернуть';
    $('.open-list-filter').click(function (e) {
      e.preventDefault();
      var parent = $(this).parents('.catalog-filter-block');
      parent.toggleClass('open-filter-block');
      openFilterText = openFilterText == 'Развернуть' ? 'Свернуть' : 'Развернуть';
      $(this).find('span').text(openFilterText);
    }); // filter price

    var priceMax = $('#priceFrom').data('price-min');
    var priceMin = $('#priceTo').data('price-max');

    var _priceFrom = $('#priceFrom').data('price-from');

    var _priceTo = $('#priceTo').data('price-to');

    $('#priceFrom').val(_priceFrom.toLocaleString());
    $('#priceTo').val(_priceTo.toLocaleString());
    $('#priceRange').ionRangeSlider({
      skin: 'round',
      type: 'double',
      min: priceMax,
      max: priceMin,
      from: _priceFrom,
      to: _priceTo,
      step: 10,
      hide_from_to: true,
      onChange: function onChange(data) {
        $('#priceFrom').val(data.from.toLocaleString());
        $('#priceTo').val(data.to.toLocaleString());
      }
    });
    var priceRange = $('#priceRange').data('ionRangeSlider');
    $('.catalog-filter-price input').on('input', function () {
      var val = Number($(this).val().replace(/\s+/g, ''));
      $(this).val(val.toLocaleString());
    });
    $('.catalog-filter-price input').change(function () {
      priceRange.update({
        from: Number($('#priceFrom').val().replace(/\s+/g, '')),
        to: Number($('#priceTo').val().replace(/\s+/g, ''))
      });
    });
  } // Article page script


  if ($('.article-blocks-wrapper').length) {
    $('.article-blocks').hover(function () {
      $('.article-blocks-active').removeClass('article-blocks-active');
      $(this).addClass('article-blocks-active');
    });
  }

  if ($('.article').length) {
    var anchorBlock = $('.anchor-list');
    var anchorList = $('.anchor-block');
    anchorList.each(function (index, item) {
      var anchorLi = document.createElement('li');
      var anchorA = document.createElement('a');
      anchorA.className = 'anchor-link';
      anchorA.href = "#" + $(item).attr('id');
      anchorA.innerText = $(item).data('link');
      anchorLi.appendChild(anchorA);
      anchorBlock[0].appendChild(anchorLi);
    });
    $('.anchor-link').on('click', function (e) {
      e.preventDefault();
      $('html, body').animate({
        scrollTop: $($(this).attr("href")).offset().top - 100
      }, 500);
    });

    if (anchorList.length) {
      var anchorArray = [];
      anchorList.each(function (index, item) {
        anchorArray.push({
          id: item.id,
          position: item.getBoundingClientRect().top + pageYOffset
        });
      });
      $(document).scroll(function () {
        $('.active-anchor').removeClass('active-anchor');
        var scroll = $(document).scrollTop();
        var arr = anchorArray.filter(function (elem) {
          return elem.position < scroll + 300;
        });
        var el = null;

        if (arr.length) {
          el = $('[href="#' + arr[arr.length - 1].id + '"]');
        } else {
          el = $('[href="#' + anchorArray[0].id + '"]');
        }

        el.addClass('active-anchor');
      });
    } else {
      $('.article-anchor-nav').css('display', 'none');
    }
  } // Modal script


  if ($('.modal-wrapper').length) {
    $('.modal-close').click(function (e) {
      e.preventDefault();
      $('.modal-wrapper').removeClass('modal-visible');
      $('#tel').val('');
      $('#name').val('');
      $('.input-danger').removeClass('input-danger');
      $('.errors-visible').removeClass('errors-visible');
    });
    $('.open-modal').click(function (e) {
      e.preventDefault();
      $('.modal-form-wrapper').removeClass('modal-block-hide');
      $('.modal-message-wrapper').addClass('modal-block-hide');
      $('.modal-wrapper').addClass('modal-visible');
      $('#tel').val('');
      $('#name').val('');
    });
    $('#tel').mask('+7 (999) 999-99-99');
    $('.submit').click(function (e) {
      e.preventDefault();
      $('.input-danger').removeClass('input-danger');
      $('.errors-visible').removeClass('errors-visible');
      var error = false;
      var tel = $('#tel');
      var name = $('#name');

      if (!tel.val()) {
        tel.addClass('input-danger');
        tel.parents('.modal-input').find('label').addClass('errors-visible');
        error = true;
      }

      if (!name.val()) {
        name.addClass('input-danger');
        name.parents('.modal-input').find('label').addClass('errors-visible');
        error = true;
      }

      if (error) return false;
      var dd = {
        action: 'call_form_add_request',
        tel: tel.val(),
        name: name.val()
      };
      $.ajax({
        type: 'POST',
        url: window.wp_data.ajax_url,
        dataType: "json",
        data: dd,
        beforeSend: function beforeSend() {
          $('.modal-form-wrapper').addClass('modal-inactive');
        },
        success: function success(data) {
          console.log(data);
          $('.modal-form-wrapper').addClass('modal-block-hide').removeClass('modal-inactive');

          if (data) {
            $('.modal-done').removeClass('modal-block-hide');
          } else {
            $('.modal-error').removeClass('modal-block-hide');
          }
        }
      });
    });
  } // Filter submit


  $('#filter_submit').click(function (e) {
    e.preventDefault();
    var list_checkbox = $('input[type="checkbox"]');
    var value_array = {};
    list_checkbox.each(function (index, item) {
      if ($(item).is(":checked")) {
        var key = $(item).attr('name');
        var value = $(item).attr('id');

        if (key in value_array) {
          value_array[key].push(value);
        } else {
          value_array[key] = new Array(value);
        }
      }
    });
    var price = [$('#priceFrom').val().replace(/[^\d.\-]/g, ''), $('#priceTo').val().replace(/[^\d.\-]/g, '')];
    var link = '?session_add';

    for (var key in value_array) {
      link += '&' + key + '=' + value_array[key].join(',');
    }

    link += '&price=' + price.join(',');
    window.location.href = window.location.pathname + link;
  });
  $('#sort').on("change", function (e, result) {
    var link = '?session_add&sort=' + result;
    window.location.href = window.location.pathname + link;
  }); //Ajax
  //Добавляем в избранное

  $('.add-favorites').click(function (e) {
    e.preventDefault();
    var elem = $(this);
    $.ajax({
      type: 'POST',
      url: window.wp_data.ajax_url,
      data: {
        action: 'add_favorites',
        id: elem.data('id')
      },
      beforeSend: function beforeSend() {
        elem.addClass('flicker-anim');
      },
      success: function success(response) {
        var result = JSON.parse(response);
        elem.removeClass('flicker-anim');

        if (result.status == 1) {
          elem.addClass('added-item');
        } else {
          elem.removeClass('added-item');
        }

        $('#favorites').text(result.counter);

        if (elem.find('.result-text')) {
          elem.find('.result-text').text(result.status ? 'Добавлен в избранное' : 'В избранное');
        }
      }
    });
  }); //Дабавляем в сравнение

  $('.add-compare').click(function (e) {
    e.preventDefault();
    var elem = $(this);
    $.ajax({
      type: 'POST',
      url: window.wp_data.ajax_url,
      data: {
        action: 'add_compare',
        id: elem.data('id')
      },
      beforeSend: function beforeSend() {
        elem.addClass('flicker-anim');
      },
      success: function success(response) {
        var result = JSON.parse(response);
        elem.removeClass('flicker-anim');

        if (result.status == 1) {
          elem.addClass('added-item');
        } else {
          elem.removeClass('added-item');
        }

        $('#compare').text(result.counter);

        if (elem.find('.result-text')) {
          elem.find('.result-text').text(result.status ? 'Добавлен к сравнению' : 'В сравнение');
        }
      }
    });
  });
});