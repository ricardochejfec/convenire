<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require 'db.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);



//Code when user goes to the event login url
$app->any("/Events/{eventID}", function ($request, $response, $args) {
    $fullUrl = $_SERVER['REQUEST_URI'];
    $urlArray = Explode('/', $fullUrl);
    $eventID = $urlArray[count($urlArray)-1];

    $db = getDB();

    try
    {
        $stmt = $db->prepare( "SELECT EventId, EventTitle FROM Events where EventId = :eventID");   
        $stmt->bindParam(":eventID", $eventID);
        $stmt->execute();
        $result = $stmt->fetchAll();  
        



        if($stmt->rowCount() == 1)
        {
                        
            $title = $result[0]['EventTitle'] ;
            echo '
            <!DOCTYPE html>
            <html lang="en">
              <head>
                <title>Convenire</title>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
                <link rel="stylesheet" href="/event_login.css">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
                <script src="/index.js"></script>
              </head>
              
              <body>
                <div class="wrapper">
                  <form class="form-signin" method="post" action="/Events/' . $eventID .'/home">       
                    <h2 class="form-signin-heading">Sign in to ' . $title  . '!</h2>
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
        // else
        // {
        //     header("Location: /index.html");
        //     exit;
        // }
    
    $db = null;
    } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
    }

});

