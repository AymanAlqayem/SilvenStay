<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SilvenStay For Flat Rent</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<section class="index-container">
    <main>
        <section class="index-profile-section">
            <figure class="index-profile-photo">
                <img src="images/Ayman1.jpg" alt="My Photo">
            </figure>
            <section class="index-profile-info">
                <h2>Student Information</h2>
                <p><strong>Name:</strong> Ayman Alqayem</p>
                <p><strong>ID:</strong> 1220040</p>
                <a href="main.php" class="index-btn">Go to Main Page</a>
            </section>
        </section>

        <section class="index-info-section">
            <h3>Database Information</h3>
            <table class="index-styled-table">
                <tr>
                    <th>Database Name</th>
                    <td>web1220040_silvenstaydb.sql</td>
                </tr>
                <tr>
                    <th>Database User</th>
                    <td>web1220040_proj1220040</td>
                </tr>
                <tr>
                    <th>Database Password</th>
                    <td>pass1220040</td>
                </tr>
            </table>
        </section>

        <section class="index-info-section">
            <h3>Test Users</h3>
            <table class="index-styled-table">
                <thead>
                <tr>
                    <th>User Type</th>
                    <th>Username</th>
                    <th>Password</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Manager</td>
                    <td>alice@gmail.com</td>
                    <td>1Passwqz</td>
                </tr>
                <tr>
                    <td>Owner</td>
                    <td>sofia@gmail.com</td>
                    <td>3Bankingz</td>
                </tr>
                <tr>
                    <td>Customer</td>
                    <td>emily@gmail.com</td>
                    <td>4Clientx</td>
                </tr>
                </tbody>
            </table>
        </section>
    </main>
</section>
<?php include 'footer.php'; ?>
</body>
</html>