<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            display: flex;
            min-height: 100vh;
            margin: 0;
            flex-direction: column;
        }

        .admin-header {
            background: #007BFF;
            color: #fff;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-links a, .user-menu a {
            color: #fff;
            margin: 0 10px;
            text-decoration: none;
        }

        .admin-sidebar {
            background: #343a40;
            color: #fff;
            padding: 1rem;
            width: 200px;
            flex-shrink: 0;
        }

        .admin-sidebar ul {
            list-style: none;
            padding: 0;
        }

        .admin-sidebar li {
            margin: 10px 0;
        }

        .admin-sidebar a {
            color: #fff;
            text-decoration: none;
        }

        .admin-content {
            flex-grow: 1;
            padding: 1rem;
        }

        .breadcrumbs {
            margin-bottom: 1rem;
        }

        .admin-footer {
            background: #f8f9fa;
            padding: 1rem;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }

        .admin-footer a {
            margin: 0 10px;
            text-decoration: none;
        }

    </style>
</head>
<body>
<div class="admin-header">
    <div class="logo">Sports Warehouse Admin</div>
    <div class="nav-links">
        <a href="#">Dashboard</a>
        <a href="#">Users</a>
        <a href="#">Products</a>
        <a href="#">Orders</a>
        <a href="#">Reports</a>
        <a href="#">Settings</a>
    </div>
    <div class="user-menu">
        <span>Admin</span>
        <a href="#">Logout</a>
    </div>
</div>
<div class="admin-sidebar">
    <ul>
        <li><a href="#">User Management</a></li>
        <li><a href="#">Product Management</a></li>
        <li><a href="#">Order Management</a></li>
        <li><a href="#">Reports</a></li>
        <li><a href="#">Settings</a></li>
    </ul>
</div>
<div class="admin-content">
    <div class="breadcrumbs">Home > Dashboard</div>
    <h1>Dashboard</h1>
    <div class="dashboard-widgets">
        {{ slot }}
    </div>
</div>
<div class="admin-footer">
    <p>&copy; 2024 Sports Warehouse. All rights reserved.</p>
    <a href="#">Support</a>
    <a href="#">Privacy Policy</a>
</div>
</body>
</html>

