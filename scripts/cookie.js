"use strict";

function setCookie(name, value, days) {
  // 序列化对象为 JSON 字符串
  const serializedValue = JSON.stringify(value);
  let expires = "";
  if (days) {
    const date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie =
    name + "=" + encodeURIComponent(serializedValue) + expires + "; path=/";
}

function getCookie(name) {
  const nameEQ = name + "=";
  const cookies = document.cookie.split("; ");
  for (let cookie of cookies) {
    if (cookie.startsWith(nameEQ)) {
      // 解码并解析为对象
      return JSON.parse(decodeURIComponent(cookie.substring(nameEQ.length)));
    }
  }
  return null; // 未找到时返回 null
}
