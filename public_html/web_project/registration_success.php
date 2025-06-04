<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful!</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .success-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 90%;
            animation: fadeIn 0.8s ease-in-out;
            position: relative;
            overflow: hidden;
        }

        .success-container::after {
            content: "✓✓✓";
            position: absolute;
            top: -20px;
            left: -20px;
            right: -20px;
            bottom: -20px;
            color: rgba(76, 175, 80, 0.05);
            font-size: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 0;
            pointer-events: none;
        }

        .content {
            position: relative;
            z-index: 1;
        }

        .checkmark {
            color: #4CAF50;
            font-size: 80px;
            margin-bottom: 20px;
            text-shadow: 0 5px 10px rgba(76, 175, 80, 0.3);
        }

        h1 {
            color: #333;
            margin-bottom: 15px;
        }

        p {
            color: #666;
            margin-bottom: 25px;
            font-size: 18px;
            line-height: 1.6;
        }

        .home-link {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
            position: relative;
            overflow: hidden;
        }

        .home-link:hover {
            background-color: #45a049;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }

        .home-link::after {
            content: "→";
            position: absolute;
            right: -20px;
            top: 50%;
            transform: translateY(-50%);
            transition: all 0.3s ease;
        }

        .home-link:hover::after {
            right: 15px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .celebration {
            display: inline-block;
            font-size: 24px;
            animation: bounce 0.5s ease infinite alternate;
        }

        @keyframes bounce {
            to { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
<div class="success-container">
    <div class="content">
        <div class="checkmark">✓</div>
        <h1>Welcome Aboard!</h1>
        <p>Your registration was successful! <span class="celebration">🎉</span><br>
            We're excited to have you join our community. Get ready for an amazing experience!</p>
        <a href="main.php" class="home-link">Go to Home Page</a>
    </div>
</div>
</body>
</html>