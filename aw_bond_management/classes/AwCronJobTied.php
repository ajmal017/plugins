<?php
require 'AwCronJob.php';
$AwCronJob = new AwCronJob;

/*Code for the Tired Auctions. */

echo "<pre>";
print_r( $AwCronJob->processTiedAuctions() );
echo "</pre>";

?>