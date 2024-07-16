<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View Database</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #e0e0e0;
            margin: 0;
            padding: 0;
        }
        h1 {
            color: #00FF66;
            text-align: center;
            padding: 20px;
        }
        .admin-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .admin-buttons a {
            padding: 10px 20px;
            background-color: #00FF66;
            color: #121212;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 20px;
        }
        .admin-buttons a:hover {
            background-color: #00cc52;
        }
        .result-container {
            padding: 20px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
        .result-box {
            background-color: #1e1e1e;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 10px;
            width: calc(100% - 40px);
            max-width: 800px;
        }
        .result-box h2 {
            color: #00FF66;
        }
        .result-box p {
            margin: 5px 0;
        }
        .result-box a {
            color: #00FF66;
            text-decoration: none;
            display: block;
        }
        .result-box a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Admin View Database</h1>
    <div class="admin-buttons">
        <a href="/pbskids/admin/view-database/glossary">Glossary</a>
        <a href="/pbskids/admin/view-database/admin-guide">Admin Guide & Policies</a>
    </div>
    <div class="result-container">
        <?php
        include 'config.php';

        if ($conn->connect_error) {
            die("<h2>Connection failed: " . htmlspecialchars($conn->connect_error) . "</h2>");
        }

        $sql = "SELECT show_title, title, description, premiered_on, object_type, duration, kids_mezzannine_16x9, videos_url FROM episodes";
        $result = $conn->query($sql);

        function formatDuration($seconds) {
            $minutes = floor($seconds / 60);
            $seconds = $seconds % 60;
            return sprintf('%d:%02d', $minutes, $seconds);
        }

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='result-box'>";
                echo "<h2>" . htmlspecialchars($row['show_title']) . "</h2>";
                echo "<p><strong>Title:</strong> " . htmlspecialchars($row['title']) . "</p>";
                echo "<p><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
                echo "<p><strong>Premiered On:</strong> " . htmlspecialchars($row['premiered_on']) . "</p>";
                echo "<p><strong>Object Type:</strong> " . htmlspecialchars($row['object_type']) . "</p>";
                echo "<p><strong>Duration:</strong> " . formatDuration($row['duration']) . "</p>";
                echo "<p><strong>Image URL:</strong> <a href='" . htmlspecialchars($row['kids_mezzannine_16x9']) . "' target='_blank'>" . htmlspecialchars($row['kids_mezzannine_16x9']) . "</a></p>";
                echo "<p><strong>Video URL:</strong> " . htmlspecialchars($row['videos_url']) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<div class='result-box'><p>No results found</p></div>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
