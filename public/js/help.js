/* help.js — جستجو و فیلتر گروه در راهنما */
(function ($) {
  'use strict';
  var $q = $('#hpQ'); if (!$q.length) return;
  var g = 'all';
  function norm(s) { return String(s).replace(/[۰-۹]/g, function (d) { return d.charCodeAt(0) - 1776; }).replace(/[يى]/g, 'ی').replace(/ك/g, 'ک').toLowerCase().trim(); }
  function run() {
    var t = norm($q.val()), shown = 0;
    $('.hp-group').each(function () {
      var $gr = $(this), inG = g === 'all' || $gr.data('group') === g, any = 0;
      $gr.find('.hp-item').each(function () { var m = inG && (!t || norm($(this).text()).indexOf(t) > -1); $(this).toggleClass('d-none', !m); if (m) any++; });
      $gr.toggleClass('d-none', !any); shown += any;
    });
    $('#hpEmpty').toggleClass('d-none', shown > 0);
  }
  $q.on('input', run);
  $(document).on('click', '[data-g]', function () { g = $(this).data('g'); $('[data-g]').removeClass('active'); $(this).addClass('active'); run(); });
})(jQuery);
