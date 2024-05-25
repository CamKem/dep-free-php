<html lang="en">
<head>
    <title>Welcome to <?= config('app.name') ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .email-header {
            background-color: #3eb7ff;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .email-body {
            background-color: #ffffff;
            color: #595959;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 5px 5px;
        }

        .email-body p {
            line-height: 1.5;
        }

        .email-button {
            display: inline-block;
            background-color: #3eb7ff;
            color: #ffffff;
            padding: 10px 20px;
            margin-top: 20px;
            text-decoration: none;
            border-radius: 5px;
        }

        .email-button:hover {
            background-color: #00aced;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <h1>Welcome to <?= config('app.name') ?>, <?= $username ?></h1>
    </div>
    <div class="email-body">
        <p>
            We are excited to have you join our community. Please let us know if you
            have any questions. Enjoy your time with us!
        </p>
        <a href="<?= config('app.url') .route('login.index') ?>" class="email-button">Login</a>
    </div>
</div>
</body>
</html>