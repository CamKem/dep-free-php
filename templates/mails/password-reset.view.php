<html lang="en">
<head>
    <title>Password Reset</title>
</head>

<body>
    <h1>Password Reset</h1>
    <p>To reset your password, click on the following link:</p>
    <span><?= config('app.url') . route('password.reset.edit', compact('token')) ?></span>
    <a href="<?= config('app.url') . route('password.reset.edit', compact('token')) ?>">Reset Password</a>
</body>
</html>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        color: #333;
        margin: 0;
        padding: 0;
    }

    h1 {
        color: #333;
    }

    a {
        color: #007bff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>