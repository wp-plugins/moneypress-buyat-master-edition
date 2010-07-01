<?php

/**
 * This is a test script for the BuyAt driver.  If you run this script
 * from the command line you will see a list of stuff.
 */

require_once('../Drivers/buyat.php');

$DataProvider     = new BuyAtPanhandler('01-8565fdd6e88a0738cf0f05692ff5398f');


print "name\t price\t weburl\t imgurl\t \n";
$products = $DataProvider->get_products_by_keywords(array('tool'));
foreach ($products as $p) {
    printf ("%s\t %s\t %s\t %s\t \n",$p->name,$p->price,$p->web_urls,$p->image_urls);
}


print "name\t price\t weburl\t imgurl\t \n";
$products = $DataProvider->get_products_by_keywords(array('roger waters'));
foreach ($products as $p) {
    printf ("%s\t %s\t %s\t %s\t \n",$p->name,$p->price,$p->web_urls,$p->image_urls);
}

print "name\t price\t weburl\t imgurl\t \n";
$products = $DataProvider->get_products_by_keywords(array('aventura'));
foreach ($products as $p) {
    printf ("%s\t %s\t %s\t %s\t \n",$p->name,$p->price,$p->web_urls,$p->image_urls);
}
?>
