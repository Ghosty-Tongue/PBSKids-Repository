<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Episode Details</title>
    <link href="https://vjs.zencdn.net/7.15.4/video-js.css" rel="stylesheet" />
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
        h1, h2 {
            color: #00a2ff;
            text-align: center;
        }
        p {
            color: #e0e0e0;
            margin-bottom: 20px;
        }
        video {
            width: 100%;
            height: auto;
            border-radius: 8px;
            max-width: 100%;
            max-height: 100%;
            object-fit: contain; 
        }
        .direct-link {
            display: block;
            background-color: #333333;
            border: none;
            color: #e0e0e0;
            padding: 10px;
            margin: 20px auto;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            width: fit-content;
        }
        .direct-link:hover {
            background-color: #00a2ff;
            color: #121212;
        }
        .how-to-play {
            text-align: center;
            margin-top: 10px;
            font-style: italic;
            color: #aaa;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Episode Details</h1>
        <?php
        if (isset($_GET['episode_title']) && !empty($_GET['episode_title'])) {
            $episode_title = $_GET['episode_title'];

            include 'config.php'; 

            $sql = "SELECT DISTINCT title, description, videos_url, premiered_on, duration, kids_mezzannine_16x9 FROM episodes WHERE title = ?";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("s", $episode_title);
                
                $stmt->execute();
                
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $found_episodes = [];
                    
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        if (in_array($row['title'], $found_episodes)) {
                            continue; 
                        }
                        
                        $found_episodes[] = $row['title'];
                        
                        echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
                        echo "<p>" . htmlspecialchars($row['description']) . "</p>";
                        
                        if (!empty($row['premiered_on'])) {
                            $premiered_date = new DateTime($row['premiered_on']);
                            $now = new DateTime();
                            $interval = $now->diff($premiered_date);
                            
                            if ($interval->y == 0 && $interval->m == 0 && $interval->d < 31) {
                                $days_ago = $interval->format('%a');
                                $weeks_ago = floor($days_ago / 7);
                                
                                if ($weeks_ago > 0) {
                                    echo "<p><strong>Premiered on:</strong> " . $premiered_date->format('F j, Y') . "</p>";
                                    echo "<p><strong>Time Since Premiere:</strong> " . $weeks_ago . ($weeks_ago == 1 ? " week" : " weeks") . " and " . ($days_ago % 7) . ($days_ago % 7 == 1 ? " day" : " days") . " ago</p>";
                                } else {
                                    echo "<p><strong>Premiered on:</strong> " . $premiered_date->format('F j, Y') . "</p>";
                                    echo "<p><strong>Time Since Premiere:</strong> " . $days_ago . ($days_ago == 1 ? " day" : " days") . " ago</p>";
                                }
                            } else {
                                $premiered_human_readable = $premiered_date->format('F j, Y');
                                $premiered_ago = $interval->format('%y years, %m month ago');
                                echo "<p><strong>Premiered on:</strong> " . $premiered_human_readable . "</p>";
                                echo "<p><strong>Time Since Premiere:</strong> " . $premiered_ago . "</p>";
                            }
                        }
                        
                        if (!empty($row['duration'])) {
                            $duration_seconds = $row['duration'];
                            $duration = gmdate("H:i:s", $duration_seconds); 
                            
                            $formatted_duration = ltrim($duration, '0:');
                            
                            echo "<p><strong>Duration:</strong> " . $formatted_duration . "</p>";
                        }
                        
                        if (!empty($row['videos_url'])) {
                            echo '<video id="videoPlayer" class="video-js vjs-default-skin" controls preload="auto" data-setup=\'{"fluid": true}\' poster="' . htmlspecialchars($row['kids_mezzannine_16x9']) . '">' .
                                 '<source src="' . htmlspecialchars($row['videos_url']) . '" type="video/mp4">' .
                                 'Your browser does not support the video tag.' .
                                 '</video>';
                            echo '<div class="how-to-play">Click the play button to start watching the episode.</div>';
                            echo '<a href="' . htmlspecialchars($row['videos_url']) . '" id="downloadButton" download class="direct-link">Direct Video Link</a>';
                        }
                    }
                } else {
                    echo "<p>No video found for this episode.</p>";
                }
                
                $stmt->close();
            } else {
                echo "<p>Failed to prepare the SQL statement.</p>";
            }
            
            $conn->close();
        } else {
            echo "<p>No episode title specified.</p>";
        }
        ?>
    </div>

    <script src="https://vjs.zencdn.net/7.15.4/video.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var player = videojs('videoPlayer');
            player.on('error', function() {
                document.querySelector('.container').innerHTML = "<p>Sorry, this episode has been removed from the official PBS Kids site and is no longer accessible.</p>";
            });
        });
    </script>
</body>

</html>
