
<?php
//this tells the system that it's no longer just parsing html; it's now parsing PHP

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = NULL; // edit the login credentials in connectToDB()
$show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

function debugAlertMessage($message) {
    global $show_debug_alert_messages;

    if ($show_debug_alert_messages) {
        echo "<script type='text/javascript'>alert('" . $message . "');</script>";
    }
}

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
    //echo "<br>running ".$cmdstr."<br>";
    global $db_conn, $success;

    $statement = OCIParse($db_conn, $cmdstr);
    //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
        echo htmlentities($e['message']);
        $success = False;
    }

    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) {
        echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
        $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
        echo htmlentities($e['message']);
        $success = False;
    }

    // echo $statement . "WHAT IN THE";
    return $statement;
}

function executeBoundSQL($cmdstr, $list) {
    /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
In this case you don't need to create the statement several times. Bound variables cause a statement to only be
parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
See the sample code below for how this function is used */

    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        echo htmlentities($e['message']);
        $success = False;
    }

    foreach ($list as $tuple) {
        foreach ($tuple as $bind => $val) {
            //echo $val;
            //echo "<br>".$bind."<br>";
            OCIBindByName($statement, $bind, $val);
            unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
        }

        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br> Cannot execute the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
            echo htmlentities($e['message']);
            echo "<br>";
            $success = False;
        }
    }
}

function printResult($result) { //prints results from a select statement
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
          echo "<tr>" . "<td>" . $row[0] . "</td> <td>" . $row[1] . "</td> <td>" . $row[2] . "</td> <td>" . $row[3] . "</td> <td>" . $row[4] . "</td> <td>" . $row[5] . "</td> </tr>"; //or just use "echo $row[0]"
    }
}

function printResultProject($result) { //prints results from a select statement
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
          echo "<tr>" . "<td>" . $row[0] . "</td> <td>" . $row[1] . "</td> <td>" . $row[2] . "</td> <td>" . $row[3] . "</td> </tr>";
    }
}

function connectToDB() {
    global $db_conn;

    // Your username is ora_(CWL_ID) and the password is a(student number). For example,
    // ora_platypus is the username and a12345678 is the password.
    $db_conn = OCILogon("ora_phillngs", "a18569673", "dbhost.students.cs.ubc.ca:1522/stu");

    if ($db_conn) {
        debugAlertMessage("Database is Connected");
        return true;
    } else {
        debugAlertMessage("Cannot connect to Database");
        $e = OCI_Error(); // For OCILogon errors pass no handle
        echo htmlentities($e['message']);
        return false;
    }
}

function disconnectFromDB() {
    global $db_conn;

    debugAlertMessage("Disconnect from Database");
    OCILogoff($db_conn);
}

function handleUpdateRequest() {
    global $db_conn;

    // $old_name = $_POST['oldName'];
    // $new_name = $_POST['newName'];

    $agentID = $_POST['agentid'];
    $lawyerID = $_POST['lawyerid'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $rating = $_POST['rating'];
    $salary = $_POST['salary'];
    $query = "UPDATE agentRepresents SET";
    //
    if ($lawyerID != "") {
        $query = $query . " lawyerID='".$lawyerID."'";
    } else if ($phone != "") {
        $query = $query .  " phone='".$phone."'";
    } else if ($email != ""){
        $query = $query .  " email='".$email."'";
    } else if ($rating != "") {
        $query = $query .  " rating='".$rating."'";
    } else if ($salary != ""){
        $query = $query .  " salary='".$salary."'";
    }
    $query = $query .  " WHERE agentID='" . $agentID . "'";


    echo $query;
    // you need the wrap the old name and new name values with single quotations
    // executePlainSQL("UPDATE agentRepresents SET name='" . $new_name . "' WHERE name='" . $old_name . "'");
    executePlainSQL($query);
    OCICommit($db_conn);
}

function handleResetRequest() {
    global $db_conn;
    // Drop old table
    executePlainSQL("DROP TABLE demoTable");

    // Create new table
    echo "<br> creating new table <br>";
    executePlainSQL("CREATE TABLE demoTable (id int PRIMARY KEY, name char(30))");
    OCICommit($db_conn);
}

function handleInsertRequest() {
    global $db_conn;

    //Getting the values from user and insert data into the table
    $tuple = array (
        ":bind1" => $_POST['agentid'],
        ":bind2" => $_POST['lawyerid'],
        ":bind3" => $_POST['phone'],
        ":bind4" => $_POST['email'],
        ":bind5" => $_POST['rating'],
        ":bind6" => $_POST['salary'],
    );

    // echo  $_POST['agentid'] . $_POST['lawyerid'] .$_POST['phone'] .$_POST['email'] . $_POST['rating'] . $_POST['salary'];

    $alltuples = array (
        $tuple
    );

    executeBoundSQL("insert into agentRepresents values (:bind1, :bind2, :bind3, :bind4, :bind5, :bind6)", $alltuples);
    OCICommit($db_conn);
}

function handleCountRequest() {
    global $db_conn;

    $result = executePlainSQL("SELECT Count(*) FROM demoTable");

    if (($row = oci_fetch_row($result)) != false) {
        echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
    }
}

function handleSelectRequest() {
    global $db_conn;

    $variable = $_POST['value'];
    $query = "SELECT * FROM agentRepresents WHERE" ;

    if(isset($_POST['attribute'])){
        $selectAttribute = $_POST['attribute'];
        if($selectAttribute == "0") {
            $query = $query .  " salary ";
        } else if ($selectAttribute == "1") {
            $query = $query . " rating ";
        }
    }

    if(isset($_POST['comparison'])){
        $selectComparison = $_POST['comparison'];
        if($selectComparison == "0") {
            $query = $query .  " > ";
        } else if ($selectComparison == "1") {
            $query = $query . " < ";
        } else if ($selectComparison == "2") {
            $query = $query . " = ";
        }
    }

    $query = $query . " $variable ";
    $result = executePlainSQL($query);
    printResult($result);
}

function handleDeleteRequest() {
    global $db_conn;

    $agentID = $_POST['agentid'];

    $query = "DELETE FROM agentRepresents WHERE agentID='" . $agentID . "'";
    echo $query;
    executePlainSQL($query);

}

// HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('selectTupleRequest', $_POST)) {
            // echo "select tuple\n";
            handleSelectRequest();
        } else if (array_key_exists('insertTupleRequest', $_POST)) {
            // echo "insert tuple";
            handleInsertRequest();
        } else if (array_key_exists('updateTupleRequest', $_POST)) {
            // echo "update tuple\n";
            handleUpdateRequest();
        } else if (array_key_exists('deleteTupleRequest', $_POST)) {
            // echo "delete tuple\n";
            handleDeleteRequest();
        }

        disconnectFromDB();
    }
}

// HANDLE ALL GET ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handleGETRequest() {
    if (connectToDB()) {
        if (array_key_exists('countTuples', $_GET)) {
            handleCountRequest();
        }
        disconnectFromDB();
    }
}

if (isset($_POST['select']) || isset($_POST['insert']) || isset($_POST['update'])|| isset($_POST['delete'])) {
    handlePOSTRequest();
} else if (isset($_GET['countTupleRequest'])) {
    handleGETRequest();
} else if (isset($_POST['project'])) {
   if(connectToDB()) {
           $result = executePlainSQL("select agentID, email, phone from agentRepresents");
           printResultProject($result);
       }
 }
 else {
    if(connectToDB()) {
        $result = executePlainSQL("select * from agentRepresents");
        printResult($result);
    }
}
?>
