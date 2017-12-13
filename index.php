<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \phpmailer\phpmailer\phpmailer;
use \phpmailer\phpmailer\Exception;

require '../vendor/autoload.php';
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';
require 'db.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

$user = '';
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
                  <form class="form-signin" method="post" id="loginform" action="/Events/' . $eventID .'/home">       
                    <h2 class="form-signin-heading">Sign in to ' . $title  . '!</h2>
                    <input type="text" id="username" class="form-control" name="username" placeholder="Email Address" required="" autofocus="" />
                    <input type="text" class="form-control" name="name" placeholder="Name" required=""/>
                    <input type="password" class="form-control" name="password" placeholder="Password" required=""/>      
                    <button id="loginbtn" class="btn btn-lg btn-primary btn-block" type="submit">Login</button>   
                  </form>
                </div>
            
                
                <div id="footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <a rel="license" href="http://creativecommons.org/licenses/by/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by/4.0/80x15.png"/></a> <span>2017 Mcgill University.</span>
                            </div>
                        </div>
                    </div>
                </div>

              </body>
            </html>
            ';
        }
        else
        {
            header("Location: /index.html");
            exit;
        }
    
    $db = null;
    } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
});

// Code that generates event page
$app->any('/Events/{eventID}/home', function ($request, $response, $args){

    $db = getDB();
    // Data that user inputted in login page
    $eventID = $request->getAttribute('eventID');
    $pwdInputted = $request->getParam('password');
    $emailInputted = $request->getParam('username');
    $nameInputted = $request->getParam('name');
    
    // session start
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

    

    try {

        // get main info of event
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
                                            <td><input type="email" name="' . $resultTasks[$i]['taskName'] . '0' . '" size="25"></td>
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
                                                        <input type="radio" name="optionsRadios">
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

        // if password is entered by user, means he was in login page and attempted to log in
        if ( $pwdInputted != '' ){
            // verify password
            $checked = password_verify($pwdInputted , $eventPwd);

            
            $stmt = $db->prepare("SELECT Email FROM eventEmails WHERE eventId= :eventID AND email = :emailInputted");
            
            
            $stmt->bindParam(":eventID", $eventID);
            $stmt->bindParam(":emailInputted", $emailInputted);
            
            $stmt->execute();
            
            // if email entered is associated to event then $checked become result of password verification
            if ($stmt->rowCount() > 0){

                $_SESSION['checked'] = $checked;
                if($_SESSION['checked']){$_SESSION['user'] = $emailInputted;}
            } else{
                $_SESSION['checked'] = 0;
            }
            
        }
        // if email and password is correct generate html code for event page
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
                        <a class="navbar-brand" href="/">CONVENIRE</a>
                      </div>
                      <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                          <li><a href="/">Home</a></li>
                          <li><a href="/about.html">About</a></li>
                          <li><a href="/contact.html">Contact</a></li>
                        </ul>
                        <form action="/logout">
                        <button type="submit" style="float:right; margin-top:10px;"  class="btn btn-primary btn-sm">Logout</button>
                        </form>
                      </div><!--/.nav-collapse -->
                    </div>
                  </nav>
            
            
              <!-- body -->
              <div class="wrapper">
                <h1 class="title"> ' . $eventTitle  .'</h1>
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
                                    <div id="WinningLoc">' . $mainLocScript . '</div>
                                    <br>
                                    <label for="times">Date and Time:</label>
                                    <div id="WinningTime"> ' . $mainTimeScript . '</div>
                                    <br>
                                    <label for="desc">Description:</label>
                                    <p id="desc"> ' . $eventDesc . '</p>
                                </div>
                                <div class="col-xs-6">
                                    <label>Created by:</label> 
                                    <div>' . $creator .'</div>
                                    <br>
                                    <label for="guests">Guests:</label>
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
                                        <ul id="LocList" class="list-group">
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
                                        <ul id="timeList" class="list-group"> 
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
                                  <div style="overflow-y= scroll class="message-wrap col-sm-12">
                                      <div id="chat" class="msg-wrap" style="overflow-y= scroll;">
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
                                          </div>
                                      </div>
            
                                      <div class="send-wrap ">
                                          <textarea id="textSent" class="form-control send-message" rows="3" placeholder="Write a reply..."></textarea>
                                      </div>
            
                                      <div class="btn-panel">
                                         <button id="MsgBtn" type="button" class="btn btn-primary btn-sm" style="float:right">Send Message</button>
                                      </div>
                                  </div>
                              </div>
                          </div>      
                      </div>
            
                    </div>
                  </div>
                </div>
            
                </div>
                  <br><br>
                    <!-- footer -->
                  <div id="footer">
                      <div class="container">
                        <div class="row">
                          <div class="col-md-6">
                            <a rel="license" href="http://creativecommons.org/licenses/by/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by/4.0/80x15.png"/></a> <span>2017 Mcgill University.</span>
                          </div>
                          <div class="col-md-6">
                            <span class="pull-right"><a href="contact.html">Contact</a> | <a href="index.html">CONVENIRE</a> |  <a href="about.html">Help</a></span>  
                          </div>
                        </div>
                      </div>
                  </div>
                  
               </body>
            </html>
            ';
        } 
        // password is incorrect
        else if (isset($_SESSION['checked']) && $pwdInputted != ''){

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
                  <form class="form-signin" method="post" id="loginform" action="/Events/' . $eventID .'/home">       
                    <h2 class="form-signin-heading">Sign in to ' . $eventTitle  . '!</h2>
                    <input type="text" id="username" class="form-control" name="username" placeholder="Email Address" required="" autofocus="" />
                    <input type="text" class="form-control" name="name" placeholder="Name" required=""/>
                    <input type="password" class="form-control" name="password" placeholder="Password" required=""/>      
                    <button id="loginbtn" class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
                    <div class="alert alert-danger" role="alert" style="margin-top: 10px;">  
                    <strong>Oh snap!</strong> Password or Email was incorrect. Please try again.
                    </div>    
                  </form>
                </div>
            
                
                <div id="footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <a rel="license" href="http://creativecommons.org/licenses/by/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by/4.0/80x15.png"/></a> <span>2017 Mcgill University.</span>
                            </div>
                        </div>
                    </div>
                </div>

              </body>
            </html>
            ';
        }

        // when user session expired and he refreshes page
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
                  <form class="form-signin" method="post" id="loginform" action="/Events/' . $eventID .'/home">       
                    <h2 class="form-signin-heading">Sign in to ' . $eventTitle  . '!</h2>
                    <input type="text" id="username" class="form-control" name="username" placeholder="Email Address" required="" autofocus="" />
                    <input type="text" class="form-control" name="name" placeholder="Name" required=""/>
                    <input type="password" class="form-control" name="password" placeholder="Password" required=""/>      
                    <button id="loginbtn" class="btn btn-lg btn-primary btn-block" type="submit">Login</button>   
                  </form>
                </div>
            
                
                <div id="footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <a rel="license" href="http://creativecommons.org/licenses/by/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by/4.0/80x15.png"/></a> <span>2017 Mcgill University.</span>
                            </div>
                        </div>
                    </div>
                </div>

              </body>
            </html>
            ';
        }

    }

    catch(exception $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
});

