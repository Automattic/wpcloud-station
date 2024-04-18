/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*********************************!*\
  !*** ./blocks/src/form/view.js ***!
  \*********************************/
const redirectNotification = status => {
  const urlParams = new URLSearchParams(window.location.search);
  urlParams.append('wp-form-result', status);
  window.location.search = urlParams.toString();
};
document.querySelectorAll('form.wpcloud-block-form[data-ajax]').forEach(form => {
  const button = form.querySelector('button[type="submit"]');
  form.addEventListener('submit', async e => {
    e.preventDefault();
    button.setAttribute('disabled', 'disabled');
    form.classList.add('is-loading');
    form.classList.remove('is-error');
    const formData = Object.fromEntries(new FormData(form).entries());
    formData.action = 'wpcloud_form_submit';
    try {
      const response = await fetch('http://localhost:8888/wp-admin/admin-ajax.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(formData).toString()
      });
      const result = await response.json();
      if (response.ok && result?.data?.redirect) {
        window.location = result.data.redirect;
      }
      button.removeAttribute('disabled');
      form.classList.remove('is-loading');
      if (!response.ok) {
        form.classList.add('is-error');
      }
    } catch (error) {
      redirectNotification('error');
    }
  });
});
/******/ })()
;
//# sourceMappingURL=view.js.map