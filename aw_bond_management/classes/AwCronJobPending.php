<?php

require 'AwCronJob.php';
$AwCronJob = new AwCronJob;

echo "<pre>";
print_r( $AwCronJob->processAuctions() );
echo "</pre>";

?>