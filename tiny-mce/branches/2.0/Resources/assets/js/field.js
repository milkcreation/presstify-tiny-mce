/* global jQuery, tify */
"use strict";

jQuery(document).ready(function ($) {
  $('[data-control="tinymce"]').each(function() {
    let o = $.parseJSON(decodeURIComponent($(this).data('options'))) || {};

    $(this).tinymce(o);
  });
});