<html>
  <head><title>Task 3 - Capacity</title>
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
<h1>Task 3 - Venue Capacity</h1>
<br>
  <?php
  //validates min. and max. capacity entered and displays error message if invalid
  if(!empty($_GET['minCapacity']) && !empty($_GET['maxCapacity'])){
    
    $min_capacity = $_GET['minCapacity'];
    $min_capacity = filter_var($min_capacity, FILTER_VALIDATE_INT);
    $max_capacity = $_GET['maxCapacity'];
    $max_capacity = filter_var($max_capacity, FILTER_VALIDATE_INT);
    
    if ($min_capacity === false || $max_capacity === false || $min_capacity < 1 || $max_capacity < 1) {
      exit("Error: Please enter a positive integer for the minumum/maximum capacity.");
    }
  }else{
    exit("Error: No value entered for minimum/maximum capacity.");
  } 

  //connecting to database (lines 52 to 81)
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
  //this is the sql query that filters out the venues with wrong capacity or that don't have a license
  $sql = "SELECT name, capacity, weekday_price, weekend_price, licensed 
    FROM venue 
    WHERE capacity <= '$max_capacity' AND capacity >= '$min_capacity' AND licensed = 1";
  
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
    <th>Capacity</th>
    <th>Weekend price (£)</th>
    <th>Weekday price (£)</th>
  </tr>
  <?php
  //while loop goes through each row of $res and displays results in table.
  while($row = $res->fetchRow()){
    $name = $row[strtolower('name')];
    echo "<tr><td>$name</td>";
    $capacity = $row[strtolower('capacity')];
    echo "<td>$capacity</td>";
    $weekend_price = $row[strtolower('weekend_price')];
    echo "<td>£$weekend_price</td>";
    $weekday_price = $row[strtolower('weekday_price')];
    echo "<td>£$weekday_price</td></tr>";
  }
  ?>
  </table>
</body>
</html>