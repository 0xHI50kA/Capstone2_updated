<?php
session_start();
if(empty($_SESSION['name']))
{
	header('location:index.php');
}
include('header1.php');
include('includes/connection.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Google Calendar Dashboard</title>
  <script src="https://apis.google.com/js/api.js"></script>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Calendar Update</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        .page-wrapper {
            margin-top: 50px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background-color: #f4f4f4;
            min-height: 100vh;
        }

        .calendar-container {
            width: 90%;
            max-width: 1000px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        iframe {
            width: 100%;
            height: 300px;
            border: 0;
            border-radius: 8px;
        }

        .admin-actions {
            margin-top: 20px;
        }

        .admin-actions button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .admin-actions button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>

    <div class="page-wrapper">
        <h1>Event Calendar</h1>
        <br>
        <div class="calendar-container">
            <iframe src="https://calendar.google.com/calendar/embed?src=your_calendar_id@gmail.com&ctz=America/New_York" 
                frameborder="0"></iframe>
            <div class="admin-actions">
                <h3>Update Calendar</h3>
                <button onclick="signInWithGoogle()">Sign In to Google</button>
            </div>
        </div>
    </div>

    <script>
        function signInWithGoogle() {
          window.location.href = "https://calendar.google.com/calendar/u/0/r";
        }
    </script>

</body>

</html>
<?php 
 include('footer.php');
?>