const loginTabs = document.querySelectorAll('#loginTabs button');
const registerTabs = document.querySelectorAll('#registerTabs button');

function toggleFields(tabs, roleInput, fieldMap) {
  tabs.forEach(button => {
    button.addEventListener('click', () => {
      tabs.forEach(btn => btn.classList.remove('active'));
      button.classList.add('active');
      const role = button.dataset.role;
      roleInput.value = role;
      Object.entries(fieldMap).forEach(([field, selector]) => {
        const el = document.querySelector(selector);
        if (!el) return;
        if (field === role) {
          el.classList.remove('d-none');
          el.querySelectorAll('input, select').forEach(i => i.required = true);
        } else {
          el.classList.add('d-none');
          el.querySelectorAll('input, select').forEach(i => i.required = false);
        }
      });
    });
  });
}

if (loginTabs.length) {
  toggleFields(loginTabs, document.getElementById('loginRole'), {
    student: '.student-field',
    staff: '.staff-field',
    admin: '.admin-field'
  });
}

if (registerTabs.length) {
  toggleFields(registerTabs, document.getElementById('registerRole'), {
    student: '.student-field',
    staff: '.staff-field',
    admin: '.admin-field'
  });
}
