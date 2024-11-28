"use strict";
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
  try {
    const response = await fetch(apiUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(body),
    });
    const result = await response.json();
    console.log(result);
    if (response.ok && result.code === 0) {
      // 根据返回数据动态拼接table
      const dataList = result.data;
      async function loadTableData() {
        try {
          // 先清空表格的数据
          dynamicTable.innerHTML = "";

          dataList.forEach((element) => {
            const newRow = dynamicTable.insertRow();
            const checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.classList.add("rowCheckBox");
            // 默认未勾选
            checkbox.value = 0;
            const checkboxCell = newRow.insertCell(0);
            checkboxCell.appendChild(checkbox);
            newRow.insertCell(1).textContent = element.taskName;
            newRow.insertCell(2).textContent = element.dueDate;
            newRow.insertCell(3).textContent = element.priority;
            newRow.insertCell(4).textContent = element.taskStatus;
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

function openTaskWindow() {
  const createWindow = document.getElementById("new-task-modal");
  createWindow.classList.add("active");
}
function openUpdateWindow() {
  const updateWindo = document.getElementById("task-update-modal");
  updateWindo.classList.add("active");
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
