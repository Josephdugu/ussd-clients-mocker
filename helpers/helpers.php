<?php

/**
 * ****************************************************************************************************************************************************************
 * Database 
 */

/**
 * Connection to DB
*/
function connect_db ($data = ['host'=>'localhost', 'user'=>'root', 'password'=>'', 'database'=>'ussd_sessions']) {
    return $conn = mysqli_connect(
        $data['host'], $data['user'], $data['password'], $data['database']
    );
}

/**
 * Save session 
 * @param string $sessionID     This field stores the sessionID of the user's session, and it's usually required
 * @param string $msisdn        This field stores the phone number of the user, usually required
 * @param string $network       This field stores the mobile operator name of the user's phone number, This is not a requirement and can be removed from the operations
 * @param string $ussd_code     This field stores the application's ussd code, This is not a requirement and can be removed from the operations
 * @param string $input         This field is the input the user has made in a particular prompt and is concatenated with previous input and stored in the session, starting from the initial prompt when the input is 0, This is usually required
 * @param string $newSession    This field indicates whether the session is a new one and a new session data be created in the database, Or update an exisiting session record, This is usually required
 */
function save_session ($sessionID, $msisdn, $network, $input = 0, $newSession = false, $ussd_code = "*123*9#"):mysqli_result|string|bool {
    try {
        $conn = connect_db();
        // chech if the session already exists 
        if ($newSession) {
            // create a new session record
            // For initial save, let user data be 0, indicating that no option/input has been reeived from the user yet
            $query = "INSERT INTO `ark_client_sessions` (session_id, msisdn, network, ussd_code, u_data)
                        VALUES('$sessionID', '$msisdn', '$network', '$ussd_code', '$input')";
            return mysqli_query($conn, $query);
        } else {
            // sansitize input
            $input = str_replace([' ','*'], '', $input);
            // Fetch the existing record for the value of previous data 
            $previous_data = mysqli_query($conn, "SELECT * FROM `ark_client_sessions` WHERE session_id = '$sessionID' AND msisdn = '$msisdn' ORDER BY created_at DESC LIMIT 1");
            $previous_data->data_seek(0);
            $previous_data = $previous_data->fetch_array(MYSQLI_ASSOC);
            // If input is 0, then replace everything in DB to indicate that the user is starting from initial prompt again, else add *input to the existing data
            $data = $input == 0 ? 0 : $previous_data['u_data']."*".$input;
            $id = $previous_data['id'];
            var_dump($data);
            $query = "UPDATE `ark_client_sessions` SET u_data = '$data' WHERE id = '$id' AND msisdn = '$msisdn'";
            // $query = "UPDATE `ark_client_sessions` SET u_data = '$data' WHERE session_id = '$sessionID' AND msisdn = '$msisdn'";
            return mysqli_query($conn, $query);
        }

    } catch (Exception $e) {
        echo($e->getMessage());
        // return $e->getMessage();
        return false;
    }
}


/**
 * Get the session's record
 * @param string $sessionID     The session Id of the ussd session
 * @param string $msisdn    The phone number of the user
 */
function get_session ($sessionID, $msisdn):array {
    $conn = connect_db();
    $result = mysqli_query($conn, "SELECT u_data FROM `ark_client_sessions` WHERE session_id = '$sessionID' AND msisdn = '$msisdn' ORDER BY created_at DESC LIMIT 1");
    $result->data_seek(0);
    return $result->fetch_array(MYSQLI_ASSOC);
}