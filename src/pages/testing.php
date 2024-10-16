<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Absence Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #218838;
        }
        .output {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Generate Absence Details</h1>
    <form id="generateForm" method="post">
        <button type="submit" name="generate">Generate Absence Details Now</button>
    </form>

    <?php
    if (isset($_POST['generate'])) {
        include '../db/routes/generateAbsenceDetails.php';

        // Output the result after execution
        echo "<div class='output'>";
        echo "<p><strong>Script executed successfully. Absence details have been generated.</strong></p>";
        echo "</div>";
    }
    ?>
</div>

<script>
    function checkTimeAndExecute() {
        const now = new Date();
        
        // Jakarta's time zone (GMT+7)
        const jakartaOffset = 7 * 60; // Jakarta is GMT+7
        const utc = now.getTime() + (now.getTimezoneOffset() * 60000); // Convert to UTC
        const jakartaTime = new Date(utc + (jakartaOffset * 60000)); // Convert UTC to Jakarta time
        
        const targetHour = 21; // 21:00 or 9 PM
        const targetMinute = 45; // 43 minutes
        
        // Check if current time matches the target time
        if (jakartaTime.getHours() === targetHour && jakartaTime.getMinutes() === targetMinute) {
            document.getElementById('generateForm').submit();
        }
    }

    // Check the time every minute
    setInterval(checkTimeAndExecute, 60000);
</script>

</body>
</html>