// Update tasks when assigned
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

    header("Location: /Events/". $eventID . "/home#TasksAndDiscussion");
    exit;
});

// Updates poll votes and displays poll
$app->get('/updatelocationpoll', function ($request, $response, $args){
    $db = getDB();
    $eventID = $request->getParam('eventId');
    $locName = $request->getParam('address');
    $user = $request->getParam('email');

    $sql = $db->prepare('SELECT votedLoc from eventEmails  WHERE eventID = :eId and email = :user ;'); 

    $sql->bindParam(':eId',$eventID);
    $sql->bindParam(':user', $user);
    $sql->execute();

    $vot = $sql->fetchAll();
    $voted = $vot[0];

    // User can only vote for 1 location option, if he votes again, his vote is changed

    if(!$voted){

        $sql = $db->prepare('UPDATE LocationPoll SET  votes = votes + 1   WHERE eventID = :eId and address = :addr ;'); 

        $sql->bindParam(':eId',$eventID);
        $sql->bindParam(':addr', $locName);
        $sql->execute();
    
        $sql = $db->prepare('UPDATE eventEmails SET  votedLoc = 1 , location = :addr  WHERE eventID = :eId and email = :user ;'); 
    
        $sql->bindParam(':eId',$eventID);
        $sql->bindParam(':addr', $locName);
        $sql->bindParam(':user', $user);
        $sql->execute();
    
        $sql = $db->prepare('SELECT eventID, address, votes from LocationPoll WHERE eventID = :eId order by votes DESC;'); 
    
        $sql->bindParam(':eId',$eventID);
        
        $sql->execute();
        $res = $sql->fetchAll();
    } else {

        $sql = $db->prepare('SELECT location from eventEmails WHERE eventID = :eId and email = :user ;'); 
    
        $sql->bindParam(':eId',$eventID);
        $sql->bindParam(':user', $user);
        
        $sql->execute();
        $arr = $sql->fetchAll();
        $oldLoc = $arr[0][0];

        $sql = $db->prepare('UPDATE eventEmails SET  votedLoc = 1 , location = :addr  WHERE eventID = :eId and email = :user ;'); 
    
        $sql->bindParam(':eId',$eventID);
        $sql->bindParam(':addr', $locName);
        $sql->bindParam(':user', $user);
        $sql->execute();

        $sql = $db->prepare('UPDATE LocationPoll SET  votes = votes - 1   WHERE eventID = :eId and address = :oldLoc ;'); 

        $sql->bindParam(':eId',$eventID);
        $sql->bindParam(':oldLoc', $oldLoc);
        $sql->execute();

        $sql = $db->prepare('UPDATE LocationPoll SET  votes = votes + 1   WHERE eventID = :eId and address = :addr ;'); 

        $sql->bindParam(':eId',$eventID);
        $sql->bindParam(':addr', $locName);
        $sql->execute();

        $sql = $db->prepare('SELECT eventID, address, votes from LocationPoll WHERE eventID = :eId order by votes DESC;'); 
    
        $sql->bindParam(':eId',$eventID);
        
        $sql->execute();
        $res = $sql->fetchAll();
    }

    $response->getBody()->write(json_encode($res));
});

