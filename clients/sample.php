<?php

/** 
 * This is a sample ussd application that illiustrates the general structure of a ussd application's script  
 * Use Case :
 * Consider a ussd application that allows users to register and cancel membership to an agency
*/

// Retrieve Json data from request
$data = file_get_contents('php://input');
// var_dump($data);     // Dump to see if you get the request data
$data = json_decode($data);
$sessionID = $data->sessionID;
$userId = $data->userID;
$msisdn = $data->msisdn;
$network = $data->network;
$newSession = $data->newSession;
$input = $data->userData;
$message = "";
$continueSession = false;

set_include_path('../');
require_once "helpers/helpers.php";

if ($newSession) {
    // Display Initial menu
    $message = "Welcome to Example Agency.\n";
    $message .= "Please select an option to proceed\n";
    $message .= "1. Regiser\n";
    $message .= "2. View Membership\n";
    $message .= "3. Terms and Conditions\n";

    // Save session
    // In first session, you can save the session details and set user data to 0 or 1 indicating that the previous prompt was the initial screen
    save_session($sessionID,$msisdn,$network,0,true);
    $continueSession = true;

} else {

    try {
        // Get this session save data
        $stages = get_session($sessionID,$msisdn)['u_data'];
        // var_dump($stages);
        if ($input == "0") {

            // Scenario: User enters 0 to go back to initial menu 
            // Action: display initial prompt and clear existing session data 
            $message = "Welcome to Example Agency.\n";
            $message .= "Please select an option to proceed\n";
            $message .= "1. Regiser\n";
            $message .= "2. View Membership\n";
            $message .= "3. Terms and Conditions\n";
            // reset session data to 0
            save_session($sessionID,$msisdn,$network,0);
            $continueSession = true;

        } else if ($stages == "0" && $input == "1") {

            $message = "Register to Example Agency\n";
            $message .= "Enter your name";
            // we will append 1 to the session data because user choose option 1 in previous screen
            save_session($sessionID,$msisdn,$network,1);
            $continueSession = true;

        } else if ($stages == "0*1") {

            // In this condition, we did not check for user input because user input was not for an option, but generic input
            // You can perform save `registration_data.name` logic here
            $message = "Register to Example Agency\n";
            $message .= "Enter your location";
            // Because input from previous prompt is not in line with a specific option, we will append 1 to the session data to track that prompt
            save_session($sessionID,$msisdn,$network,1);
            $continueSession = true;            

        } else if ($stages == "0*1*1") {
            
            // In this condition, we did not check for user input because user input was not for an option, but generic input
            // You can perform save `registration_data.location` logic here
            $message = "You have successfully registered to Example Agency";
            // No need to save session data since registration was completed

        } else if ($stages == "0" && $input == "2") {

            // Diplay Option 2 menu
            $message = "Your membership\n";
            $message .= "1. Renew membership";
            $message .= "2. Cancel membership";
            // we will append 2 to the session data because user choose option 2 in previous screen
            save_session($sessionID,$msisdn,$network,2);
            $continueSession = true;

        } else if ($stages == "0*2" && $input == "1") {
            
            // Display option 3 menu
            $message = "Your Membership\n\n";
            // Perform membership.renew logic here
            $message .= "Your Membership has been renewed successfully.";
            // No need to save session data since action has completed

        } else if ($stages == "0*2" && $input == "2") {
            
            // Display option 3 menu
            $message = "Your Membership\n\n";
            // Perform membership.cancel logic here
            $message .= "Your Membership has been cancelled.";
            // No need to save session data since action has completed

        } else if ($stages == "0" && $input == "3") {
            // Display option 3 menu
            $message = "Terms and conditions\n\n";
            $message .= "Visit https://example-agency.com/terms-and-conditions to read our terms and conditions";
            // No need to save session data since action has completed

        } else {
            // values outside scope
            var_dump($stages);
            $message = "Session terminates..";
        }

    } catch (Exception $e) {
        var_dump($e->getMessage());
        $message = "An error occured from the application!\n";
        $message .= "Please try again later";
    }    
}

// Data to return
$response = [
    'sessionID' => $sessionID,
    'userID' => $userId,
    'msisdn' => $msisdn,
    'message' => $message,
    'continueSession' => $continueSession
];

http_response_code(200);
header('content-type: application/json');
echo json_encode($response);