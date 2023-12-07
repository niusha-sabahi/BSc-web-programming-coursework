<html>
  <head><title>Task 2 - Details</title>
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
<h1>Task 2 - Venue Details</h1>
<br> 
  <?php
  //validates input for venue ID and displays error message if invalid
  if(!empty($_GET['venueId'])){
    $venue_ID = $_GET['venueId'];
    $venue_ID = filter_var($venue_ID, FILTER_VALIDATE_INT);
    if ($venue_ID === false) {
      exit("Error: Please enter an integer for the venue ID.");
    }else if($venue_ID > 10 || $venue_ID < 1){
      exit("Error: Please enter an integer for the venue ID bwtween 1-10.");
    }
  }else{
    exit("Error: No value entered for venue ID.");
  } 
  ?>
  <!-- table headings -->
  <table align="center">
    <tr>
      <!-- these are the table headings -->
      <th>Venue ID</th>
      <th>Name</th>
      <th>Capacity</th>
      <th>Weekend price (£)</th>
      <th>Weekday price (£)</th>
      <th>Licensed</th>
    </tr>
    <tr>
    <?php 
    //connecting to the database and retreiving data (line 63 to 86)  
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
    //query that returns the row of details for chosen venue ID
    $sql = "SELECT * FROM venue WHERE venue_id='$venue_ID'";
    //stores query result
    $res =& $db->query($sql);
  
    if(PEAR::isError($res)){
        die($res->getMessage());
    }

    $row = $res->fetchRow();

    $weekend_price = $row[strtolower('weekend_price')];
    $weekday_price = $row[strtolower('weekday_price')];
    
    //Used to display 'Yes' or 'No' under licenced in the table instead of 
    //1 or 0.
    if($row[strtolower('licensed')] == 1){
      $licence = "Yes";
    }else{
      $licence = "No";
    }
      
    //creates array of details of chosen venue 
    $venue_details = array(
    0 => $row[strtolower('venue_id')],
    1 => $row[strtolower('name')],
    2 => $row[strtolower('capacity')],
    3 => "£$weekend_price",
    4 => "£$weekday_price",
    5 => $licence,
    );
      
    //loop goes through array, displaying each detail as a cell
    foreach($venue_details as $det) {
      echo "<td>$det</td>";
    }
    ?>
  </tr>
  </table>
</body>
</html>