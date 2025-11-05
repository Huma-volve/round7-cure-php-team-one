// resources/js/bootstrap.js (أو ملف مخصص)
import Echo from 'laravel-echo';

// Reverb uses socket.io-like interface; Laravel Reverb docs توضّح الإعداد
window.io = require('socket.io-client');

window.Echo = new Echo({
  broadcaster: 'socket.io',
  host: window.location.hostname + ':6001', // المنفذ الذي يشغّل reverb/rever server أو حسب إعدادك
  // إضافة auth headers لو تستخدم sanctum/token
  auth: {
    headers: {
      Authorization: 'Bearer ' + localStorage.getItem('token') // أو axios default
    }
  }
});
