<?php

require_once(__DIR__ . '/includes/config.php');
$ret = null;
$db = new Database;

echo "Last 5: <br> ";
$ret = $db->query("SELECT * from hq_payments;");

$i = 1;
while($row = $ret->fetchArray(SQLITE3_ASSOC) )
{
    echo "Var dump for result number $i <br />";
    var_dump($row);
    echo "<br /><br/>";
    $i++;
}




