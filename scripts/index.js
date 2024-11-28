"use strict";

document
  .getElementById("login-form")
  .addEventListener("submit", async function (event) {
    event.preventDefault(); // 阻止表单的默认提交行为

    const loginName = document.getElementById("login-name").value.trim();
    const password = document.getElementById("password").value.trim();
    const messageDiv = document.getElementById("message");

    // 清空之前的提示信息
    messageDiv.textContent = "";

    // 模拟后端接口地址
    const apiUrl = "http://127.0.0.1:8080/web/assignment2/login";

    try {
      const response = await fetch(apiUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ loginName, password }),
      });
      const result = await response.json();
      console.log(result);
      if (response.ok && result.code === 0) {
        // 登录成功，跳转页面
        setCookie("userInfo", result.data, 3);
        window.location.href = "/pages/task management.html"; // 替换为目标页面
      } else {
        // 登录失败，显示错误信息
        messageDiv.textContent = result.msg || "Invalid credentials";
        messageDiv.classList.add("error");
      }
    } catch (error) {
      // 网络错误或其他错误
      messageDiv.textContent = "An error occurred. Please try again later.";
    }
  });
