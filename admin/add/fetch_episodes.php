<?php
function parse_date($date_str) {
    return date_create_from_format('Y-m-d', $date_str);
}

function get_http_location($url) {
    $headers = get_headers($url, 1);
    if (isset($headers['Location'])) {
        return $headers['Location'];
    }
    return null;
}

function fetch_and_save_episodes($page) {
$servername = "localhost";
$username = "Database_Username_Here";
$password = "Database_Password_Here";
$dbname = "Database_Name_Here";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt_check = $conn->prepare("SELECT id FROM episodes WHERE title = ?");
    $stmt_insert = $conn->prepare("INSERT INTO episodes (title, description, object_type, kids_mezzannine_16x9, videos_url, show_title, premiered_on, duration) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $progress = '';

    $url = "https://producerplayer.services.pbskids.org/show-list?shows=almas-way%2Carthur%2Ccet-parents%2Ccetthinktv-education%2Ccity-island%2Cclifford-big-red-dog%2Ccliffords-puppy-days%2Ccrafts-kids%2Ccurious-george%2Ccyberchase%2Cdaniel-tigers-neighborhood%2Cdesign-squad-nation%2Cdinosaur-train%2Cdonkey-hodie%2Cdots-spot%2Celinor-wonders-why%2Cfetch-with-ruff-ruffman%2Cfamily-math%2Cfamily-math-en-espanol%2Cfizzys-lunch-lab%2Chero-elementary%2Cjamming-on-the-job%2Cjelly-ben-pogo%2Ckeyshawn-solves-it%2Clets-go-luna%2Clets-talk%2Cliteracy-tips-all-ages%2Clyla-loop%2Cmartha-speaks%2Cmaya-miguel%2Cmecha-builders%2Cmeet-helpers%2Cmega-wow%2Cmilo%2Cmister-rogers%2Cmolly-of-denali%2Cnature-cat%2Codd-squad%2Codd-tube%2Coh-noah%2Cpbs-kids%2Cpbs-kids-rocks%2Cpbs-kids-talk-about%2Cpbs-kids-activity-challenge%2Cpbs-kids-get-moving%2Cwestern-reserve-public-media-educational-productions%2Cparent-hacks%2Cparentalogic%2Cparenting-minutes%2Cpeg%2Cpinkalicious-and-peterrific%2Cplum-landing%2Cready-jet-go%2Crocket-saves-the-day%2Crosies-rules%2Csuper-why%2Cscigirls%2Cscribbles-and-ink%2Csearch-it-up%2Csesame-street%2Csid-science-kid%2Csplash-and-bubbles%2Csuper-whys-comic-book-adventures%2Ccat-in-the-hat%2Cnot-too-late-show-elmo%2Cruff-ruffman-show%2Cthrough-woods%2Ctiny-time-travel%2Cwosu-specials%2Cwhat-can-you-become%2Cwhats-good%2Cwild-kratts%2Cword-world%2Cword-of-the-week%2Cwordgirl%2Cwork-it-out-wombats%2Cxavier-riddle-and-secret-museum%2Cyou-me-community%2Ciq-smartparent&page=$page";

    $response = file_get_contents($url);
    if ($response === FALSE) {
        $progress .= "Error fetching data from API for page $page. Skipping.<br>";
    } else {
        $data = json_decode($response, true);
        $episodes = $data['items'];

        foreach ($episodes as $episode) {
            $title = $episode['title'];
            $description = $episode['description_long'];
            $object_type = $episode['object_type'];
            $kids_mezzannine_16x9 = $episode['images']['kids-mezzannine-16x9']['url'];
            $video_url = $episode['videos'][0]['url'];
            $show_title = $episode['series_title'];
            $premiered_on = $episode['premiered_on'];
            $duration = $episode['duration'];

            if (strpos(strtolower($video_url), '.m3u8') !== false) {
                $progress .= "Video URL is an m3u8 format for episode: $title. Skipping.<br>";
                continue;
            }

            $http_location = get_http_location($video_url);

            if ($http_location === null || strpos(strtolower($http_location), '.m3u8') !== false) {
                $progress .= "Cannot get valid MP4 HTTP location header for episode: $title. Skipping.<br>";
                continue;
            }

            $stmt_check->bind_param("s", $title);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows == 0) {
                $stmt_insert->bind_param("sssssssi", $title, $description, $object_type, $kids_mezzannine_16x9, $http_location, $show_title, $premiered_on, $duration);

                if ($stmt_insert->execute() === FALSE) {
                    $progress .= "Error inserting episode: " . $stmt_insert->error . "<br>";
                } else {
                    $progress .= "Inserted episode: $title<br>";
                }
            } else {
                $progress .= "Skipped duplicate episode: $title<br>";
            }

            usleep(50000); 

        }
    }

    $stmt_check->close();
    $stmt_insert->close();
    $conn->close();

    return $progress;
}

if (isset($_POST['page'])) {
    $page = $_POST['page'];
    $progress = fetch_and_save_episodes($page);
    echo $progress;
}
?>
