<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PBS Kids Episodes Fetcher</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1f1f1f; 
            color: #ffffff; 
        }
        #insertForm {
            margin: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="submit"] {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            width: 200px;
            background-color: #ffffff; 
            color: #1f1f1f; 
        }
        input[type="submit"] {
            cursor: pointer;
            background-color: #4CAF50; 
            color: white;
            border: none;
        }
        #message {
            margin: 20px;
            padding: 10px;
            background-color: #333333; 
            color: #ffffff;
            border-radius: 4px;
            display: none; 
        }
    </style>
</head>
<body>
    <form id="insertForm" action="fetch_episodes.php" method="POST">
        <label for="page">Enter Page Number:</label>
        <input type="text" id="page" name="page" required>
        <input type="submit" value="Fetch Episodes">
    </form>

    <div id="message"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#insertForm').submit(function (event) {
                event.preventDefault();
                var page = $('#page').val();
                $.ajax({
                    type: 'POST',
                    url: 'fetch_episodes.php',
                    data: {page: page},
                    success: function (response) {
                        $('#message').html(response).fadeIn().delay(3000).fadeOut(); 
                    }
                });
            });
        });
    </script>
</body>
</html>
