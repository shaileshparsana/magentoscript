<?php 

$Rent = 250;
function Expenses($Other)
{
   $Rent = 250 + $Other;
   return $Rent;
}
Expenses(50);
echo $Rent;

?>