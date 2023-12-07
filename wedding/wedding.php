<html>
<head><title>Task 5 - Wedding</title>
  <!-- the style tag includes the css code for the page desing -->
  <style type="text/css">
    /* everything shares this design */
    * {
      background-color: #cc99ff;
      font-family: "Apple Chancery", Times, serif;
      color: #333333;
    }
    /* styling text displayed on page */
    h1, p {
      text-align: center;
    }
    /* results table design */
    table.t1 {
      border-collapse: collapse;
      text-align: center;
    }
    table.t1, th.t1, td.t1 {
      border: 2px solid #333333;
    }
    th.t1 {
      padding: 15px;
      font-size: 16px;
      background-color: #ffb3d7;
    }
    td.t1 {
      padding: 15px;
      font-size: 14px;
      background-color: #ffe6f2;
    }
    /* t2 and t3 design the invisible table for the form */
    td.t2 {
      padding-bottom: 15px;
    }
    td.t3{
      padding-bottom: 15px;
      padding-right: 100px;
    }/* designs the text boxes */
    input[type=text] {
      box-sizing: border-box;
      padding: 5px 10px;
      border: 1px solid black;
      border-radius: 4px;
      background-color: floralwhite;
    }
    /* highlights text box when selected */
    input[type=text]:focus {
      background-color: #ffe6f2;
    }
    /* designs the drop down box */
    select{
      background-color: floralwhite;
      width: 25%;
      padding: 3px 3px;
      border: 1px solid black;
      border-radius: 4px;
    }
    /* highlights drop down box when selected */
    select:focus{
      background-color: #ffe6f2;
    }
    /* deisgns the submit button */
    input[type=button], input[type=submit], input[type=reset] {
      background-color: floralwhite;
      border: 1px solid black;
      border-radius: 4px;
      color: black;
      padding: 8px 13px;
      width: 50%;
    }
  </style>
</head>
<body>
<!-- this is the heading -->
<h1>Wedding Planner</h1>
<br>
  <?php
  //Displays result when the submit button is pressed
  if(isset($_REQUEST['submit'])){
    //validates date input and returns error message if invalid
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
        exit("<p>Error: Please enter a valid date.</p>");
      }
    }else{
      exit("<p>Error: No value entered for date.</p>");
    } 

    //changes the date input into YYYY-MM-DD form.
    //NOTE: this program doesn't work if the date enetered is in the form DD/MM/YYYY as strtotime() will interpret this 
    //as the american format which is MM/DD/YYYY. This is why the code above checks for this and rearranges the input 
    //into the american format that is interpreted correctly by strtotime().
    $date = date('Y-m-d', strtotime("$dateInput"));
    
    //validates input for the party size and returns error message if invalid
    if(!empty($_GET['party_size'])){
      
      $party_size = $_GET['party_size'];
      $party_size = filter_var($party_size, FILTER_VALIDATE_INT);

      if ($party_size === false || $party_size < 1) {
        exit("<p>Error: Please enter a positive integer for the party size.</p>");
      }
    }else{
      exit("<p>Error: No value entered for party size.</p>");
    }  
    //gets chosen catering grade
    $catering_grade = $_REQUEST['catering_grade'];

    //connecting to database (lines 126 to 160)
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
    //sql query that selects the venue information of venues that can accommodate for the capacity entered and that are also
    //available on the chosen date and offer the catering grade chosen
    $sql = "SELECT venue.venue_id, name, capacity, weekday_price, weekend_price, cost, licensed 
      FROM venue 
      INNER JOIN catering 
      ON venue.venue_id = catering.venue_id
      WHERE (capacity >= $party_size) 
      AND (venue.venue_id IN (SELECT venue_id FROM catering WHERE grade=$catering_grade))
      AND (venue.venue_id NOT IN (SELECT venue_id FROM venue_booking WHERE date_booked='$date'))
      AND (grade=$catering_grade)";
    //stores query result
    $res =& $db->query($sql);
    //checks if no results were found and returns message if this is the case
    if($res->numRows() == 0){
      exit("<p>No results found for your search.</p>");
    }

    if(PEAR::isError($res)){
        die($res->getMessage());
    }
    //created results table headings
    echo "<table align='center' class='t1'>
      <tr class='t1'>
        <th class='t1'>Name</th>
        <th class='t1'>Licensed</th>
        <th class='t1'>Venue Price (£)</th>
        <th class='t1'>Catering Cost (£)</th>
        <th class='t1'>Total Cost (£)</th>
        <th class='t1'>Capacity</th>
      </tr>";
    
    //loop creates results table rows, each for a venue result
    while($row = $res->fetchRow()){
      //checks if date is weekend or weekday and sets $price to appropriate price. This is done by the use of date() with 'N'
      //as the first parameter that returns a number from 1-7 for the 7 days of the week. Therefore, if it returns 6 or 7 the 
      //date is a weekend. 
      if(date('N', strtotime("$date")) >= 6){
        $price = $row[strtolower('weekend_price')];
      }else{
        $price = $row[strtolower('weekday_price')];
      }
      //calculates price of catering 
      $cateringPrice = $party_size*$row[strtolower('cost')];
      //Used to display 'Yes' or 'No' under licenced in the table instead of 
      //1 or 0.
      if($row[strtolower('licensed')] == 1){
        $licence = "Yes";
      }else{
        $licence = "No";
      }
      //displays the venue name, whether the venue is licensed, venue price, price of catering for the entered capacity at the catering grade chosen, total cost and the capacity of the venue
      $name = $row[strtolower('name')];
      $capacity = $row[strtolower('capacity')];
      $totalCost = $cateringPrice + $price;
      echo "<tr class='t1'><td class='t1'>$name</td>";
      echo "<td class='t1'>$licence</td>";
      echo "<td class='t1'>£$price</td>";
      echo "<td class='t1'>£$cateringPrice</td>";
      echo "<td class='t1'>£$totalCost</td>";
      echo "<td class='t1'>$capacity</td></tr>";
    }
    
    echo "</table>";
      
  }else{
    
  ?>
  <!-- form that returns result to this file, running the php code above -->
  <form action="<?= $_SERVER['PHP_SELF'] ?>" method="get">
    <!-- the form labels and inputs are in an invisible table to create symmetry for a better display -->
    <table align='center' class='t2'>
      <tr>
        <!-- Text box for date entry -->
        <td class='t3'><label for="date"><b>Enter date:</b></label></td>
        <td class='t2'><input name="date" type="text" id="date" size="25" /></td>
      </tr>
      <tr>
        <!-- text box for party size entry -->
        <td class='t3'><label for="party_size"><b>Enter party size:</b></label></td>
        <td class='t2'><input name="party_size" type="text" id="party_size" size="25" /></td>
      </tr>
      <tr>
        <!-- drop down box for selecting catering grade -->
        <td class='t3'><label for="catering_grade"><b>Select a catering grade:</b></label></td>
        <td class='t2'><select id="catering_grade" name="catering_grade">
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5</option>
          </select></td>
      </tr>
      <!-- submit button -->
      <tr><td></td><td><input type="submit" name="submit" id="submit" value="Submit"/></td></tr>
    </table>
  </form>
  <?php
  }
  ?>
</body>
</html>