"use strict";
document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("login-name").addEventListener("input", validateName);
  document
    .getElementById("password")
    .addEventListener("input", validatePassword);
  document
    .getElementById("confirm-password")
    .addEventListener("input", validateConfirm);
  document.getElementById("email").addEventListener("input", validateEmail);
});

function validateName() {
  const loginName = document.getElementById("login-name").value;
  const nameError = document.getElementById("nameError");
  if (loginName.length < 6 || loginName.length > 20) {
    nameError.textContent = "Length of login name must be between 6 and 20.";
    nameError.classList.add("active");
    document.getElementById("login-name").classList.add("error-field");
    return false;
  }
  nameError.classList.remove("active");
  document.getElementById("login-name").classList.remove("error-field");
  return true;
}

function validatePassword() {
  const password = document.getElementById("password").value;
  const passwordError = document.getElementById("passwordError");
  if (password.length < 6 || password.length > 20) {
    passwordError.textContent = "Length of password must be between 6 and 20.";
    passwordError.classList.add("active");
    document.getElementById("password").classList.add("error-field");
    return false;
  }
  passwordError.classList.remove("active");
  document.getElementById("password").classList.remove("error-field");
  return true;
}

function validateConfirm() {
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirm-password").value;
  const confirmError = document.getElementById("confirmError");
  if (password !== confirmPassword) {
    confirmError.textContent = "The password should be the same both times";
    confirmError.classList.add("active");
    document.getElementById("confirm-password").classList.add("error-field");
    return false;
  }
  confirmError.classList.remove("active");
  document.getElementById("confirm-password").classList.remove("error-field");
  return true;
}

function validateEmail() {
  const email = document.getElementById("email").value;
  const emailError = document.getElementById("emailError");
  const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
  if (!emailPattern.test(email)) {
    emailError.textContent = "Please enter a valid email address.";
    emailError.classList.add("active");
    document.getElementById("email").classList.add("error-field");
    return false;
  }
  emailError.classList.remove("active");
  document.getElementById("email").classList.remove("error-field");
  return true;
}

function validate() {
  const isNameValid = validateName();
  const isPasswordValid = validatePassword();
  const isConfirmValid = validateConfirm();
  const isEmailValid = validateEmail();

  if (isNameValid && isPasswordValid && isConfirmValid && isEmailValid) {
    return true;
  }

  return false;
}

document
  .getElementById("signup-form")
  .addEventListener("submit", function (event) {
    event.preventDefault();
    const loginName = document.getElementById("login-name").value;
    const password = document.getElementById("password").value;
    const email = document.getElementById("email").value;
    const firstName = document.getElementById("first-name").value;
    const lastName = document.getElementById("last-name").value;

    const requestBody = {
      loginName: loginName,
      password: password,
      email: email,
      firstName: firstName,
      lastName: lastName,
    };

    if (!validate()) {
      return; // 校验不通过，直接返回，不调用后端
    }

    const apiUrl =
      "http://localhost/web-assignment2/server/userManagement/create_user.php";

    fetch(apiUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(requestBody),
    })
      .then((response) => {
        if (response.ok) {
          return response.json();
        } else {
          throw new Error("Registration failed");
        }
      })
      .then((data) => {
        if (data.code === 0) {
          alert("Registration successful!");
          window.location.href = "/web-assignment2/pages/index.html";
        } else {
          alert(`${data.message}`);
        }
      })
      .catch((error) => {
        alert(`Error: ${error.message}`);
      });
  });
