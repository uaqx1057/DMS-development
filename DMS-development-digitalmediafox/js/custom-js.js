//   document.addEventListener('DOMContentLoaded', function () {
//         const toggleBtn = document.getElementById('password-addon');
//         const passwordInput = document.getElementById('password');
//         const icon = toggleBtn.querySelector('i');

//         toggleBtn.addEventListener('click', function () {
//             const isPassword = passwordInput.getAttribute('type') === 'password';
//             passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
//             icon.classList.toggle('ri-eye-fill', !isPassword);
//             icon.classList.toggle('ri-eye-off-fill', isPassword); // use ri-eye-off-fill for hidden state
//         });
//     });