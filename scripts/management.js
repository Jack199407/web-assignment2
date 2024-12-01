/* 
    JS Comments
    Name: Ke Zhao 041129546
    File Name: management.js
    Date Created: 2024-11-25
    Description: This script handles task management operations for task management.html, including filtering, searching, creating, updating, and deleting tasks.
*/
"use strict";

// Ensure the DOM is fully loaded before attaching event handlers
document.addEventListener("DOMContentLoaded", function () {
  filterAndSearchTasks(); // Initialize task list filtering and searching
});

// Get the current user's information from cookies
const currentUser = getCookie("userInfo");

// Get the dynamic task list table
const dynamicTable = document.getElementById("task-list");

/**
 * Filters and searches tasks based on user input (priority, status, and due date).
 * Sends a request to the server and dynamically updates the task table.
 */
async function filterAndSearchTasks() {
  // Get selected priority values from checkboxes
  const selectedPriorities = document.querySelectorAll(
    'input[name="priority"]:checked'
  );
  const selectedPriorityValues = Array.from(selectedPriorities).map(
    (checkbox) => checkbox.value
  );

  // Get selected status values from checkboxes
  const selectedStatus = document.querySelectorAll(
    'input[name="status"]:checked'
  );
  const selectedStatusValues = Array.from(selectedStatus).map(
    (checkbox) => checkbox.value
  );

  // Get the selected due date from the date picker
  const selectedDate = document.getElementById("due-date-filter").value;

  // Define the API URL for fetching tasks
  const apiUrl =
    "http://localhost/web-assignment2/server/taskManagement/request_task.php";

  // Create the request body
  const body = {
    userId: currentUser.userId, // Current user's ID
    priority: selectedPriorityValues, // Selected priorities
    dueDate: selectedDate, // Selected due date
    taskStatus: selectedStatusValues, // Selected statuses
  };

  console.log(body); // Debug: Log the request body

  try {
    // Send a POST request to the server
    const response = await fetch(apiUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(body),
    });

    // Parse the response JSON
    const result = await response.json();

    // If the request is successful, update the task table
    if (response.ok && result.code === 0) {
      const tableList = result.data;

      // Dynamically load data into the task table
      async function loadTableData() {
        try {
          // Clear the existing table rows
          dynamicTable.innerHTML = "";

          // Populate the table with the new data
          tableList.forEach((element) => {
            const newRow = dynamicTable.insertRow();

            // Add a checkbox to each row
            const checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.classList.add("rowCheckBox");
            checkbox.value = 0; // Default unchecked
            const checkboxCell = newRow.insertCell(0);
            checkboxCell.appendChild(checkbox);

            // Populate the table cells with task data
            newRow.insertCell(1).textContent = element.taskId;
            newRow.insertCell(2).textContent = element.taskName;
            newRow.insertCell(3).textContent = element.dueDate;
            newRow.insertCell(4).textContent = element.priority;
            newRow.insertCell(5).textContent = element.taskStatus;

            // Enable or disable the update button based on the selected task
            checkbox.addEventListener("change", () => {
              const selectedTaskId = getTaskId();
              console.log(selectedTaskId);
              const updateButton = document.getElementById("update-button");
              updateButton.disabled = !selectedTaskId;
            });
          });
        } catch (error) {
          console.error("Error fetching data:", error);
          alert("Failed to load data from the backend.");
        }
      }
      loadTableData();
    }
  } catch (error) {
    alert(`An error occurred. ${error.message}`);
  }
}

/**
 * Gets the count of selected checkboxes.
 *
 * @param {NodeList} checkboxes - The list of checkboxes to count.
 * @returns {number} The count of selected checkboxes.
 */
function getSelectedCount(checkboxes) {
  let selectedCount = 0;
  checkboxes.forEach((checkbox) => {
    if (checkbox.checked) {
      selectedCount++;
    }
  });
  return selectedCount;
}

/**
 * Gets the task ID of the selected task.
 *
 * @returns {string|boolean} The task ID if exactly one task is selected, otherwise false.
 */
function getTaskId() {
  const checkboxes = document.querySelectorAll(".rowCheckBox");
  if (getSelectedCount(checkboxes) !== 1) {
    return false;
  }
  for (let checkbox of checkboxes) {
    if (checkbox.checked) {
      const row = checkbox.closest("tr");
      const taskId = row.cells[1].textContent;
      return taskId;
    }
  }
  return false;
}

/**
 * Opens the modal window for creating a new task.
 */
function openTaskWindow() {
  const createWindow = document.getElementById("new-task-modal");
  createWindow.classList.add("active");
}

/**
 * Opens the modal window for updating a task and pre-fills it with the task's data.
 */
function openUpdateWindow() {
  const updateWindow = document.getElementById("task-update-modal");
  const checkboxes = document.querySelectorAll(".rowCheckBox");
  for (let checkbox of checkboxes) {
    if (checkbox.checked) {
      const row = checkbox.closest("tr");
      document.getElementById("task-name").value = row.cells[2].textContent;
      document.getElementById("due-date").value = row.cells[3].textContent;
      document.getElementById("priority").value = mapPriorityValue(
        row.cells[4].textContent
      );
      document.getElementById("status").value = mapStatusValue(
        row.cells[5].textContent
      );
    }
  }
  updateWindow.classList.add("active");
}

/**
 * Maps priority text to its corresponding value.
 */
function mapPriorityValue(priorityText) {
  switch (priorityText.toLowerCase()) {
    case "high":
      return "0";
    case "middle":
      return "1";
    case "low":
      return "2";
    default:
      return "";
  }
}

/**
 * Maps status text to its corresponding value.
 */
function mapStatusValue(statusText) {
  switch (statusText.toLowerCase()) {
    case "to do":
      return "0";
    case "in progress":
      return "1";
    case "completed":
      return "2";
    case "paused":
      return "3";
    case "cancelled":
      return "4";
    default:
      return "";
  }
}

/**
 * Closes all modal windows.
 */
function closeModal() {
  const windows = document.querySelectorAll(".modal");
  windows.forEach((item) => {
    item.classList.remove("active");
  });
}

// Other functions (addTask, updateTask, deleteTask) remain similar with added comments to clarify their logic.
