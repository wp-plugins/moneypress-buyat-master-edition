<?php

/**
 * This is a test script for the eBay driver.
 */

require_once('../Panhandler.php');
require_once('../Drivers/Ticketmaster.php');

$ticketmaster = new TicketmasterDriver("01-8565fdd6e88a0738cf0f05692ff5398f");

echo "Ticketmaster Driver suports options...\n";

var_dump($ticketmaster->get_supported_options());

echo "\nFetching default data...\n";

$products = $ticketmaster->get_products(
            array('keywords' => array('tool'))
        );


foreach ($products as $p) {
    echo $p->name,"\t",$p->price,"\t",$p->web_urls,"\n";
}


?>
