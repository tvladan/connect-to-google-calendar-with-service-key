<?php

require __DIR__ . '/vendor/autoload.php';

function getClient() {
	
	$KEY_FILE_LOCATION = __DIR__ . '/mycal-key.json';

    $client = new Google_Client();
    $client->setApplicationName('daily-cal');
	$client->setAuthConfig($KEY_FILE_LOCATION);
    $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
    return $client;
}


// Get the API client and construct the service object.
//$client->request('GET', '/', ['verify' => false]);
$client = getClient();
$service = new Google_Service_Calendar($client);
var_dump ($client);
var_dump ($service);

// Print the next 10 events on the user's calendar.
$calendarId = 'confortul.timisoara@gmail.com';
$optParams = array(
  'maxResults' => 10,
  'orderBy' => 'startTime',
  'singleEvents' => true,
  'timeMin' => date('c'),
);
$results = $service->events->listEvents($calendarId, $optParams);
$events = $results->getItems();

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