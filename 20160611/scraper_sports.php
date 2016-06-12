<?php
error_reporting(E_ALL);
/**
 * Created by PhpStorm.
 * User: jghyde
 * Date: 6/11/16
 * Time: 3:03 PM
 */
/**
 * Configuration for this scraper
 * Scrapes from Cobalt car dealership websites
 * such as MegaCars.com
 */
// Variables for customoizations
$save_json_file = 'sports.json';
$image_dir = 'images/sports';
$url_to_fetch = 'http://www.megacars.com/VehicleSearchResults?model=Camaro&model=Challenger&model=Charger&search=preowned&paymentTerm=monthly&make=Dodge&make=Chevrolet';
// ClickMeter.com API key:
$api_key = '8B44194D-10AB-47CD-B89B-20CA322D46B7';
// Clickmeter tracking link group id, or campaign id
$clickmeter_group_id = '393197'; //'364211';
// Google Analytics link params
$utm_source = 'san-angelo-live';
$utm_medium = 'BigBang';

// End Configuration

// Delete the images from the directory
array_map('unlink', glob($image_dir . "/*"));
// Include the library
include_once('simplehtmldom/simple_html_dom.php');
//Fetch Megacars webpage
echo 'Fetching the cars from Megacars.com' . "\n";
$html = file_get_html($url_to_fetch);
$cars = array();
$title = '';
$img = '';
//Create an array of info about 10 cars displayed
echo 'Parsing the cars from Megacars.com' . "\n";
$cars_json = file_get_contents($save_json_file);
$old = json_decode($cars_json);
$i = 0;
foreach($html->find('section.vehicleListWrapper article.itemscope') as $element) {
  $vin_obj = $element->find('.imageContainer');
  $vin = $vin_obj[0]->attr['data-vin'];
  $condition_obj = $element->find('header .vehicleName a span.condition');
  $condition = $condition_obj[0]->nodes[0]->_[4];
  $year_obj = $element->find('header .vehicleName a span.year');
  $year = $year_obj[0]->attr['value'];
  $make_obj = $element->find('header .vehicleName a span.make');
  $make = $make_obj[0]->attr['value'];
  $model_obj = $element->find('header .vehicleName a span.model');
  $model = $model_obj[0]->attr['value'];
  $trim_obj = $element->find('header .vehicleName a span.trim');
  $trim = $trim_obj[0]->attr['value'];
  $title = $condition . ' ' . $year . ' ' . $make . ' ' . $model . ' ' . $trim;
  $img = $element->find('.imageContainer figure a img[src]');
  $img_path = $img[0]->attr['data-original'];
  // Create a local copy of the image
  $filename = 'array' . $i . '.jpg';
  $image = file_get_contents($img_path);
  $im = imagecreatefromstring($image);
  $width = imagesx($im);
  $height = imagesy($im);
  $newwidth = '200';
  $newheight = '132';
  $thumb = imagecreatetruecolor($newwidth, $newheight);
  imagecopyresized($thumb, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
  imagejpeg($thumb, $image_dir . '/' . $filename); //save image as jpg
  imagedestroy($thumb);
  imagedestroy($im);
  // Check to see if a Clickmeter URL has already been created:
  $exists = false;
  $clickmeter = '';
  foreach($old as $v) {
    if ($v->vin == $vin) {
      // Check to see if there is a tracking id
      if (!empty($v->link_id)) {
        $tid = get_domain_details($api_key, 'http://apiv2.clickmeter.com:80/datapoints/' . $v->link_id);
        $clickmeter = $tid->trackingCode;
        $clickmeter_link_id = $tid->id;
        $exists = true;
      }
      else {
        $clickmeter = $v->url;
        $clickmeter_link_id = $v->link_id;
      }
    }
  }
  if (!$exists || empty($clickmeter)) {
    // Get the path to the VDP
    $path = $element->find('.imageContainer figure a');
    $url = 'http://www.megacars.com/' . $path[0]->attr['href'] . '?utm_source=' . $utm_source .'&utm_medium=' . $utm_medium;
    $date = date('Ymd');
    // Clean up the link name
    $link_title = $year . $model . $vin; // . $date;
    $link_title = seo_friendly_url($link_title);
    // Get available Domain ID
    $domain_array = get_domain($api_key, 'http://apiv2.clickmeter.com:80/domains?offset=0&limit=1&type=system');
    $body = [
      'type' => 0,
      'title' => $link_title,
      'groupId' => $clickmeter_group_id,
      'name' => $link_title,
      'typeTL' => [
        'domainId' => $domain_array['id'], // http://45.gs/
        'redirectType' => 301,
        'url' => $url,
      ],
    ];
    $output = api_request('http://apiv2.clickmeter.com/datapoints/', 'POST', $body, $api_key);
    if (!empty($output)) {
      if (isset($output->errors[0]->errorMessage)) {
        echo 'ClickMeter said (error on ' . $linktitle . ' => ' . $url . '): ' . $output->errors[0]->errorMessage . "\n";
        $clickmeter = $url;
      }
      else {
        // Decode the $output into a real url
        $request_tracking_url = 'http://apiv2.clickmeter.com:80' . $output->uri;
        $tracking_url_obj = get_domain_details($api_key, $request_tracking_url);
        $clickmeter = $tracking_url_obj->trackingCode;
        $clickmeter_link_id = $tracking_url_obj->id;
      }
    }
    else {
      // Fallback to non-Clickmeter url
      $clickmeter = $url;
    }
  }
  $cars[] = array (
    'title' => $title,
    'vin' => $vin,
    'url' => $clickmeter,
    'link_id' => $clickmeter_link_id,
    'image' => $image_dir . '/' . $filename,
  );
  $clickmeter_link_id = null;
  $i++;
  echo 'Fetched and saved ' . $i . ' of 10 cars' . "\n";
}
// Create a JSON array of cars
$out = json_encode($cars);
//Save the JSON array for banner ads to pickup
file_put_contents($save_json_file, $out);
echo 'Banner ad inventory updated'  . "\n";

function seo_friendly_url($string){
  $string = str_replace(array('[\', \']'), '', $string);
  $string = preg_replace('/\[.*\]/U', '', $string);
  $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
  $string = htmlentities($string, ENT_COMPAT, 'utf-8');
  $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
  $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
  return strtolower(trim($string, '-'));
}

/**
 * Accepts a POST request to create a tracking ID from Clickmeter.com
 * @param $request_url
 * @param $type
 * @param array ($data)
 * @param $api_key
 * @return bool|mixed
 */
function api_request($request_url, $type, $data, $api_key) {
  if (!isset($request_url)) {
    echo 'Error on Clickmeter.com API: No request_url provided' . "\n";
    return false;
  }
  if (!isset($type)) {
    $type = 'POST';
    echo 'Warning: No type of transaction, GET or POST given. Assuming POST' . "\n";
  }
  if (!isset($data) || count($data) < 1) {
    if ($type != 'GET') {
      echo 'No data provided, in array format, to define the Clickmeter.com link via the API' . "\n";
      return FALSE;
    }
  }
  if (!isset($api_key)) {
    echo 'No API Key provided for creating the tracking link via API' . "\n";
    return false;
  }
  if (count($data) > 0) {
    $data_json = json_encode($data);
  }
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HTTPHEADER,array('X-Clickmeter-Authkey:' . $api_key,'Content-Type: application/json'));
  curl_setopt($ch, CURLOPT_URL, $request_url);
  curl_setopt($ch, CURLOPT_POST, count($data));
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //Execute
  $result = curl_exec($ch);
  if ($result === false) {
    echo 'Error number: ' . curl_errno($ch) . "\n";
  }
  //Close CURL connection
  curl_close($ch);
  return json_decode($result);
}
function get_domain($api_key, $request_url) {
  if (empty($request_url)) {
    $request_url = 'http://apiv2.clickmeter.com:80/domains?offset=0&limit=1&type=system';
  }
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HTTPHEADER,array('X-Clickmeter-Authkey:' . $api_key,'Content-Type: application/json'));
  curl_setopt($ch, CURLOPT_URL, $request_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $result = curl_exec($ch);
  curl_close($ch);
  $domain_details = json_decode($result);
  // Get the actual url for the domain id:
  $id = $domain_details->entities[0]->id;
  $uri = $domain_details->entities[0]->uri;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HTTPHEADER,array('X-Clickmeter-Authkey:' . $api_key,'Content-Type: application/json'));
  curl_setopt($ch, CURLOPT_URL, 'http://apiv2.clickmeter.com:80' . $uri);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $result = curl_exec($ch);
  curl_close($ch);
  $domain_final = json_decode($result);
  $domain = array(
    'id' => $domain_final->id,
    'url' => $domain_final->name,
  );
  curl_close($ch);
  return $domain;
}
function get_domain_details($api_key, $request_tracking_url) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HTTPHEADER,array('X-Clickmeter-Authkey:' . $api_key,'Content-Type: application/json'));
  curl_setopt($ch, CURLOPT_URL, $request_tracking_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $result = curl_exec($ch);
  curl_close($ch);
  return json_decode($result);
}


