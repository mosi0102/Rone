/* errors.js — دکمه‌های صفحات خطا (بدون وابستگی به jQuery/App؛ حتی وقتی بقیه‌ی اسکریپت‌ها لود نشده‌اند کار می‌کند) */
(function () {
  'use strict';
  document.addEventListener('click', function (e) {
    var t = e.target.closest('[data-reload],[data-back]'); if (!t) return;
    if (t.hasAttribute('data-reload')) location.reload();
    else if (history.length > 1) history.back(); else location.href = '/';
  });
})();
