<?php
include 'config.php';

function fetchStatistics($conn) {
    $statistics = array();

    $stmt_clips = $conn->prepare("SELECT COUNT(*) AS clip_count FROM episodes WHERE object_type = 'clip'");
    $stmt_clips->execute();
    $result_clips = $stmt_clips->get_result();
    $statistics['clip_count'] = $result_clips->fetch_assoc()['clip_count'];

    $stmt_episodes = $conn->prepare("SELECT COUNT(*) AS episode_count FROM episodes WHERE object_type = 'full_length'");
    $stmt_episodes->execute();
    $result_episodes = $stmt_episodes->get_result();
    $statistics['episode_count'] = $result_episodes->fetch_assoc()['episode_count'];

    $stmt_most_clips = $conn->prepare("SELECT show_title, COUNT(*) AS clip_count FROM episodes WHERE object_type = 'clip' GROUP BY show_title ORDER BY clip_count DESC LIMIT 1");
    $stmt_most_clips->execute();
    $result_most_clips = $stmt_most_clips->get_result();
    $statistics['most_clips_show'] = $result_most_clips->fetch_assoc();

    $stmt_most_episodes = $conn->prepare("SELECT show_title, COUNT(*) AS episode_count FROM episodes WHERE object_type = 'full_length' GROUP BY show_title ORDER BY episode_count DESC LIMIT 1");
    $stmt_most_episodes->execute();
    $result_most_episodes = $stmt_most_episodes->get_result();
    $statistics['most_episodes_show'] = $result_most_episodes->fetch_assoc();

    $stmt_most_popular = $conn->prepare("SELECT show_title, click_count FROM show_clicks ORDER BY click_count DESC LIMIT 1");
    $stmt_most_popular->execute();
    $result_most_popular = $stmt_most_popular->get_result();
    if ($result_most_popular->num_rows > 0) {
        $statistics['most_popular_show'] = $result_most_popular->fetch_assoc();
    } else {
        $statistics['most_popular_show'] = array('show_title' => 'No one has visited a show yet', 'click_count' => '-');
    }

    $stmt_recent_clip = $conn->prepare("SELECT show_title, title, premiered_on FROM episodes WHERE object_type = 'clip' ORDER BY id DESC LIMIT 1");
    $stmt_recent_clip->execute();
    $result_recent_clip = $stmt_recent_clip->get_result();
    $statistics['recent_clip'] = $result_recent_clip->fetch_assoc();

    $stmt_recent_episode = $conn->prepare("SELECT show_title, title, premiered_on FROM episodes WHERE object_type = 'full_length' ORDER BY id DESC LIMIT 1");
    $stmt_recent_episode->execute();
    $result_recent_episode = $stmt_recent_episode->get_result();
    $statistics['recent_episode'] = $result_recent_episode->fetch_assoc();

    $stmt_oldest_clip = $conn->prepare("SELECT show_title, title, premiered_on FROM episodes WHERE object_type = 'clip' AND premiered_on IS NOT NULL AND premiered_on > '1995-01-01' ORDER BY premiered_on ASC LIMIT 1");
    $stmt_oldest_clip->execute();
    $result_oldest_clip = $stmt_oldest_clip->get_result();
    $oldest_clip = $result_oldest_clip->fetch_assoc();

    if ($oldest_clip && isset($oldest_clip['premiered_on'])) {
        $premiere_date = new DateTime($oldest_clip['premiered_on']);
        $now = new DateTime();
        $interval = $premiere_date->diff($now);
        $statistics['oldest_clip'] = $oldest_clip;
        $statistics['oldest_clip']['premiere_ago'] = formatDateDiff($interval);
    } else {
        $statistics['oldest_clip'] = null;
    }

    $stmt_oldest_episode = $conn->prepare("SELECT show_title, title, premiered_on FROM episodes WHERE object_type = 'full_length' AND premiered_on IS NOT NULL AND premiered_on > '1995-01-01' ORDER BY premiered_on ASC LIMIT 1");
    $stmt_oldest_episode->execute();
    $result_oldest_episode = $stmt_oldest_episode->get_result();
    $oldest_episode = $result_oldest_episode->fetch_assoc();

    if ($oldest_episode && isset($oldest_episode['premiered_on'])) {
        $premiere_date = new DateTime($oldest_episode['premiered_on']);
        $now = new DateTime();
        $interval = $premiere_date->diff($now);
        $statistics['oldest_episode'] = $oldest_episode;
        $statistics['oldest_episode']['premiere_ago'] = formatDateDiff($interval);
    } else {
        $statistics['oldest_episode'] = null;
    }

    $stmt_longest_clip = $conn->prepare("SELECT show_title, title, duration FROM episodes WHERE object_type = 'clip' ORDER BY duration DESC LIMIT 1");
    $stmt_longest_clip->execute();
    $result_longest_clip = $stmt_longest_clip->get_result();
    $statistics['longest_clip'] = $result_longest_clip->fetch_assoc();

    $stmt_shortest_clip = $conn->prepare("SELECT show_title, title, duration FROM episodes WHERE object_type = 'clip' ORDER BY duration ASC LIMIT 1");
    $stmt_shortest_clip->execute();
    $result_shortest_clip = $stmt_shortest_clip->get_result();
    $statistics['shortest_clip'] = $result_shortest_clip->fetch_assoc();

    $stmt_longest_episode = $conn->prepare("SELECT show_title, title, duration FROM episodes WHERE object_type = 'full_length' ORDER BY duration DESC LIMIT 1");
    $stmt_longest_episode->execute();
    $result_longest_episode = $stmt_longest_episode->get_result();
    $statistics['longest_episode'] = $result_longest_episode->fetch_assoc();

    $stmt_shortest_episode = $conn->prepare("SELECT show_title, title, duration FROM episodes WHERE object_type = 'full_length' ORDER BY duration ASC LIMIT 1");
    $stmt_shortest_episode->execute();
    $result_shortest_episode = $stmt_shortest_episode->get_result();
    $statistics['shortest_episode'] = $result_shortest_episode->fetch_assoc();

    return $statistics;
}

function formatDateDiff($interval) {
    if ($interval->y > 0) {
        return $interval->format("%y years ago");
    } elseif ($interval->m > 0) {
        return $interval->format("%m months ago");
    } elseif ($interval->d > 0) {
        return $interval->format("%d days ago");
    } elseif ($interval->h > 0) {
        return $interval->format("%h hours ago");
    } elseif ($interval->i > 0) {
        return $interval->format("%i minutes ago");
    } else {
        return $interval->format("%s seconds ago");
    }
}

?>
