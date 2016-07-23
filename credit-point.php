<style>
td{ width:109px;text-align:center;}
th {width:152px; }
</style>
<?php

if ( isset( $_GET['id'] ) )
{
$con=mysqli_connect("localhost","root","","storeathome");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }


$query = "SELECT * FROM sh_customer_credit_point WHERE customer_id = '". $_GET['id'] ."' ORDER BY created_time ASC";

$result = mysqli_query($con,$query) or die("Couldn't execute query");
echo "<table>";
echo "<tr>";
echo " <th>Customer ID</th>";
echo "<th>Order Number</th>";
echo "<th>Applied Credit Point</th>";
echo "<th>Applied Credit Point Price</th>";
echo "<th>Time</th>";
echo "</th>";

while ($row= mysqli_fetch_array($result,MYSQLI_NUM))
{
$customerid = $row[1];
$orderid = $row[2];
$appliedpoint = $row[3];
$pointprice = $row[4];
$time = $row[7];


echo "<tr>";
echo "<td>$customerid</td>";
echo "<td> $orderid</td>";
echo "<td>$appliedpoint</td>";
echo "<td>$pointprice</td>";
echo "<td>$time</td>";
echo "</tr>";

$count++ ;
} // end WHILE
echo"</table>";

} // end IF
?>
