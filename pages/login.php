<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Inter&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/main.css">
    <title>Login</title>
</head>

<body>
    <header>
        <h1>Nova Schola</h1>
        <div>Mabini Ave, Brgy. Sambat, Tanauan City<br>
            (043) 702-6867 inquiry@ntcbatangas.edu.ph</div>
    </header>
    <div id="loginBody">
        <img src="../images/login.png" alt="" id="loginBg">
        <div id="loginForm">
            <form action="../backend/login.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <br>
                <label for="password">Password:</label>
                <div><input type="password" id="password" name="password" required>
                <input type="checkbox" id="togglePassword"></div>
                <input type="submit" value="Login" class="button">
            </form>
        </div>
    </div>
</body>
<script>
    document.getElementById('togglePassword').addEventListener('change', function() {
        var passwordInput = document.getElementById('password');
        if (this.checked) {
            passwordInput.setAttribute('type', 'text'); // Change to text
        } else {
            passwordInput.setAttribute('type', 'password'); // Change back to password
        }
    });
</script>
