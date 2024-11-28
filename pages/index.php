<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="../css/general.css" rel="stylesheet" />
    <link href="../css/index.css" rel="stylesheet" />
    <title>Task Management - Login</title>
  </head>
  <body>
    <header>
      <h1>Task Management</h1>
    </header>
    <main>
      <div class="form-container">
        <h2>Welcome!</h2>
        <form id="login-form">
          <div class="form-row">
            <label for="login-name">Login Name:</label>
            <input type="text" id="login-name" required />
          </div>

          <div class="form-row">
            <label for="password">Password:</label>
            <input type="password" id="password" required />
          </div>

          <div class="button-row">
            <button type="submit">Sign In</button>
            <button
              type="button"
              onclick="window.location.href='registeration.php'"
            >
              Sign Up
            </button>
          </div>
        </form>
        <small id="message"></small>
      </div>
    </main>
    <footer>
      <p>&copy; All rights reserved.</p>
    </footer>
    <script src="../scripts/cookie.js"></script>
    <script src="../scripts/index.js"></script>
  </body>
</html>
