<html>
  <head><title>Task 1 - Catering</title>
  </head>
  <!-- the style tag includes the css code for the page desing -->
  <style type="text/css">
    /* everything share this design */
    * {
      background-color: #cc99ff;
      font-family: "Apple Chancery", Times, serif;
      color: #333333;
      text-align: center;
    }
    /* designs table */
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
<body>
<!-- this is the heading -->
<h1>Task 1 - Catering prices</h1>
<br>
  <!-- this is the table -->
  <table align="center">
  <?php
    
    //this is an array of the values entered by the user for each of 5 catering grades
    $catering_tiers = array(
    0 => $_GET['c1'],
    1 => $_GET['c2'],
    2 => $_GET['c3'],
    3 => $_GET['c4'],
    4 => $_GET['c5'],
    );
    
    //validates input for min. and max. capacity and shows error messages if invalid.
    if (!empty($_GET['min']) && !empty($_GET['max'])) {

      $max = $_GET['max'];
      $max = filter_var($max, FILTER_VALIDATE_INT);
      $min = $_GET['min'];
      $min = filter_var($min, FILTER_VALIDATE_INT);

      if ($min === false || $max === false || $min < 1 || $max < 1) {
        exit('Error: Please enter a positive integer for minumum/maximum capacity.');
      }else if ($min > $max) {
        exit('Error: Please enter a minumum capacity that is not greater than the maximum capacity.');
      }
    }else{
      exit("Error: No value entered for minimum/maximum capacity");
    }
    
    //validates input for cost of each catering grade and shows error messages if invalid.
    for($n=0; $n<=4; $n++){
      $grade = $n+1;
      if(!empty($catering_tiers[$n])){
        $catering_tiers[$n] = filter_var($catering_tiers[$n], FILTER_VALIDATE_FLOAT);
        if ($catering_tiers[$n] === false || $catering_tiers[$n] <= 0) {
          exit("Error: Please enter a positive decimal/integer number for catering grade " . $grade . ".");
        }
      }else{
        exit("Error: No value entered for catering grade " . $grade . ".");
      }
    }
    
    //displays first column heading
    echo "<tr><th>cost per person → <br>↓ party size";
    //for loop displays the rest of the column headings that are for each catering grade
    for($i=0; $i<5; $i++){
      //displays catering grades to 2 decimal places.
      $grade = number_format($catering_tiers[$i], 2, '.', '');
      echo "<th>£$grade</th>";
    }
    echo "</tr>";
    
    //calculates number of rows for capacity needed 
    $size_diff = $max - $min;
    $num_rows = $size_diff / 5;
    //Outer for loop runs creating that many rows, each is for a different party size, they go up in +5, going down from
    //min to max entered by user 
    for($j=0; $j<=$num_rows; $j++){
      $num = $min + ($j*5);
      echo "<tr><th>$num</th>";
      //inner for loop calculates the cost for each cell 
      for($k=0; $k<5; $k++){
        $cost = ((int) $catering_tiers[$k]) * $num;
        echo "<td>£$cost</td>";
      }
      echo "</tr>";
    }
    ?>
  </table>
</body>
</html>