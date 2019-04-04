
<?php

require_once('easychaincoin.php');

echo ' <html>
<head>
	<title>Chaincoin Island Verfication System</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

</head>
<body>';


if (isset($_POST['form_submitted'])) { //this code is executed when the form is submitted

	//check for error input
	if (!$_POST['address'] || !$_POST['message'] || !$_POST['signature'])
		die("Empty input detected.<br> <p>Go <a href='/verify'>back</a>");
        echo '<h2>Thank You </h2>';

 	$server = "localhost";
	$username = "";
	$password = "";
	$dbname = "";


	$chc_server = "localhost";
	$chc_user = "";
	$chc_pass = "";
	$chc_port = 11995;


	$conn = new mysqli($server, $username, $password, $dbname);
	// Check connection
 	if ($conn->connect_error) {
     		die("Database Connection failed:" );
     	}
	echo "DB Connected successfully";

	$chaincoin = new Chaincoin($chc_user,$chc_pass,$chc_server,$chc_port);
	$chaincoin->masternode('list');

	if ($chaincoin->error) {
  	  die("Chaincoin Connection failed: " . $chaincoin->error);
	}

	$obj = json_decode($chaincoin->raw_response);
	$arr = $obj->result;
	$ismn = 0;


	foreach ($arr as $key => $item) {
  	  if ($item->payee == $_POST['address'])
		 $ismn++ ;
	}

	$result = $conn->query("select * from whitelist");

	if ($ismn > 0) {
	  echo "<p>This address is <b><font color=green>on the masternode list</font></b>.";
	  if ($chaincoin->verifymessage($_POST['address'],  $_POST['signature'],  $_POST['message'])) {
		echo "<p>Message Verified!";
		$user = explode(":", $_POST['message']);

		$dupe = false;
		$dupe_adr = false;
	        foreach ($result as $item) {
			if ($item[username] == $user[1]) {
				$dupe = true;
				break;
			}
			elseif ($item[address] == $_POST['address']) {
				$dupe_adr = true;
				break;
			}

		}
		if (($user[0] != "chaincoinisland") || ($user[1] == ""))
			echo ("</p> Wrong message format, user NOT added to the whitelist.");
		elseif ($dupe)
			echo "<p>User: " . $user[1] . " is already registered.";
		elseif ($dupe_adr) {
			if ($conn->query("update whitelist set username =  '" . $user[1] . "' where address = '" . $_POST['address'] . "'"))
				echo "<p>Congratulations! <b>" .  $_POST['address'] . "</b> has been update.";
			else
				die("Errormessage: ". $conn->error);
		}
		else {
			if ($conn->query("insert into whitelist(address, username) values ('" . $_POST['address'] . "', '" . $user[1] . "')"))
				echo "<p>Congratulations! <b>" . $user[1] . "</b> has been added to the whitelist.";
			else
				die("Errormessage: ". $conn->error);


		}
		echo "<p>Current Chaincoin Island Masternodes Verified";
		echo "<table border=1>";
		foreach ($result as $item) {
			   echo "<tr cellspacing=3>";
			      echo "<td><a href=\"https://explorer.chaincoin.org/#Explorer/Address/" .  $item[address] ."\">" . $item[address] . "</a></td>" ;
		}
		echo "</table>";

	  }
	  else
		echo "<p>Message Verification <font color=red>failed</font>.";
	}
	else
	  echo "<p> " . $_POST['address'] . " not on the Chaincoin masternode list!";

	echo '<p>Go <a href="/">home</a> | <a href="/verify">back</a>';
}
else {
            echo '      <h2>Chaincoin Island Verification System</h2>
	    <p>Instructions:  <p>1 - Input your Chaincoin address with a 1000 CHC input.
			      <p>2 - To be whitelisted to Chaincoin Island, message format: "chaincoinisland:USERNAME", replace USERNAME with your <a href="https://minecraft.net">Minecraft®</a>(Java) username.
			      <p>3 - Signature derived from signing the message in your wallet.
	    <p>
	    <table>
            <form action="index.php" method="POST">

                <tr><td>Address:
                <td><input type="text" name="address">

                <tr><td>Message:
                <td><input type="text" name="message">

                <tr><td>Signature:
                <td><input type="text" name="signature">

		<tr><td><input type="hidden" name="form_submitted" value="1" />

                <input type="submit" value="Submit">

	    </form>
	    </table>';
}

echo '
</body>
</html>';


