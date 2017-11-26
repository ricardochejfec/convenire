<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require 'db.php';

$app = new \Slim\App;


$app->post('/create', function ($request, $response, $args) {

    //back/cancel button that brings user back to home page
    echo "<form action='/'><input type='submit' value='Back'></form>";
    
    $title = $request->getParam('title');
    $locs = $request->getParam('locs');
    $times = $request->getParam('times');
    $tasks = $request->getParam('tasks');
    $desc = $request->getParam('desc');
    $pw = $request->getParam('pw');
    $emails = $request->getParam('emails');
    
    //Input verification should be done through JS

    // //does not allow users to input empty strings
    // if($username == "" || $password == "") {
    //     echo '{"error":{"text": empty field(s).}}';
    //     return;
    // }
    
    //$sql = "INSERT INTO Users (username, password, email) VALUES ('$username', '$password', '$email')";

    try {
        $db = getDB();

        //We create a random event ID, and we keep recreating as long as it is not unique
        while(1)
        {
            $eventID = bin2hex(random_bytes(10));
            $sql_verify = "SELECT * FROM Events where EventId = $eventID";
            
            $verify = $db->query($sql_verify);
            $rows = mysql_num_rows($verify);
            
            if($rows == 0)
            {
                //Add the event to the main event table
                $sql_event = "INSERT INTO Events ('EventId', 'Address', 'Date', 'StartTime', 'EndTime', 'Description', 'EventTitle', 'Password') 
                VALUES ('$eventID', '$locs[0]', '$times[0]', '$times[1]', '$times[2]', '$desc', '$title', '$pw' )";

                //Add the extra times proposed for the poll to the database
                if(count($times) > 3)
                {
                    for($i = 3; $i < count($times); $i = $i + 3)
                    {
                        $i2 = $i + 1;
                        $i3 = $i + 2;
                        $sql_times = "INSERT INTO TimePoll ('EventId', 'Date', 'StartTime', 'EndTime', 'Votes')
                                      VALUES ('$eventID', '$times[$i]', '$times[$i2]', '$times[$i3])', 0";
                        $stmt_times = $db->query($sql_times);
                    }
                }

                //Add the extra locations proposed for the poll to the database
                if(count($locs) > 1)
                {
                    for($i = 1; $i < count($locs); $i++)
                    {
                        $sql_locs = "INSERT INTO LocationPoll ('EventId', 'Address', 'Votes')
                                     VALUES ('$eventID', '$locs[$i]', 0)";
                        $stmt_locs = $db->query($sql_locs);
                    }
                }

                //Add the tasks to the database
                if(count($tasks) > 0)
                {
                    for($i = 0; $i < count($tasks); $i++)
                    {
                        $sql_tasks = "INSERT INTO EventTasks ('EventId', 'TaskName')
                                     VALUES ('$eventID', '$locs[$i]')";
                        $stmt_tasks = $db->query($sql_tasks);
                    }
                }

                //Add the emails to the database
                if(count($emails) > 0)
                {
                    for($i = 0; $i < count($emails); $i++)
                    {
                        $sql_emails = "INSERT INTO EventEmails ('EventId', 'Email')
                                     VALUES ('$eventID', '$emails[$i]')";
                        $stmt_emails = $db->query($sql_emails);
                    }
                }

                $stmt = $db->query($sql_event);
                break;
            }       
        }

        
        $db = null;
        } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
        }

    header('Location: event page here');
});

$app->run();