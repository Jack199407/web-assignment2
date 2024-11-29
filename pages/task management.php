<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Task Management - Tasks</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
      rel="stylesheet"
    />
    <link href="../css/general.css" rel="stylesheet" />
    <link href="../css/task-management.css" rel="stylesheet" />
  </head>
  <body>
    <header>
      <h1>Task Management</h1>
    </header>
    <main>
      <div class="tasks-container">
        <div class="filter-container">
          <div class="filter-group-wrapper">
            <h3>Priority</h3>
            <div id="priority-filter" class="filter-group">
              <label
                ><input type="checkbox" name="priority" value="0" /> High</label
              >
              <label
                ><input type="checkbox" name="priority" value="1" />
                Middle</label
              >
              <label
                ><input type="checkbox" name="priority" value="2" /> Low</label
              >
            </div>
          </div>

          <div class="filter-group-wrapper">
            <h3>Status</h3>
            <div id="status-filter" class="filter-group">
              <label
                ><input type="checkbox" name="status" value="0" /> To Do</label
              >
              <label
                ><input type="checkbox" name="status" value="1" /> In
                Progress</label
              >
              <label
                ><input type="checkbox" name="status" value="2" />
                Completed</label
              >
              <label
                ><input type="checkbox" name="status" value="3" /> Paused</label
              >
              <label
                ><input type="checkbox" name="status" value="4" />
                Cancelled</label
              >
            </div>
          </div>

          <div class="date-search-container">
            <i class="fas fa-search search-icon"></i>
            <input type="date" id="due-date-filter" />
            <button id="btn-search" onclick="filterAndSearchTasks()">
              Search
            </button>
          </div>
        </div>

        <table>
          <thead>
            <tr>
              <th>Select</th>
              <th>Task Name</th>
              <th>Due Date</th>
              <th>Priority</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="task-list">
            <!-- 动态生成内容将插入到这里 -->
          </tbody>
        </table>
      </div>
      <div class="button-container">
        <button onclick="openTaskWindow()">New Task</button>
        <button onclick="openUpdateWindow()" disabled>Update</button>
      </div>
    </main>

    <div id="new-task-modal" class="modal">
      <div class="modal-content">
        <!-- Close button -->
        <button class="close-button" onclick="closeModal()">×</button>

        <h3 id="new-modal-title">New Task</h3>
        <form id="new-task-form">
          <label for="task-name">Task Name:</label>
          <input type="text" id="new-task-name" required />

          <label for="due-date">Due Date:</label>
          <input type="date" id="new-due-date" required />

          <label for="priority">Priority:</label>
          <select id="new-priority" required>
            <option value="0">High</option>
            <option value="1">Middle</option>
            <option value="2">Low</option></select
          >

          <label for="status">Status:</label>
          <select id="new-status" required>
            <option value="0">To Do</option>
            <option value="1">In Progress</option>
            <option value="2">Completed</option>
            <option value="3">Paused</option>
            <option value="4">cancelled</option></select
          >

          <div class="modal-button-container">
            <button
              type="button"
              class="btn-add"
              id="add-button"
              onclick="addTask()"
            >
              Add
            </button>
            <button type="reset" class="btn-reset">Reset</button>
          </div>
        </form>
      </div>
    </div>

    <div id="task-update-modal" class="modal">
      <div class="modal-content">
        <!-- Close button -->
        <button class="close-button" onclick="closeModal()">×</button>

        <h3 id="modal-title">Task Details</h3>
        <form id="task-form">
          <label for="task-name">Task Name:</label>
          <input type="text" id="task-name" required />

          <label for="due-date">Due Date:</label>
          <input type="date" id="due-date" required />

          <label for="priority">Priority:</label>
          <select id="priority" required>
            <option value="0">High</option>
            <option value="1">Middle</option>
            <option value="2">Low</option></select
          >

          <label for="status">Status:</label>
          <select id="status" required>
            <option value="0">To Do</option>
            <option value="1">In Progress</option>
            <option value="2">Completed</option>
            <option value="3">Paused</option>
            <option value="4">cancelled</option></select
          >

          <div class="modal-button-container">
            <button type="submit" id="submit-button" class="btn-update">
              Save
            </button>
            <button
              type="button"
              class="btn-delete"
              id="delete-button"
              onclick="deleteTask()"
            >
              Delete
            </button>
            <button type="reset" class="btn-reset">Reset</button>
          </div>
        </form>
      </div>
    </div>

    <footer>
      <p>&copy; All rights reserved.</p>
    </footer>
    <script src="../scripts/cookie.js"></script>
    <script src="../scripts/management.js"></script>
  </body>
</html>
