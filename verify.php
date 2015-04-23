<?php
//Define Soap Authentication Variables
$username = ""; //Enter your verifyID username 
$apikey = ""; //Enter your verifyID  apikey
$id_number =$_POST['id'];

function login($username, $apikey) {
    global $username, $apikey, $session_id;
//Disable WSDL cache
ini_set("soap.wsdl_cache_enabled", "0");

//Create new soap client
$client = new SoapClient("http://verifyid.co.za/api/verifyiddata.wsdl");

//initialize variables
$login_message = "null";
$session_id = "null";

//Login and create new session
try
{
    $session_data = $client->doLogin($username, $apikey);

    //Parse the xml and extract result information
    $xml_object = @simplexml_load_string($session_data);
    if ($xml_object)
        {
            foreach ($xml_object as $element => $value)
            {
                switch ($element)
                {
                    case 'LOGIN-MESSAGE':
                        $login_message = "$value";
                        break;
                    case 'SESSIONID':
                        $session_id = "$value";
                        break;
                }
            }
        }
}

catch(SoapFault $exception)
{
    #echo $exception;
    echo 'Your ID is invalid';
}
#echo $login_message .' '. $session_id;

return $session_id;
}

$session_id = login($username, $apikey);
#echo $session_id;

function profile($session, $apikey, $id_number) {
    global $session_id, $apikey, $id_number;
//Disable WSDL cache
ini_set("soap.wsdl_cache_enabled", "0");

//Create new soap client
$client = new SoapClient("http://verifyid.co.za/api/verifyiddata.wsdl");

//initialize variables
//Login and create new session
try
{
    $session_data = $client->profile($session_id,$apikey,$id_number);

    //Parse the xml and extract result information
    $xml_object = @simplexml_load_string($session_data);
    if ($xml_object)
    {
        $search_result = $xml_object->{'SEARCH-RESULT'};
        $search_message = $xml_object->{'SEARCH-MESSAGE'};
        $login_message = $xml_object->{'LOGIN-MESSAGE'};
        $date = $xml_object->{'TIMESTAMP'}->{'DATE'};
        $time = $xml_object->{'TIMESTAMP'}->{'TIME'};
        $id_number2 = $xml_object->{'FILE-DETAIL'}->{'IDNUMBER'};
        $firstname = $xml_object->{'FILE-DETAIL'}->{'FIRSTNAMES'};
        $surname = $xml_object->{'FILE-DETAIL'}->{'SURNAME'};
        $birthday = $xml_object->{'FILE-DETAIL'}->{'BIRTHDAY'};
        $age = $xml_object->{'FILE-DETAIL'}->{'AGE'};
        $gender = $xml_object->{'FILE-DETAIL'}->{'GENDER'};
        $citizen = $xml_object->{'FILE-DETAIL'}->{'CITIZEN'};
        $death_status = $xml_object->{'FILE-DETAIL'}->{'DEATH-STATUS'};
        $death_date = $xml_object->{'FILE-DETAIL'}->{'DEATH-DATE'};
        $death_place = $xml_object->{'FILE-DETAIL'}->{'DEATH-PLACE'};
    }
}
catch(SoapFault $exception)
{
    #echo $exception;
     echo 'Your ID is invalid';
}
if (!empty($surname)) {
$username = "jongzanet";
$password = "jongizanetpass";
$hostname = "localhost";

//connection to the database
$dbhandle = mysql_connect($hostname, $username, $password)
  or die("Unable to connect to MySQL");
$selected = mysql_select_db("jongizanet",$dbhandle)
  or die("Could not select jongizanet");
$result = mysql_query("SELECT count(*) as count FROM wp_users WHERE display_name LIKE '%$surname%'");
//fetch tha data from the database
$row = mysql_fetch_array($result);
if ($row['count'] > 0) {
    echo 'You are:: ';
    echo '<br/>ID Number: ';
    echo $id_number2;
    echo '<br/>First Names: ';
    echo $firstname;
    echo '<br/>Last Name: ';
    echo $surname;
    echo '<br/>Birthday: ';
    echo $birthday;
    echo '<br/>Age: ';
    echo $age;
    echo '<br/>Gender: ';
    echo $gender;
    echo '<br/>Citizen: ';
    echo $citizen;
    echo '<br/>Death Status: ';
    echo $death_status;
    echo '<br/>If the above is correct please: Yippy you still alive ';
}else
  echo "Something went wrong, it could be that you are using someone's ID, if that is incorrect, please email jongi@jongi.zanet";
}else 
    echo "Your ID is invalid";

}
profile($session_id, $apikey, $id_number);
?>
