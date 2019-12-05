<?php
//this works for a project setup in the developers console with the correct calendar API attached to it and a service account activated
//for that project
//simple script to connect, edit, update your own google calendar (v3) or calendars that you have access with service account key
//connect+auth using service account key
require __DIR__ . '/vendor/autoload.php';
function getClient() {
	$KEY_FILE_LOCATION = __DIR__ . '/mycal-key.json'; //location of service account key
    	$client = new Google_Client();
    	$client->setApplicationName('daily-cal');
	$client->setAuthConfig($KEY_FILE_LOCATION);
    	$client->setScopes(Google_Service_Calendar::CALENDAR);
    return $client;
}

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);
//var_dump ($client); UNCOMMENT AND search for this string: 'client_email' => string 'user@app-name.iam.gserviceaccount.com' (length=48)
//(user being your service account name that you choose while creating the service key 
//go to google calendar and share your calendar with this email address giving editing rigts! 
//ATTENTION: insert, update, delete will not work until you do this !!!! 
//var_dump ($service);

//get events + display events
//for more functions and examples see: https://developers.google.com/calendar/v3/reference/events
$calendarId = 'yourgmailaddresshere'; //do not use 'primary' id does not work anymore use gmail adress instead
$optParams = array(
  'maxResults' => 6,
  'orderBy' => 'startTime',
  'singleEvents' => true,
  'timeMin' => date('c'),
);
$results = $service->events->listEvents($calendarId, $optParams);
$events = $results->getItems();
var_dump ($events);
if (empty($events)) {
    print "No upcoming events found.\n";
} else {
    print "Upcoming events:\n";
    foreach ($events as $event) {
        $start = $event->start->dateTime;
        if (empty($start)) {
            $start = $event->start->date;
        }
        printf("%s (%s)\n", $event->getSummary(), $start);
    }
}

//insert
//for more functions and examples see: https://developers.google.com/calendar/v3/reference/events/insert
$event = new Google_Service_Calendar_Event(array(
  'summary' => 'My first event inserted by me',
  //'location' => '800 Howard St., San Francisco, CA 94103',
  'description' => 'Somthing in the description',
  'id' => 'giveauniqueid', //see developers page for more info
  'start' => array(
    'dateTime' => '2019-12-06T13:00:00',
    'timeZone' => 'Europe/Bucharest',
  ),
  'end' => array(
    'dateTime' => '2019-12-06T14:00:00',
    'timeZone' => 'Europe/Bucharest',
  ),
  'recurrence' => array(
    //'RRULE:FREQ=DAILY;COUNT=2'
  ),

  'reminders' => array(
    'useDefault' => FALSE,
    'overrides' => array(
      array('method' => 'email', 'minutes' => 24 * 60),
      array('method' => 'popup', 'minutes' => 10),
    ),
  ),
));

$event = $service->events->insert($calendarId, $event);
//printf('Event created: %s\n', $event->htmlLink);


//update modify dateTime
$event = $service->events->get($calendarId, '201912021440iscir');
$start = new Google_Service_Calendar_EventDateTime();
$start->setDateTime('2019-12-05T13:00:00+02:00');
$event->setStart($start);
$end = new Google_Service_Calendar_EventDateTime();
$end->setDateTime('2019-12-05T14:00:00+02:00');
$event->setEnd($end);
$updatedEvent = $service->events->update($calendarId, $event->getId(), $event);

//that's all folks !!! 
?>
