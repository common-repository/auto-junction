<?php
/**
 * @package Auto_Junction
 * @version 1.2
 */
/*
Plugin Name: Auto Junction
Plugin URI: http://www.techburnett.com/projects/auto-junction
Description: This plugin will find products and display them at the bottom of your blog posts or where you want them displayed. Using the custom fields for each post you enter the keywords and this plugin will get a list of products related to those keywords. This way when a blog post is viewed you can also show affiliate products related to that post.
Author: Jim Burnett
Version: 1.3
Author URI: http://www.techburnett.com
*/





function find_aj_products($keywords,$count)
{
$cj_webid='';
$CJ_DevKey= '';



$currency="USD";



/* Don't edit below this unless you know what you are doing. */
$affItems = array();
require_once("IXR_Library.php");
$advs="joined";
$targeturl="https://product-search.api.cj.com/v2/product-search?";
$targeturl.="website-id=$cj_webid";
$targeturl.="&keywords=" .$keywords;
$targeturl.="&advertiser-ids=$advs";
$targeturl.="&records-per-page=" . $count;
$ch = curl_init($targeturl);
curl_setopt($ch, CURLOPT_POST, FALSE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: '.$CJ_DevKey)); // send development key
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$response = curl_exec($ch);
$xml = new SimpleXMLElement($response);

curl_close($ch);

if ($xml)
{
foreach ($xml->products->product as $item) 
{

//print_r($item,false);
$adid = $item->xpath('ad-id');
$adid = (string)$adid[0];

$buy_url  = $item->xpath('buy-url');
$buy_url  = (string)$buy_url[0];
 
$title = $item->xpath('name');
$title = (string)$title[0]; 


$imgURL = $item->xpath('image-url');
$imgURL = (string)$imgURL[0];
 
$theprice = $item->xpath('price');
$theprice = (string) $theprice[0];
 
$merchantname = $item->xpath('advertiser-name');
$merchantname = (string)$merchantname[0];
 
$description = $item->xpath('description');
$description = (string)$description[0];


$p = new affItem();
$p->title = $title;
$p->price = $theprice;
$p->url_img = $imgURL;
$p->url_buylink = $buy_url;
$p->desc = $description;

array_push($affItems,$p);


}

}


return $affItems;
}

class affItem
{
   var $title;
   var $price;
   var $url_img;
   var $url_buylink;
   var $descr;

}


function aj_show_one($showdesc=0)
{
	if (is_single())
	{
$cj_keywords = get_post_custom_values('cj_keywords');

if ( $cj_keywords[0] != "")
{
 $affItems = find_aj_products($cj_keywords[0],1);
 foreach ($affItems as $affItem)
 {
  echo '<div class="aj-item">';
  echo '<div class="aj-title"><a href="' . $affItem->url_buylink . '" target="_new" rel="nofollow">' . $affItem->title . '</a></div>';
  if ($affItem->url_img != "")
  {
   echo '<div class="aj-img"><a href="' . $affItem->url_buylink . '" target="_new" rel="nofollow">' .'<img border="0" width="120" src="' . $affItem->url_img . '" alt="' . $cj_keywords[0] . '" /></a></div>';
  }
  echo '<div class="aj-meta">$' . $affItem->price;
  echo ' - <a href="' . $affItem->url_buylink . '" target="_new" rel="nofollow" >Buy It</a>';
  echo '</div></div>';
 }
}
}
}
function aj_show($showdesc=0)
{
	if (is_single())
	{
$cj_keywords = get_post_custom_values('cj_keywords');

if ( $cj_keywords[0] != "")
{
 $affItems = find_aj_products($cj_keywords[0],6);
 foreach ($affItems as $affItem)
 {
  echo '<div class="aj-item">';
  echo '<div class="aj-title"><a href="' . $affItem->url_buylink . '" target="_new" rel="nofollow">' . $affItem->title . '</a></div>';
  if ($affItem->url_img != "")
  {
   echo '<div class="aj-img"><a href="' . $affItem->url_buylink . '" target="_new" rel="nofollow">' .'<img border="0" width="120" src="' . $affItem->url_img . '" alt="' . $cj_keywords[0] . '" /></a></div>';
  }
  echo '<div class="aj-meta">$' . $affItem->price;
  echo ' - <a href="' . $affItem->url_buylink . '" target="_new" rel="nofollow" >Buy It</a>';
  echo '</div></div>';
 }
}
}
}
?>
