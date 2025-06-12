<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Hasher</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type="password"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }
        .output {
            background: #eee;
            padding: 10px;
            word-break: break-all;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Generate Hashed Password</h2>
    <form method="POST">
        <label for="password">Enter Password:</label>
        <input type="password" name="password" id="password" required>
        <input type="submit" value="Generate Hash">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["password"])) {
        $plainPassword = $_POST["password"];
        $hashed = password_hash($plainPassword, PASSWORD_DEFAULT);
        echo "<p><strong>Hashed Password:</strong></p>";
        echo "<div class='output'>" . htmlspecialchars($hashed) . "</div>";
    }
    ?>
</div>

</body>
</html>