$app->any('/Events/{eventID}/home', function ($request, $response, $args){

    $db = getDB();
    $eventID = $request->getAttribute('eventID');
    $pwdInputted = $request->getParam('password');
    $emailInputted = $request->getParam('username');
    $nameInputted = $request->getParam('name');
    
    // session code
    session_start();
    $expireAfter = 20;
    if(isset($_SESSION['last_action'])){
        //Figure out how many seconds have passed
        //since the user was last active.
        $secondsInactive = time() - $_SESSION['last_action'];
        
        //Convert our minutes into seconds.
        $expireAfterSeconds = $expireAfter * 60 ;
        
        //Check to see if they have been inactive for too long.
        if($secondsInactive >= $expireAfterSeconds){
            //User has been inactive for too long.
            //Kill their session.

            session_unset();
            session_destroy();
        }
    }
    $_SESSION['last_action'] = time();

    //sql code

    try {

        // get info from db

         
        // get main info
        $sql = $db->prepare("SELECT EventTitle, Address, Description, Date, StartTime, EndTime, Password, Creator FROM Events where EventId = :eventID"); 
        $sql->bindParam(":eventID", $eventID);
        $sql->execute();
        $result = $sql->fetchAll();
        $eventTitle = $result[0]['EventTitle'];
        $eventPwd = $result[0]['Password'];
        $defaultDate = $result[0]['Date'] ;
        $defaultStart = substr($result[0]['StartTime'], 0, -3);
        $defaultEnd = substr($result[0]['EndTime'], 0, -3);
        $eventDesc = $result[0]['Description'];
        $defaultLocation = $result[0]['Address'];
        $creator = $result[0]['Creator'];

        // get tasks

        $sql = $db->prepare("SELECT taskName, NameInCharge, EmailInCharge FROM EventTasks where EventId = :eventID"); 
        $sql->bindParam(":eventID", $eventID);
        $sql->execute();
        $resultTasks = $sql->fetchAll();
        $taskScript = '';
        $i = 0;
        while($i<sizeof($resultTasks)){

            if ($resultTasks[$i]['EmailInCharge'] == NULL){
                $taskScript = $taskScript . '<tr>
                                            <th scope="row">' . ($i+1) . '</th>
                                            <td>' .  $resultTasks[$i]['taskName']   . '</td>
                                            <td><input type="TEXT" name="' . $resultTasks[$i]['taskName'] . '0' . '" size="25"></td>
                                            <td><input type="TEXT" name="' . $resultTasks[$i]['taskName'] . '1' . '" size="25"></td>
                                            </tr>';


            } else {
                $taskScript = $taskScript . '<tr>
                                            <th scope="row">' . ($i+1) . '</th>
                                            <td>'.  $resultTasks[$i]['taskName']   .'</td>
                                            <td><p> '. $resultTasks[$i]['EmailInCharge'] . '</p></td>
                                            <td><p>' . $resultTasks[$i]['NameInCharge'] . '</p></td>
                                            </tr>' ;

            }

            $i =  $i + 1;

        }

        // get locations

        $sql = $db->prepare("SELECT Address, votes FROM LocationPoll where EventId = :eventID order by votes DESC"); 
        $sql->bindParam(":eventID", $eventID);
        $sql->execute();
        $resultLocations = $sql->fetchAll();
        $mainLocScript = '';
        $pollLocScript = '';

        if ($resultLocations[0]['votes'] == 0){

            $mainLocScript = $mainLocScript . $defaultLocation;
        } else {
            
            $mainLocScript = $resultLocations[0]['Address'];
        }
        $i = 0;
        shuffle($resultLocations);
        while($i<sizeof($resultLocations)){

            
            $pollLocScript = $pollLocScript . '<li class="list-group-item">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="optionsRadios">
                                                        ' . $resultLocations[$i]['Address'] . '
                                                    </label>
                                                </div>
                                                </li>';
            $i =  $i + 1;

        }
        // get times

        $sql = $db->prepare("SELECT Date, StartTime, EndTime, votes FROM TimePoll where EventId = :eventID ORDER by votes DESC"); 
        $sql->bindParam(":eventID", $eventID);
        $sql->execute();
        $resultTimes = $sql->fetchAll();
        $mainTimeScript = '';
        $pollTimeScript = '';

        if ($resultTimes[0]['votes'] == 0){

            $mainTimeScript = $mainTimeScript . $defaultDate . ' ' . $defaultStart . '-' . $defaultEnd;
        } else {
            
            $mainTimeScript = $mainTimeScript . $resultTimes[0]['Date'] . ' ' . substr($resultTimes[0]['StartTime'], 0, -3) . '-'. substr($resultTimes[0]['EndTime'], 0, -3);
        }
        $i = 0;
        shuffle($resultTimes);
        while($i<sizeof($resultTimes)){

            
            $pollTimeScript = $pollTimeScript . '<li class="list-group-item">
                                                <div class="radio">
                                                    <label>
                                                        <input type="checkbox" name="optionsRadios">
                                                        ' . $resultTimes[$i]['Date'] . ' ' . substr($resultTimes[$i]['StartTime'], 0, -3) . '-' . 
                                                        substr($resultTimes[$i]['EndTime'], 0, -3) . '
                                                        </input>
                                                    </label>
                                                </div>
                                                </li>';
            $i =  $i + 1;

        }


        // get emails

        $sql = $db->prepare("SELECT Email FROM EventEmails where EventId = :eventID"); 
        $sql->bindParam(":eventID", $eventID);
        $sql->execute();
        $resultEmails = $sql->fetchAll();
        $emailListScript = '';
        $i =0;
        while($i<sizeof($resultEmails)){
            
            $emailListScript = $emailListScript . '<li>' . $resultEmails[$i]['Email'] . '</li>';
            $i =  $i + 1;

        }


        // get chat history
        $sql = $db->prepare("SELECT Email, Time, Comment FROM TaskDiscussion where EventId = :eventID ORDER by time ASC"); 
        $sql->bindParam(":eventID", $eventID);
        $sql->execute();
        $resultConvo = $sql->fetchAll();


        if ( $pwdInputted != '' ){

            
            
            $checked = password_verify($pwdInputted , $eventPwd);

            /* create a prepared statement */
            $stmt = $db->prepare("SELECT Email FROM eventEmails WHERE eventId= :eventID AND email = :emailInputted");
            
                /* bind parameters for markers */
            $stmt->bindParam(":eventID", $eventID);
            $stmt->bindParam(":emailInputted", $emailInputted);
            /* execute query */
            $stmt->execute();
            
            /* bind result variables */
            if ($stmt->rowCount() > 0){

                $_SESSION['checked'] = $checked;
            } else{

                $_SESSION['checked'] = 0;

            }
            
        }
        
        if (isset($_SESSION['checked']) && $_SESSION['checked']){

            echo '
            <!DOCTYPE html>
            <html>
            
            <head>
            
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <link rel="stylesheet" href="/event.css">
            <link href="https://fonts.googleapis.com/css?family=Bungee|Bungee+Shade|Mogra|Quicksand" rel="stylesheet">
            
                  
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
            <script src="/event.js"></script>
            </head>
                
            <body>
              
              <!-- header -->
              <nav class="navbar navbar-default navbar-fixed-top">
                    <div class="container">
                      <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-          controls="navbar">
                          <span class="sr-only">Toggle navigation</span>
                          <span class="icon-bar"></span>
                          <span class="icon-bar"></span>
                          <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="index.html">CONVENIRE</a>
                      </div>
                      <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                          <li><a href="index.html">Home</a></li>
                          <li><a href="event_login.html">About</a></li>
                          <li><a href="#contact">Contact</a></li>
                        </ul>
                      </div><!--/.nav-collapse -->
                    </div>
                  </nav>
            
            
              <!-- body -->
              <div class="wrapper">
                <h1> ' . $eventTitle  .'</h1>
                <br><br>
                <div class="outersquare container">
                <!-- Nav tabs -->
                    <ul class="panpan nav nav-tabs nav-justified" id="MyTab" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#Main" role="tab">Main Info</a>
                         
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#Polls" role="tab">Location and Time Polls</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#TasksAndDiscussion" role="tab">Tasks and Disscussion</a>
                      </li>
                      
                    </ul>
            
                  <!-- Tab panes -->
                    <div class="tab-content">
                      <div class="tab-pane active" id="Main" role="tabpanel">
                        <div class="container">
                          <br>
                            <div class="main-cont row">
                                <div class="col-xs-6">
                                    <label>Location:</label> 
                                    <div>' . $mainLocScript . '</div>
                                    <br>
                                    <label for="times">Date and Time:</label>
                                    <div> ' . $mainTimeScript . '</div>
                                    <br>
                                    <label for="desc">Description:</label>
                                    <p id="desc"> ' . $eventDesc . '</p>
                                </div>
                                <div class="col-xs-6">
                                    <label>Created by:</label> 
                                    <div>' . $creator .'</div>
                                    <br>
                                    <label for="guests">Checked-in Guests:</label>
                                    <ul id="guests">
                                      ' . $emailListScript . '
                                    </ul>
                                </div>
                            </div>
                        </div>
                      </div>
                      <div class="tab-pane" id="Polls" role="tabpanel" style="padding-top: 15px">
                        <div id="locPoll" class="col-md-6">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        What location do you prefer?
                                    </h3>
                                </div>
                                    <div class="panel-body">
                                        <ul class="list-group">
                                            ' . $pollLocScript . '
                                        </ul>
                                    </div>
                                    <div class="panel-footer">
                                        <button type="button" id="locBtn" class="btn btn-primary btn-sm">
                                            Vote</button>
                                    </div>
                            </div>
                        </div>
                          <div class="col-md-6">
                                <div id="timePoll" class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            Which time slots suit you?
                                        </h3>
                                    </div>
                                    <div class="panel-body">
                                        <ul class="list-group"> 
                                            ' . $pollTimeScript . '
                                        </ul>
                                    </div>
                                    <div class="panel-footer">
                                        <button type="button" id="timeBtn" class="btn btn-primary btn-sm">
                                            Vote</button>
                                    </div>
                                </div>
                            </div>
                      </div>
            
                      <div class="tab-pane" id="TasksAndDiscussion" role="tabpanel">
                          <div style="text-align: center" class="alert alert-success">
                              <strong>Tasks</strong> 
                          </div>
                          <form method="get" action="/updateTasks">
                              <table class="table table-inverse">
                              <thead class="thead-inverse">
                              <tr>
                                  <th>#</th>
                                  <th>Task</th>
                                  <th>Email</th>
                                  <th>Name</th>
                              </tr>
                              </thead>
                              <tbody id="taskbod">
                                  ' . $taskScript . '
                              </tbody>
                              </table>
                              <input type="hidden" name="eventID"  value="' . $eventID . '"/>
                              <button type="submit" id="taskbtn" class="btn btn-primary btn-sm">Assign</button>
                          </form>
            
                          <div style="text-align: center" class="alert alert-success">
                              <strong>Discussion</strong> 
                          </div>
                          
                          <div class="">
                              <div class="row"> 
                                  <div class="message-wrap col-sm-12">
                                      <div class="msg-wrap">
                                          <div class="media msg ">
                                              <a class="pull-left" href="#"></a>
                                              <div class="media-body">
                                                  <small class="pull-right time"><i class="fa fa-clock-o"></i> 12:13am</small>
                                                  <h5 class="media-heading">andre.rodriguez@mail.mcgill.ca</h5>
                                                  <small class="col-lg-10">I can bring the donuts, theres a Tims on my way.</small>
                                              </div>
                                          </div>
                                          <div class="media msg">
                                              <a class="pull-left" href="#"></a>
                                              <div class="media-body">
                                                  <small class="pull-right time"><i class="fa fa-clock-o"></i> 12:14am</small>
                                                  <h5 class="media-heading">ricardo.chefjec@mail.mcgill.ca</h5>
                                                  <small class="col-lg-10">Sounds good. I volunteer to take notes.</small>
                                              </div>
                                          </div>
                                          <div class="media msg">
                                              <a class="pull-left" href="#"></a>
                                              <div class="media-body">
                                                  <small class="pull-right time"><i class="fa fa-clock-o"></i> 12:15am</small>
                                                  <h5 class="media-heading">phil.kwanfong@mail.mcgill.ca</h5>
                                                  <small class="col-lg-10">Ill bring the layout then.</small>
                                              </div>
                                          </div>
                                          <div class="media msg">
                                              <a class="pull-left" href="#"></a>
                                          </div>
                                      </div>
            
                                      <div class="send-wrap ">
                                          <textarea class="form-control send-message" rows="3" placeholder="Write a reply..."></textarea>
                                      </div>
            
                                      <div class="btn-panel">
                                         <button type="button" class="btn btn-primary btn-sm" style="float:right">Send Message</button>
                                      </div>
                                  </div>
                              </div>
                          </div>      
                      </div>
            
                    </div>
                  </div>
                </div>
            
                <!-- footer -->
                <footer id="footer">
                  <div class="container">
                    <span>Place sticky footer content here.</span>
                  </div>
                </footer>
              </body>
            </html>
            ';


        } else if (isset($_SESSION['checked']) && $pwdInputted != ''){

            echo '
            <!DOCTYPE html>
            <html lang="en">
              <head>
                <title>Convenire</title>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
                <link rel="stylesheet" href="/event_login.css">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
                <script src="/index.js"></script>
              </head>
              
              <body>
                <div class="wrapper">
                  <form style="padding-bottom: 10px;" class="form-signin" method="post" action="/Events/' . $eventID .'/home">       
                    <h2 class="form-signin-heading">Sign in to ' . $eventTitle . '!</h2>
                    <input type="text" class="form-control" name="username" placeholder="Email Address" required="" autofocus="" />
                    <input type="text" class="form-control" name="name" placeholder="Name" required=""/>
                    <input type="password" class="form-control" name="password" placeholder="Password" required=""/>      
                    <button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
                    <div class="alert alert-danger" role="alert" style="margin-top: 10px;">  
                    <strong>Oh snap!</strong> Password or Email was incorrect. Please try again. ' . $_SESSION['checked'] . '
                    </div> 
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
        else {
            echo '
            <!DOCTYPE html>
            <html lang="en">
              <head>
                <title>Convenire</title>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
                <link rel="stylesheet" href="/event_login.css">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
                <script src="/index.js"></script>
              </head>
              
              <body>
                <div class="wrapper">
                  <form class="form-signin" method="post" action="/Events/' . $eventID .'/home">       
                    <h2 class="form-signin-heading">Sign in to ' . $eventTitle . '!</h2>
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

    }

    catch(exception $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
        }

});

$app->get('/updateTasks', function ($request, $response, $args){

    $db = getDB();
    
    $eventID = $request->getParam('eventID');
    $sql = $db->prepare("SELECT taskName FROM EventTasks where EventId = :eventID and EmailInCharge = '' ;"); 
    $sql->bindParam(":eventID", $eventID);
    $sql->execute();
    $result = $sql->fetchAll();
    $rq = $result[0]['taskName'] . '1' ;
    $re = $request-> getParam('Take_notes_of_meeting1');
    $i = 0;
    
    while ($i < sizeof($result)) {
        $email = $result[$i]['taskName'] . '0' ;
        $email = str_replace(' ', '_', $email);
        $email = $request-> getParam($email);
        $name = $result[$i]['taskName'] . '1'  ;
        echo $name;
        $name = str_replace(' ', '_', $name);
        echo $name;
        $name = $request-> getParam($name);
        echo $name;
        $sql = $db->prepare("UPDATE EventTasks SET  EmailInCharge = ? , NameInCharge = ?  WHERE TaskName = ? ;"); 
        $sql->bindParam(1,$email);
        $sql->bindParam(2, $name);
        $sql->bindParam(3, $result[$i]['taskName']   );
        $sql->execute();


        $i = $i + 1;
    }

    header("Location: /Events/". $eventID . "/home");
    exit;

});
$app->get('/updatepoll', function ($request, $response, $args){

    $eventID = $request->getParam('eventID');
    $locName = $request->getParam('address');
    $sql = $db->prepare("UPDATE LocationPoll SET  votes = votes + 1   WHERE eventID = ? and address = ?;"); 
    $sql->bindParam(1,$eventID);
    $sql->bindParam(2, $locName);
    $sql->execute();

    

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
    $creator = $request->getParam('em');
    $pwHash = password_hash($pw, PASSWORD_DEFAULT, ['cost' => 12]);
    $adminpwHash = password_hash($adminpw, PASSWORD_DEFAULT, ['cost' => 12]);

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
                $sql_event = "INSERT INTO Events (EventId, Address, Date, StartTime, EndTime, Description, EventTitle, Password, AdminPassword, Creator) 
                VALUES ('$eventID', '$locs[0]', '$times[0]', '$times[1]', '$times[2]', '$desc', '$title', '$pwHash', '$adminpwHash' , '$creator')";

                $stmt_event = $db->query($sql_event);
                

                //Add the extra times proposed for the poll to the database
                if(count($times) > 0)
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
                if(count($locs) > 0)
                {
                    for($i = 0; $i < count($locs); $i++)
                    {
                        $sql_locs = "INSERT INTO LocationPoll (EventId, Address, Votes)
                                     VALUES ('$eventID', '$locs[$i]', 0)";
                        $stmt_locs = $db->query($sql_locs);
                    }
                }

                //Add the tasks to the database
                if(count($tasks) > 0)
                {
                    for($i = 0; $i < count($tasks); $i++)
                    {
                        $sql_tasks = "INSERT INTO EventTasks (EventId, TaskName)
                                     VALUES ('$eventID', '$tasks[$i]')";
                        $stmt_tasks = $db->query($sql_tasks);
                    }
                }

                //Add the emails to the database
                if(count($emails) > 0)
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
        
    header("Location: /Events/$eventID");
    exit;
});

$app->run();