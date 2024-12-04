/* 
    JS Comments
    Name: Ke Zhao 041129546
    File Name: registration.js
    Date Created: 2024-11-25
    Description: This script handles user registration validation and submission for registration.html.
*/
"use strict";

// Attach event listeners after the DOM has fully loaded
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

/**
 * Validates the login name field.
 *
 * @returns {boolean} - True if the login name is valid, otherwise false.
 */
function validateName() {
  const loginName = document.getElementById("login-name").value;
  const nameError = document.getElementById("nameError");

  // Check if the length is between 6 and 20 characters
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

/**
 * Validates the password field.
 *
 * @returns {boolean} - True if the password is valid, otherwise false.
 */
function validatePassword() {
  const password = document.getElementById("password").value;
  const passwordError = document.getElementById("passwordError");

  // Check if the length is between 6 and 20 characters
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

/**
 * Validates the confirm password field.
 *
 * @returns {boolean} - True if the passwords match, otherwise false.
 */
function validateConfirm() {
  const password = document.getElementById("password").value;
  const confirmPassword = document.getElementById("confirm-password").value;
  const confirmError = document.getElementById("confirmError");

  // Check if the passwords match
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

/**
 * Validates the email field.
 *
 * @returns {boolean} - True if the email is valid, otherwise false.
 */
function validateEmail() {
  const email = document.getElementById("email").value;
  const emailError = document.getElementById("emailError");

  // Regular expression to validate email format
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

/**
 * Validates all fields (login name, password, confirm password, and email).
 *
 * @returns {boolean} - True if all fields are valid, otherwise false.
 */
function validate() {
  const isNameValid = validateName();
  const isPasswordValid = validatePassword();
  const isConfirmValid = validateConfirm();
  const isEmailValid = validateEmail();

  return isNameValid && isPasswordValid && isConfirmValid && isEmailValid;
}

// Attach an event listener to the signup form's submit event
document
  .getElementById("signup-form")
  .addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission behavior

    // Collect form data
    const loginName = document.getElementById("login-name").value;
    const password = document.getElementById("password").value;
    const email = document.getElementById("email").value;
    const firstName = document.getElementById("first-name").value;
    const lastName = document.getElementById("last-name").value;

    // Request body for the API
    const requestBody = {
      loginName: loginName,
      password: password,
      email: email,
      firstName: firstName,
      lastName: lastName,
    };

    // Validate fields before sending the request
    if (!validate()) {
      return; // Stop the submission if validation fails
    }

    // API endpoint for user registration
    const apiUrl =
      "http://localhost/web-assignment2/server/userManagement/create_user.php";

    // Send a POST request to the API
    fetch(apiUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json", // Specify the content type
      },
      body: JSON.stringify(requestBody), // Send the form data as JSON
    })
      .then((response) => {
        if (response.ok) {
          return response.json(); // Parse the JSON response
        } else {
          throw new Error("Registration failed"); // Throw an error if the request failed
        }
      })
      .then((data) => {
        // Handle the API response
        if (data.code === 0) {
          alert("Registration successful!");
          window.location.href = "/web-assignment2/pages/index.html"; // Redirect to the login page
        } else {
          alert(`${data.message}`); // Show the error message from the server
        }
      })
      .catch((error) => {
        // Handle network or server errors
        alert(`Error: ${error.message}`);
      });
  });
