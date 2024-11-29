"use strict";
document.addEventListener("DOMContentLoaded", function () {
  filterAndSearchTasks();
});
// 从cookie中获取当前用户的信息
const currentUser = getCookie("userInfo");
// 获取表格
const dynamicTable = document.getElementById("task-list");
async function filterAndSearchTasks() {
  // 获取优先级复选框的值
  const selectedPriorities = document.querySelectorAll(
    'input[name="priority"]:checked'
  );
  const selectedPriorityValues = Array.from(selectedPriorities).map(
    (checkbox) => checkbox.value
  );
  // 获取状态复选框的值
  // const statusCheckbox = document.getElementById("status-filter");
  const selectedStatus = document.querySelectorAll(
    'input[name="status"]:checked'
  );
  const selectedStatusValues = Array.from(selectedStatus).map(
    (checkbox) => checkbox.value
  );
  // 获取日期
  const selectedDate = document.getElementById("due-date-filter").value;

  const apiUrl =
    "http://localhost/web-assignment2/server/taskManagement/request_task.php";
  const body = {
    userId: currentUser.userId,
    priority: selectedPriorityValues,
    dueDate: selectedDate,
    taskStatus: selectedStatusValues,
  };
  console.log(body);
  try {
    const response = await fetch(apiUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(body),
    });
    const result = await response.json();
    if (response.ok && result.code === 0) {
      // 根据返回数据动态拼接table
      const tableList = result.data;
      async function loadTableData() {
        try {
          // 先清空表格的数据
          dynamicTable.innerHTML = "";

          tableList.forEach((element) => {
            const newRow = dynamicTable.insertRow();
            const checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.classList.add("rowCheckBox");
            // 默认未勾选
            checkbox.value = 0;
            const checkboxCell = newRow.insertCell(0);
            checkboxCell.appendChild(checkbox);
            newRow.insertCell(1).textContent = element.taskId;
            newRow.insertCell(2).textContent = element.taskName;
            newRow.insertCell(3).textContent = element.dueDate;
            newRow.insertCell(4).textContent = element.priority;
            newRow.insertCell(5).textContent = element.taskStatus;

            checkbox.addEventListener("change", () => {
              const selectedTaskId = getTaskId();
              console.log(selectedTaskId);
              const updateButton = document.getElementById("update-button");
              if (selectedTaskId) {
                updateButton.disabled = false;
              } else {
                updateButton.disabled = true;
              }
            });
          });
        } catch (error) {
          console.error("Error fetching data:", error);
          alert("Failed to load data from the backend.");
        }
      }
      loadTableData();
    }
    return false;
  } catch (error) {
    alert(`An error occurred. ${error.message}`);
  }
}
function getSelectedCount(checkboxes) {
  let selectedCount = 0;
  checkboxes.forEach((checkbox) => {
    if (checkbox.checked) {
      selectedCount++;
    }
  });
  return selectedCount;
}
// 获取被选中的任务的任务id
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
function openTaskWindow() {
  const createWindow = document.getElementById("new-task-modal");
  createWindow.classList.add("active");
}
function openUpdateWindow() {
  const updateWindow = document.getElementById("task-update-modal");
  updateWindow.classList.add("active");
}
function closeModal() {
  const windows = document.querySelectorAll(".modal");
  const modalArray = Array.from(windows);
  modalArray.forEach((item) => {
    item.classList.remove("active");
  });
}
function addTask() {
  const taskName = document.getElementById("new-task-name").value;
  const dueDate = document.getElementById("new-due-date").value;
  const priority = document.getElementById("new-priority").value;
  const taskStatus = document.getElementById("new-status").value;
  const userId = currentUser.userId;

  console.log(currentUser);

  const requestBody = {
    taskName: taskName,
    dueDate: dueDate,
    priority: priority,
    taskStatus: taskStatus,
    userId: userId,
  };
  const apiUrl =
    "http://localhost/web-assignment2/server/taskManagement/create_task.php";

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
        throw new Error("Call add task fail");
      }
    })
    .then((data) => {
      if (data.code === 0) {
        alert("Task add successful!");
        // 关闭浮窗
        closeModal();
        // 查询新的数据，刷新页面
        filterAndSearchTasks();
      } else {
        alert(`${data.msg}`);
      }
    });
}
function updateTask() {
  const taskName = document.getElementById("task-name").value;
  const dueDate = document.getElementById("due-date").value;
  const priority = document.getElementById("priority").value;
  const taskStatus = document.getElementById("status").value;
  const userId = currentUser.userId;
  const taskId = getTaskId();
  const requestBody = {
    taskId: taskId,
    taskName: taskName,
    dueDate: dueDate,
    priority: priority,
    taskStatus: taskStatus,
    userId: userId,
  };
  const apiUrl =
    "http://localhost/web-assignment2/server/taskManagement/update_task.php";
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
        throw new Error("Call update task fail");
      }
    })
    .then((data) => {
      if (data.code === 0) {
        alert("Task update successful!");
        // 关闭浮窗
        closeModal();
        // 查询新的数据，刷新页面
        filterAndSearchTasks();
      } else {
        alert(`${data.msg}`);
      }
    });
}
function deleteTask() {
  const taskId = getTaskId();
  const userId = currentUser.userId;
  const requestBody = {
    taskId: taskId,
    userId: userId,
  };
  const apiUrl =
    "http://localhost/web-assignment2/server/taskManagement/delete_task.php";
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
        throw new Error("Call delete task fail");
      }
    })
    .then((data) => {
      if (data.code === 0) {
        alert("Task delete successful!");
        // 关闭浮窗
        closeModal();
        // 查询新的数据，刷新页面
        filterAndSearchTasks();
      } else {
        alert(`${data.message}`);
      }
    });
}
