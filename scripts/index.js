/* 
    JS Comments
    Name: Ke Zhao 041129546
    File Name: index.js
    Date Created: 2024-11-25
    Description: This script handles the login form operations for index.html, including form submission, user authentication, and error handling.
*/
"use strict";

// Attach an event listener to the login form's submit event
document
  .getElementById("login-form")
  .addEventListener("submit", async function (event) {
    event.preventDefault(); // Prevent the default form submission behavior

    // Get the values of the login name and password fields, and trim any extra spaces
    const loginName = document.getElementById("login-name").value.trim();
    const password = document.getElementById("password").value.trim();
    const messageDiv = document.getElementById("message"); // The element to display messages

    // Clear any previous message
    messageDiv.textContent = "";

    // The backend API URL for authentication
    const apiUrl = "http://localhost/web-assignment2/server/login.php";

    try {
      // Send a POST request to the backend API with the login credentials
      const response = await fetch(apiUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json", // Specify the request content type
        },
        body: JSON.stringify({ loginName, password }), // Send the login data as a JSON string
      });

      // Parse the JSON response from the server
      const result = await response.json();
      console.log(result); // Log the response for debugging purposes

      // Check if the response is successful and the login is valid
      if (response.ok && result.code === 0) {
        // Login successful, save user information in cookies and redirect to the task management page
        setCookie("userInfo", result.data, 3); // Save the user information for 3 days
        window.location.href = "/web-assignment2/pages/task management.html"; // Replace with the target page
      } else {
        // Login failed, display the error message from the server
        messageDiv.textContent = result.message || "Invalid credentials";
        messageDiv.classList.add("error"); // Add a class to style the error message
      }
    } catch (error) {
      // Handle network errors or other exceptions
      messageDiv.textContent = "An error occurred. Please try again later.";
    }
  });
