<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBS Kids Shows</title>
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
        .info-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            padding: 20px;
            flex-wrap: wrap;
        }
        .info-box {
            background-color: #1e1e1e;
            padding: 15px 20px;
            border-radius: 8px;
            text-align: center;
        }
        .info-box h2, .info-box h3 {
            margin: 10px 0;
            color: #00FF66;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
            justify-content: center;
        }
        .grid-item {
            list-style-type: none;
        }
        a {
            text-decoration: none;
            color: #e0e0e0;
            background-color: #333333;
            border: 1px solid #444444;
            padding: 15px 20px;
            border-radius: 5px;
            display: inline-block;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; 
        }
        a:hover {
            background-color: #00FF66;
            color: #121212; 
            border-color: #00FF66; 
        }
    </style>
</head>
<body>
    <h1>PBS Kids Shows</h1>
    <?php
    include 'stats.php';

    $statistics = fetchStatistics($conn); 
    ?>
    <div class="info-container">
        <div class="info-box">
            <h2>Indexed Clips</h2>
            <p><?php echo number_format($statistics['clip_count']); ?></p>
        </div>
        <div class="info-box">
            <h2>Indexed Episodes</h2>
            <p><?php echo number_format($statistics['episode_count']); ?></p>
        </div>
        <div class="info-box">
            <h3>Show with Most Clips</h3>
            <p><?php echo htmlspecialchars($statistics['most_clips_show']['show_title']) . " (" . number_format($statistics['most_clips_show']['clip_count']) . " clips)"; ?></p>
        </div>
        <div class="info-box">
            <h3>Show with Most Episodes</h3>
            <p><?php echo htmlspecialchars($statistics['most_episodes_show']['show_title']) . " (" . number_format($statistics['most_episodes_show']['episode_count']) . " episodes)"; ?></p>
        </div>
        <div class="info-box">
            <h3>Most Visited Show On This Site</h3>
            <p><?php echo htmlspecialchars($statistics['most_popular_show']['show_title']) . " (" . number_format($statistics['most_popular_show']['click_count']) . " visits)"; ?></p>
        </div>
        <div class="info-box">
            <h3>Most Recently Indexed Clip</h3>
            <p><?php echo isset($statistics['recent_clip']['show_title']) ? htmlspecialchars($statistics['recent_clip']['show_title']) . " - " . htmlspecialchars($statistics['recent_clip']['title']) : "No recent clips found"; ?></p>
        </div>
        <div class="info-box">
            <h3>Most Recently Indexed Episode</h3>
            <p><?php echo isset($statistics['recent_episode']['show_title']) ? htmlspecialchars($statistics['recent_episode']['show_title']) . " - " . htmlspecialchars($statistics['recent_episode']['title']) : "No recent episodes found"; ?></p>
        </div>
        <div class="info-box">
            <h3>Oldest Clip</h3>
            <?php if (isset($statistics['oldest_clip']['show_title'])) : ?>
                <p><?php echo htmlspecialchars($statistics['oldest_clip']['show_title']) . " - " . htmlspecialchars($statistics['oldest_clip']['title']) . " (" . date('Y-m-d', strtotime($statistics['oldest_clip']['premiered_on'])) . ")"; ?></p>
            <?php else : ?>
                <p>No oldest clip found</p>
            <?php endif; ?>
        </div>
        <div class="info-box">
            <h3>Oldest Episode</h3>
            <?php if (isset($statistics['oldest_episode']['show_title'])) : ?>
                <p><?php echo htmlspecialchars($statistics['oldest_episode']['show_title']) . " - " . htmlspecialchars($statistics['oldest_episode']['title']) . " (" . date('Y-m-d', strtotime($statistics['oldest_episode']['premiered_on'])) . ")"; ?></p>
            <?php else : ?>
                <p>No oldest episode found</p>
            <?php endif; ?>
        </div>
        <div class="info-box">
            <h3>Longest Episode</h3>
            <p><?php echo isset($statistics['longest_episode']['show_title']) ? htmlspecialchars($statistics['longest_episode']['show_title']) . " - " . htmlspecialchars($statistics['longest_episode']['title']) : "No longest episode found"; ?></p>
        </div>
        <div class="info-box">
            <h3>Shortest Episode</h3>
            <p><?php echo isset($statistics['shortest_episode']['show_title']) ? htmlspecialchars($statistics['shortest_episode']['show_title']) . " - " . htmlspecialchars($statistics['shortest_episode']['title']) : "No shortest episode found"; ?></p>
        </div>
        <div class="info-box">
            <h3>Longest Clip</h3>
            <p><?php echo isset($statistics['longest_clip']['show_title']) ? htmlspecialchars($statistics['longest_clip']['show_title']) . " - " . htmlspecialchars($statistics['longest_clip']['title']) : "No longest clip found"; ?></p>
        </div>
        <div class="info-box">
            <h3>Shortest Clip</h3>
            <p><?php echo isset($statistics['shortest_clip']['show_title']) ? htmlspecialchars($statistics['shortest_clip']['show_title']) . " - " . htmlspecialchars($statistics['shortest_clip']['title']) : "No shortest clip found"; ?></p>
        </div>
    </div>
    <ul class="grid-container">
        <?php
        $stmt_shows = $conn->prepare("SELECT DISTINCT show_title FROM episodes");
        $stmt_shows->execute();
        $result_shows = $stmt_shows->get_result();

        if ($result_shows->num_rows > 0) {
            while ($row = $result_shows->fetch_assoc()) {
                echo "<li class='grid-item'><a href=\"track_click.php?show_title=" . urlencode($row['show_title']) . "\">" . htmlspecialchars($row['show_title']) . "</a></li>";
            }
        } else {
            echo "<li class='grid-item'>No results found</li>";
        }

        $conn->close();
        ?>
    </ul>

    <?php include 'footer.php'; ?>
</body>
</html>
