<?php
$accessToken = 'eyJraWQiOiIxY2UxZTEzNjE3ZGNmNzY2YjNjZWJjY2Y4ZGM1YmFmYThhNjVlNjg0MDIzZjdjMzJiZTgzNDliMjM4MDEzNWI0IiwidHlwIjoiUEFUIiwiYWxnIjoiRVMyNTYifQ.eyJpc3MiOiJodHRwczovL2F1dGguY2FsZW5kbHkuY29tIiwiaWF0IjoxNzIxNjU1Mjg2LCJqdGkiOiIxMDE2ZWFhNi0wMWIwLTRjZGEtYWFjZi03MTlkMTdiZTM4Y2EiLCJ1c2VyX3V1aWQiOiJkZjdhZWY1ZS1lZWY5LTRiMmMtOTQ3Zi1mNmRkYTc5YjIzY2IifQ.ylSY5F-Njtvpwg1Y2YhH18LQ-sTusSwPvpV1x1-IAWQspnUsYycCoFg0gNIHcG0E20LCAiv8IZDnxA59LcC2Jw';

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.calendly.com/webhook_subscriptions",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\n  \"url\": \"https://crm.olympe-mariage.com/calendly/listen-group.php\",\n  \"events\": [\n    \"invitee.created\",\n    \"invitee.canceled\",\n    \"invitee_no_show.created\",\n    \"invitee_no_show.deleted\"\n  ],\n  \"organization\": \"https://api.calendly.com/organizations/75eb47eb-e06d-4817-8630-ed72ce5af3b1\",\n  \"user\": \"https://api.calendly.com/users/df7aef5e-eef9-4b2c-947f-f6dda79b23cb\",\n  \"scope\": \"organization\"\n}",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer {$accessToken}",
    "Content-Type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
?>