// Updates poll votes and displays poll
$app->get('/updatetimeepoll', function ($request, $response, $args){
    $db = getDB();
    $eventID = $request->getParam('eventId');
    $timearr = $request->getParam('timearr');
    str_replace('"', "", $eventID);
    $user = $request->getParam('email');;
    
    $sql = $db->prepare('SELECT votedTime from eventEmails  WHERE eventID = :eId and email = :user ;'); 

    $sql->bindParam(':eId',$eventID);
    $sql->bindParam(':user', $user);
    $sql->execute();

    $vot = $sql->fetchAll();
    $voted = $vot[0];
    // Voting mechanism, user can only vote for 1 time option
    if(!$voted){

        $sql = $db->prepare('UPDATE TimePoll SET  votes = votes + 1   WHERE eventID = :eId and date = :dat and StartTime =:start and EndTime = :end ;');

        $sql->bindParam(':eId',$eventID);
        $sql->bindParam(':dat', $timearr[0]["date"]);
        $sql->bindParam(':start', $timearr[0]["start"]);
        $sql->bindParam(':end', $timearr[0]["end"]);
        $sql->execute();
    
        $sql = $db->prepare('UPDATE eventEmails SET  votedTime = 1 , date =:dat, start = :start , end = :end WHERE eventID = :eId and email = :user ;'); 
    
        $sql->bindParam(':eId',$eventID);
        $sql->bindParam(':dat', $timearr[0]["date"]);
        $sql->bindParam(':start', $timearr[0]["start"]);
        $sql->bindParam(':end', $timearr[0]["end"]);
        $sql->bindParam(':user', $user);
        $sql->execute();
    
        $sql = $db->prepare('SELECT eventID, date,StartTime, EndTime, votes from TimePoll WHERE eventID = :eId order by votes DESC ;'); 

        $sql->bindParam(':eId',$eventID);
        
        $sql->execute();
        $res = $sql->fetchAll();
    }
    else {

        $sql = $db->prepare('SELECT date, start, end from eventEmails WHERE eventID = :eId and email = :user ;'); 
    
        $sql->bindParam(':eId',$eventID);
        $sql->bindParam(':user', $user);
        
        $sql->execute();
        $arr = $sql->fetchAll();
        $oldTime = $arr[0];

        $sql = $db->prepare('UPDATE eventEmails SET  votedTime = 1 , date =:dat, start = :start , end = :end WHERE eventID = :eId and email = :user ;'); 
    
        $sql->bindParam(':eId',$eventID);
        $sql->bindParam(':dat', $timearr[0]["date"]);
        $sql->bindParam(':start', $timearr[0]["start"]);
        $sql->bindParam(':end', $timearr[0]["end"]);
        $sql->bindParam(':user', $user);
        $sql->execute();

        $sql = $db->prepare('UPDATE timePoll SET  votes = votes - 1   WHERE eventID = :eId and date = :dat and startTime = :start and endTime = :end ;');
        $sql->bindParam(':eId',$eventID);
        $sql->bindParam(':dat', $oldTime['date']);
        $sql->bindParam(':start', $oldTime['start']);
        $sql->bindParam(':end', $oldTime['end']);
        $sql->execute();

        $sql = $db->prepare('UPDATE timePoll SET  votes = votes + 1   WHERE eventID = :eId and date = :dat and startTime = :start and endTime = :end ;'); 

        $sql->bindParam(':eId',$eventID);
        $sql->bindParam(':dat', $timearr[0]["date"]);
        $sql->bindParam(':start', $timearr[0]["start"]);
        $sql->bindParam(':end', $timearr[0]["end"]);
        $sql->execute();

        $sql = $db->prepare('SELECT eventID, date,StartTime, EndTime, votes from TimePoll WHERE eventID = :eId order by votes DESC;'); 

        $sql->bindParam(':eId',$eventID);
        
        $sql->execute();
        $res = $sql->fetchAll();
    }
    // return json format
    $response->getBody()->write(json_encode($res));
});

