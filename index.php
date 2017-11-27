<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require 'db.php';

$app = new \Slim\App;

//Code when user goes to the event url
$app->any("/Events/{eventID}", function ($request, $response, $args) {
    $fullUrl = $_SERVER['REQUEST_URI'];
    $urlArray = Explode('/', $fullUrl);
    $eventID = $urlArray[count($urlArray)-1];

    $db = getDB();

    try
    {
        $sql_verify = "SELECT EventId FROM Events where EventId = '$eventID'";        
        $verify = $db->query($sql_verify);
        $rows = $verify->rowCount();
                    
        if($rows == 1)
        {
            $sql_title = "SELECT EventTitle FROM Events where EventId = '$eventID'";  
            $title = $db->query($sql_title);            

            echo '
            <!DOCTYPE html>
            <html lang="en">
              <head>
                <title>Convenire</title>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
                <link rel="stylesheet" href="event_login.css">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
                <script src="index.js"></script>
              </head>
              
              <body>
                <div class="wrapper">
                  <form class="form-signin">       
                    <h2 class="form-signin-heading">Sign in to ' . $title . '!</h2>
                    <input type="text" class="form-control" name="username" placeholder="Email Address" required="" autofocus="" />
                    <input type="text" class="form-control" name="name" placeholder="Name" required=""/>
                    <input type="password" class="form-control" name="password" placeholder="Password" required=""/>      
                    <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>   
                  </form>
                </div>
            
                
                <footer>
                  <div class="container">
                    <span>Place sticky footer content here.</span>
                  </div>
                </footer>
            
              </body>
            </html>
            ';
        }
        else
        {
            header("Location: ../index.html");
            exit;
        }
    
    $db = null;
    } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

});

//Code for event creation page, adds all the user input into the database
$app->post('/create', function ($request, $response, $args) {

    //back/cancel button that brings user back to home page
    echo "<form action='/'><input type='submit' value='Back'></form>";
    $title = $request->getParam('title');
    $locs = $request->getParam('locs');
    $times = $request->getParam('times');
    $tasks = $request->getParam('tasks');
    $desc = $request->getParam('desc');
    $pw = $request->getParam('pw');
    $adminpw = $request->getParam('adminpw');
    $emails = $request->getParam('emails');


    try {
        $db = getDB();
        
        //We create a random event ID, and we keep recreating as long as it is not unique
        while(1)
        {            
            $eventID = bin2hex(random_bytes(10));
            $sql_verify = "SELECT EventId FROM Events where EventId = '$eventID'";
            
            $verify = $db->query($sql_verify);
            
            $rows = $verify->rowCount();
                        
            if($rows == 0)
            {
                //Add the event to the main event table
                $sql_event = "INSERT INTO Events (EventId, Address, Date, StartTime, EndTime, Description, EventTitle, Password, AdminPassword) 
                VALUES ('$eventID', '$locs[0]', '$times[0]', '$times[1]', '$times[2]', '$desc', '$title', '$pw', '$adminpw' )";

                //Add the extra times proposed for the poll to the database
                if(count($times) > 3)
                {
                    for($i = 0; $i < count($times); $i = $i + 3)
                    {
                        $i2 = $i + 1;
                        $i3 = $i + 2;
                        
                        $sql_times = "INSERT INTO TimePoll (EventId, Date, StartTime, EndTime, Votes)
                                      VALUES ('$eventID', '$times[$i]', '$times[$i2]', '$times[$i3]', 0)";
                        $stmt_times = $db->query($sql_times);
                    }
                }

                //Add the extra locations proposed for the poll to the database
                if(count($locs) > 1)
                {
                    for($i = 0; $i < count($locs); $i++)
                    {
                        $sql_locs = "INSERT INTO LocationPoll (EventId, Address, Votes)
                                     VALUES ('$eventID', '$locs[$i]', 0)";
                        $stmt_locs = $db->query($sql_locs);
                    }
                }

                //Add the tasks to the database
                if(count($tasks) > 1)
                {
                    for($i = 0; $i < count($tasks); $i++)
                    {
                        $sql_tasks = "INSERT INTO EventTasks (EventId, TaskName)
                                     VALUES ('$eventID', '$tasks[$i]')";
                        $stmt_tasks = $db->query($sql_tasks);
                    }
                }

                //Add the emails to the database
                if(count($emails) > 1)
                {
                    for($i = 0; $i < count($emails); $i++)
                    {
                        $sql_emails = "INSERT INTO EventEmails (EventId, Email)
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
        
    header('Location: https://google.ca');
});

$app->run();