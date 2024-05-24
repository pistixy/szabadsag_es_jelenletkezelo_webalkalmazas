<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collapsible Sidebar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: #333;
            color: white;
            height: 100vh;
            position: fixed;
            transition: width 0.3s;
            overflow-x: hidden;
            padding-top: 20px;
        }

        .sidebar.collapsed {
            width: 0;
            padding-top: 0;
            overflow: hidden;
        }

        .sidebar a {
            padding: 15px 20px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
            width: 100%;
        }

        .main-content.collapsed {
            margin-left: 0;
        }

        .toggle-btn {
            position: absolute;
            top: 20px;
            left: 300px;
            z-index: 1;
            font-size: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <a href="#">Home</a>
    <a href="#">Services</a>
    <a href="#">Clients</a>
    <a href="#">Contact</a>
</div>

<div class="main-content" id="main-content">
    <span class="toggle-btn" onclick="toggleSidebar()">☰</span>
    <h2>Main Content</h2>
    <p>This is the main content area.</p>
</div>

<script>
    function toggleSidebar() {
        var sidebar = document.getElementById('sidebar');
        var mainContent = document.getElementById('main-content');
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
    }
</script>

</body>
</html>