// Updates the chat discussion 
$app->get('/chatUpdate', function ($request, $response, $args){

    $db = getDB();
    $eventID = $request->getParam('eventId');
    $sql = $db->prepare('SELECT email, comment, date, time from  TaskDiscussion where eventId = :eventID order by Date, time ASC;'); 
    $sql->bindParam(':eventID',$eventID);
    $sql->execute();
    $res = $sql->fetchAll();


    $response->getBody()->write(json_encode($res));
});

// Adds message to chat history
$app->get('/messageToChat', function ($request, $response, $args){

    $db = getDB();
    $eventID = $request->getParam('eventId');
    $date = $request->getParam('date');
    $time = $request->getParam('time');
    $user = $request->getParam('email');
    $msg = $request->getParam('message');
    
    $sql = $db->prepare('INSERT INTO TaskDiscussion VALUES (:eId , :user, :msg, :dat , :tim ) ;'); 

    $sql->bindParam(':eId',$eventID);
    $sql->bindParam(':user', $user);
    $sql->bindParam(':msg', $msg);
    $sql->bindParam(':dat', $date);
    $sql->bindParam(':tim', $time);
    $sql->execute();

    header("Location: /chatUpdate");
    exit;
});

// logs user out of session
$app->get('/logout', function ($request, $response, $args){

    session_start();
    session_unset();
    header("Location: /index.html");
    exit;

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
    $emails = $request->getParam('emails');
    $creator = $request->getParam('em');
    $pwHash = password_hash($pw, PASSWORD_DEFAULT, ['cost' => 12]);

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
                $sql_event = "INSERT INTO Events (EventId, Address, Date, StartTime, EndTime, Description, EventTitle, Password, Creator) 
                VALUES ('$eventID', '$locs[0]', '$times[0]', '$times[1]', '$times[2]', '$desc', '$title', '$pwHash', '$creator')";

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
                    $sql_emails = "INSERT INTO EventEmails (EventId, Email)
                                 VALUES ('$eventID', '$creator')";
                    $stmt_emails = $db->query($sql_emails);

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

        $mailer = new PHPMailer;
        $mailer->isSMTP();
        $mailer->SMTP = true;
        $mailer->SMTPAuth = true;
        $mailer->SMTPDebug = 2;
        $mailer->Host = 'smtp.gmail.com';
        $mailer->Username = 'letsconvenire@gmail.com';
        $mailer->Password = 'convenire1';
        $mailer->SMTPSecure = 'ssl';
        $mailer->Port = 465;
    
        $subjectAdmin = "New event creation!";
        $message = "Hello,
                    <br/> <br/>
                    find below the passwords to access your new event, along with the secret link:
                    <br/> <br/>                
                    convenire.com/Events/" . $eventID .
                    "<br/> <br/>
                    guest password:
                    " . $pw .
                    "<br/> <br/>
                    Thank you for using Convenire!
                    <br/> <br/>
                    The Convenire team";
    
        $mailer->From = 'letsconvenire@gmail.com';
        $mailer->FromName = 'Convenire';
        $mailer->addReplyTo('letsconvenire@gmail.com','Reply address');
        $mailer->addAddress('letsconvenire@gmail.com', 'Convenire');
        $mailer->addAddress($creator, 'Event Creator');
        $mailer->Subject = $subjectAdmin;
        $mailer->Body = $message;
        $mailer->AltBody = $message;
    
        if($mailer->send())
        {
        }
        else
        {
            //echo $mailer->ErrorInfo;
        }
    
        $mailer2 = new PHPMailer;
        $mailer2->isSMTP();
        $mailer2->SMTP = true;
        $mailer2->SMTPAuth = true;    
        $mailer2->SMTPDebug = 2;
        $mailer2->Host = 'smtp.gmail.com';
        $mailer2->Username = 'letsconvenire@gmail.com';
        $mailer2->Password = 'convenire1';
        $mailer2->SMTPSecure = 'ssl';
        $mailer2->Port = 465;
    
        $subject = "Invitation to " . $title . " !";
        $message = "Hello,<br/> <br/>
                    find below the password to access " . $title . " at convenire.com/Events/" . $eventID . "
                    <br/> <br/>
                    password:
                    
                    " . $pw .
                    "<br/> <br/>
                    Thank you for using Convenire!
                    <br/> <br/>
                    The Convenire team";
        
        $mailer2->From = 'letsconvenire@gmail.com';
        $mailer2->FromName = 'Convenire';
        $mailer2->addReplyTo('letsconvenire@gmail.com','Reply address');
        $mailer2->addAddress('letsconvenire@gmail.com', 'Convenire');
        $mailer2->Subject = $subject;
        $mailer2->Body = $message;
        $mailer2->AltBody = $message;
    
        for($i = 0; $i < count($emails); $i++)
        {
            $mailer2->addBCC($emails[$i], 'Guest');
        }
    
        if($mailer2->send())
        {
        }
        else
        {
            //echo $mailer->ErrorInfo;
        }
        
        header("Location: /Events/$eventID");
        exit;
});

$app->run();