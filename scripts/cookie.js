/* 
    JS Comments
    Name: Ke Zhao 041129546
    File Name: cookie.js
    Date Created: 2024-11-25
    Description: This script provides functions to store and retrieve user information using cookies.
*/
"use strict";

/**
 * Sets a cookie with the specified name, value, and expiration days.
 *
 * @param {string} name - The name of the cookie.
 * @param {any} value - The value to store in the cookie. It will be serialized to JSON.
 * @param {number} [days] - The number of days until the cookie expires. If not provided, the cookie will be a session cookie.
 */
function setCookie(name, value, days) {
  // Serialize the value as a JSON string for storage
  const serializedValue = JSON.stringify(value);

  let expires = "";
  if (days) {
    // Calculate the expiration date
    const date = new Date();
    date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000); // Convert days to milliseconds
    expires = "; expires=" + date.toUTCString(); // Format as a UTC string
  }

  // Set the cookie with the specified name, serialized value, and expiration date
  document.cookie =
    name + "=" + encodeURIComponent(serializedValue) + expires + "; path=/";
}

/**
 * Retrieves the value of a cookie with the specified name.
 *
 * @param {string} name - The name of the cookie to retrieve.
 * @returns {any|null} - The parsed value of the cookie, or null if the cookie does not exist.
 */
function getCookie(name) {
  const nameEQ = name + "="; // The prefix to look for in the cookie string
  const cookies = document.cookie.split("; "); // Split all cookies into an array

  // Iterate through all cookies to find the one with the matching name
  for (let cookie of cookies) {
    if (cookie.startsWith(nameEQ)) {
      // Decode the cookie value and parse it as JSON
      return JSON.parse(decodeURIComponent(cookie.substring(nameEQ.length)));
    }
  }

  return null; // Return null if the cookie is not found
}
