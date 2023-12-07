<html>
  <head><title>Task 4 - Costs</title>
  <!-- the style tag includes the css code for the page desing -->
  <style type="text/css">
    /* everything shares this design */
    * {
      background-color: #cc99ff;
      font-family: "Apple Chancery", Times, serif;
      color: #333333;
      text-align: center;
    }
    /* table design */
    table {
      border-collapse: collapse;
    }
    table, th, td {
      border: 2px solid #333333;
    }
    th {
      padding: 15px;
      font-size: 16px;
      background-color: #66b5ff;
    }
    td {
      padding: 15px;
      font-size: 14px;
      background-color: #cce6ff;
    }
  </style>
</head>
<body>
<!-- this is the heading -->
<h1>Task 4 - Venue Capacity</h1>
<br>
  <?php
  
  //validates the input for date and converts it, gives error message if invalid
  if(!empty($_GET['date'])){
    $dateInput = $_GET['date'];
    if(strlen($dateInput) >= 5){
      //checks if date input is in DD/MM/YYYY format with slashes, and if so changes it to the american MM/DD/YYYY format.
      if($dateInput[2] == '/' && $dateInput[5] == '/' ){
          $day = substr("$dateInput", 0, 2);
          $month = substr("$dateInput", 3, 2);
          $year = substr("$dateInput", 6, 4);
          $dateInput =  "$month/$day/$year";
      }
    }
    //gets time stamp for date
    $timeStamp = strtotime("$dateInput");
    //if the date input is not a real date, strtotime() returns false, so this is used to validate the date
    if($timeStamp == false){
      exit("Error: Please enter a valid date.");
    }
  }else{
    exit("Error: No value entered for date.");
  } 
  
  //changes the date input into YYYY-MM-DD form.
  //NOTE: this program doesn't work if the date enetered is in the form DD/MM/YYYY as strtotime() will interpret this 
  //as the american format which is MM/DD/YYYY. This is why the code above checks for this and rearranges the input 
  //into the american format that is interpreted correctly by strtotime().
  $date = date('Y-m-d', strtotime("$dateInput"));
  
  //validates input for party size and returns error message if invalid
  if(!empty($_GET['partySize'])){
    
    $party_size = $_GET['partySize'];
    $party_size = filter_var($party_size, FILTER_VALIDATE_INT);
    
    if ($party_size === false || $party_size < 1) {
      exit("Error: Please enter a positive integer for the party size.");
    }
  }else{
    exit("Error: No value entered for party size.");
  } 
     
  //connecting to database (lines 79 to 111)
  require_once 'MDB2.php';
  //use of another file to store username and password for security
  include "security-details.php";

  $host='localhost';
  $dbName='coa123wdb';

  $dsn = "mysql://$username:$password@$host/$dbName";
  $db =& MDB2::connect($dsn);

  if(PEAR::isError($db)){ 
      die($db->getMessage());
  }

  //setting fetch mode to an associative array
  $db->setFetchMode(MDB2_FETCHMODE_ASSOC);

  //this is the sql query that filters out the venues with wrong capacity and those who are not available on the given date
  $sql = "SELECT venue_id, name, capacity, weekday_price, weekend_price 
    FROM venue 
    WHERE (capacity >= '$party_size') 
    AND (venue_id NOT IN (SELECT venue_booking.venue_id FROM venue_booking WHERE date_booked='$date'))";
  
  //stores query result
  $res =& $db->query($sql);
  //checks if no results were found and returns message if this is the case
  if($res->numRows() == 0){
    exit("No results found for your search.");
  }

  if(PEAR::isError($res)){
      die($res->getMessage());
  }
  ?>
  <table align="center">
  <tr>
    <!-- table headings -->
    <th>Name</th>
    <th>Price (£)</th>
  </tr>
  <?php
  //loops through each row in $res and displays results in table
  while($row = $res->fetchRow()){
    $name = $row[strtolower('name')];
    echo "<tr><td>$name</td>";
    //checks if date is weekend or weekday and sets $price to appropriate price. This is done by the use of date() with 'N'
    //as the first parameter that returns a number from 1-7 for the 7 days of the week. Therefore, if it returns 6 or 7 the 
    //date is a weekend.
    if(date('N', strtotime("$dateInput")) >= 6){
      $price = $row[strtolower('weekend_price')];
    }else{
      $price = $row[strtolower('weekday_price')];
    }
    echo "<td>£$price</td></tr>";
  }
  ?>
  </table>
</body>
</html>