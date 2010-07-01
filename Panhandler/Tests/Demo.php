<?php
/******************************************************************************

 Copyright (c) 2010, Perfiliate Technologies Ltd
 All rights reserved.

 Redistribution and use in source and binary forms, with or without
 modification, are permitted provided that the following conditions are met:
  * Redistributions of source code must retain the above copyright notice, this
    list of conditions and the following disclaimer.
  * Redistributions in binary form must reproduce the above copyright notice,
    this list of conditions and the following disclaimer in the documentation
    and/or other materials provided with the distribution.
  * Neither the name of Perfiliate Technologies Ltd nor the names of its
    contributors may be used to endorse or promote products derived from this
    software without specific prior written permission.
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
 *****************************************************************************/
/**
 * Path to BuyatAPIClient.php and Entities.php files.
 * You may edit this to actual path of the files e.g $path = 'buyat_api_client-php5-1.0/'; 
 * 
 */
$path = '../Drivers/';

/**
 * Include the affiliate API client
 */
require_once($path.'BuyatAPIClient.php');
require_once($path.'Entities.php');

try
{
  /**
   * The API Key passed here is  from our demo accounts. 
   *  You need to change this to your own key.
   */
  $client = new BuyatAPIClient('01-8565fdd6e88a0738cf0f05692ff5398f');
  
  echo "\n*********** Echo ************\n";
  echo $client->test_echo('Hello');
  
  echo "\n*********** Level 1 Categories ************\n" ;
  echo "List of level 1 categories: Show only  'category_id, category_name' fields \n";
  $categories = $client->listLevel1Categories();
  foreach($categories as $category)
  {
    echo "Category:  {$category->getCategoryId()}, {$category->getCategoryName()} \n";
  }
  
  echo "\n*********** Level 2 Categories ************\n" ;
  echo "List of level 2 categories: Show only  'category_id, category_name' fields for parent_category_id = 28  \n";
  $categories = $client->listLevel2Categories(28); 
  foreach($categories as $category)
  {
    echo "Category:  {$category->getCategoryId()}, {$category->getCategoryName()} \n";
  }
  
  echo "\n***********  Category Tree ************\n" ;
  echo "Category tree: Show only 'category_id, category_name, subcategories' fields  \n";
  $categoryTree = $client->categoryTree();
  
  foreach($categoryTree as $category)
  {
    $cat = "Category: {$category->getCategoryId()}, {$category->getCategoryName()}"; 
    if($category->getSubcategories())
    {
      $cat .= "\n\tsubcategories\n";
      foreach ($category->getSubcategories() as $subCategory)
      {
        $cat .=  "\t\t{$subCategory->getCategoryId()}, {$subCategory->getCategoryName()} \n"; 
      }
      
    }
       
    echo $cat;
  }
  
  echo "\n*********** Programme List ************\n" ;
  echo "List of programmes: Show only 'programme_id, programme_name, programme_url, has_feed' fields \n";
  $programmes = $client->listProgrammes();
  foreach($programmes as $programme)
  {
    echo "Programme:  {$programme->getProgrammeID()}, {$programme->getProgrammeName()}, {$programme->getProgrammeUrl()}, {$programme->getHasFeed()} \n"; 
  }
  
  echo "\n*********** Get Programme Info ************\n";
  echo "Programme info for  programme_id ='665' : Show only 'programme_id, programme_name, programme_url, has_feed' fields \n";
  $programme = $client->getProgramme(665);
  echo  "Got programme: {$programme->getProgrammeID()}, {$programme->getProgrammeName()}, {$programme->getProgrammeUrl()}, {$programme->getHasFeed()} \n"; 
  
  echo "\n*********** Feed List ************\n" ;
  echo "List of feeds: Show only  'feed_id, feed_name, number_of_products, last_updated' fields \n";
  $feeds = $client->listFeeds();
  
  foreach($feeds as $feed)
  {
    echo "Feed:  {$feed->getFeedID()}, {$feed->getFeedName()}, {$feed->getNumberOfProducts()}, {$feed->getLastUpdated()}  \n";
  }
  
  echo "\n*********** Get Feed Info ************\n";
  echo "Feed info for  feed_id ='381' : Show only  'feed_id, feed_name, number_of_products, last_updated' fields \n";
  $feed = $client->getFeed(381);
  echo "Feed: {$feed->getFeedID()}, {$feed->getFeedName()}, {$feed->getNumberOfProducts()}, {$feed->getLastUpdated()}  \n";
  
  echo "\n*********** Feed Download URL ************\n" ;
  echo "Feed URL for feed 381  using defaults for optional values except setting  \n";
  echo "Got url: {$client->getFeedUrl(381)} \n";; 
  
  echo "\nFeed URL for feed 381, format='XML', start=0, perpage=50, lid='BuyAtDemo', use_https='yes', reverse_map_xml='yes'   \n";
  echo "Got url: {$client->getFeedUrl(381, 'XML', 0, 50, 'BuyAtDemo', 'yes')}  \n"; 
  
  echo "\n*********** Product Search ************\n";
  echo "Show only 'product_id, product_name, product_url, price' fields \n";
  $products = $client->searchProducts('alice');
  echo "Searching for alice...\n"; 
  echo "Total Results found $products[total_results] \n";
  echo "Retrieved $products[current_results] \n"; 
  
  foreach($products['products'] as $product)
  {
    echo "\nProduct: {$product->getProductId()}, {$product->getProductName()}, {$product->getProductURL()},  {$product->getCurrency()} {$product->getOnlinePrice()} \n";
    $lastProductID=$product->getProductId();
  }
  
  echo "\n*********** Get Product Info ************\n";
  echo "Product info for  product_id ='$lastProductID' : Show only 'product_id, product_name, product_url, price' fields \n";
  $product = $client->getProduct($lastProductID);
  echo "GotProduct:  {$product->getProductId()}, {$product->getProductName()}, {$product->getProductURL()},  {$product->getCurrency()} {$product->getOnlinePrice()} \n";  
   
  
  //echo "\n*********** Create DeepLink ************\n";
  //echo "DeepLink for  'http://www.virginmobile.com/vm/home.do' \n";
  //echo  "Got url: {$client->createDeeplink('http://www.virginmobile.com/vm/home.do')} \n"; 
}   
   
catch (BuyatException  $e)
{
  echo $e->getMessage(); 
}
   