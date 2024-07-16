<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBS Kids Episodes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #e0e0e0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #1e1e1e;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        h1 {
            color: #00a2ff; 
            text-align: center;
        }
        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        li {
            margin-bottom: 10px;
        }
        a {
            text-decoration: none;
            color: #e0e0e0;
            background-color: #333333;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        a:hover {
            background-color: #00a2ff; 
            color: #121212; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>PBS Kids Episodes</h1>
        <ul>
            <?php
            include 'config.php';

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            if (isset($_GET['show_title'])) {
                $show_title = $_GET['show_title'];
                $sql = "SELECT title FROM episodes WHERE show_title = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $show_title);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<li><a href=\"details/?episode_title=" . urlencode($row['title']) . "\">" . htmlspecialchars($row['title']) . "</a></li>";
                    }
                } else {
                    echo "<li>No results found</li>";
                }
                $stmt->close();
            } else {
                echo "<li>No show title selected</li>";
            }
            $conn->close();
            ?>
        </ul>
    </div>
</body>
</html>
