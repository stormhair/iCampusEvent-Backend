<?php
//require '../utility.php';

error_reporting(E_ERROR | E_PARSE);

function getCurrentId($aFilePath) {
	$str = file_get_contents($aFilePath);
	$arr = split(',', $str);
	$arr = array_flip($arr);
	return $arr;
}

//initialize current event id
$currentId = getCurrentId('event_id.txt');
$results = [];
$content = file_get_contents('currentEvents.txt');
$dom = new DOMDocument();
$dom->loadHTML($content);
$events = $dom->getELementById('feed_items');
foreach ($events->childNodes as $event) {
	$item = [];
    foreach ($event->childNodes as $a) {
    	# code...
    	$page_url = $a->getAttribute('href');
    	$item['page_url'] = $page_url;
    	$img = $a->getElementsByTagName('img');
    	$img_url = $img->item(0) ? $img->item(0)->getAttribute('src') : NULL;
    	$item['img_url'] = $img_url;
    	foreach ($a->childNodes as $child) {
    		switch($child->getAttribute('class')) {
    			case 'eventtitle':
    				$item['event_title'] = $child->nodeValue;
    				break;
    			case 'subtitle':
    				$item['event_subtitle'] = $child->nodeValue;
    				break;
    			case 'date':
    				$item['date'] = $child->nodeValue;
    				break;
    			case 'time':
    				$item['time'] = $child->nodeValue;
    				break;
    			case 'location':
    				$item['location'] = $child->nodeValue;
    				break;
    			default:
    				break;
    		}
    	}
    	$arr = split('/',$page_url);
    	$targetId = $arr[count($arr)-1];
    	if(is_numeric($targetId)) {
    		$id = intval($targetId);
    		if(!array_key_exists($id, $currentId)) {
	    		$item['event_id'] = $id;
	    		if(!array_key_exists('time', $item)) {
	    			$item['time'] = '10:00 am';
	    		}
	    		if(!array_key_exists('event_subtitle', $item)) {
	    			$item['event_subtitle'] = '';
	    		}
	    		$currentId[$id] = 1;
	    		$result[] = $item;
	    		var_dump($item);
    		}
    	}
    }
}
//$keys = array_keys($currentId);
//file_put_contents(implode(',', $keys), 'event_id.txt');
