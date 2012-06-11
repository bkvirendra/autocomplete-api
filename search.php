<?php
//http://www.recessframework.org/preview/content/files/recess-v0.20.tar.gz
header('Content-type: application/json');
if (isset($_REQUEST["q"]) && isset($_REQUEST["c"])) {

	if ($_REQUEST["q"] == "") {
		$str = "not found";
		$str = json_encode($str);
		echo "[".$str."]";
		return;
	}
	
	$term = trim(strtolower($_REQUEST['q']));
	$url = str_replace(array(" ", "(", ")"), array("_", "", ""), $term);
	require 'functions.php';
	
	switch ($_REQUEST["c"]) {
		case 'g':
			$google = new google();
			$data = $google->get($url);
			echo $data;
			break;
		case 'b':
			$bing = new bing();
			$data = $bing->get($url);
			echo $data;
			break;
		case 'y':
			$yahoo = new yahoo();
			$data = $yahoo->get($url);
			$str = substr($data, 0 , -9);
			echo $str. "]";
			break;
		case 'w':
			$wikipedia = new wikipedia();
			$data = $wikipedia->get($url);
			echo $data;
			break;
		case 'yt':
			$youtube = new youtube();
			$data = $youtube->get($url);
			$arr = json_decode($data, true);
			echo "[\"$url\",[";
			$c = count($arr[1]);
			$i = 1;
			foreach($arr[1] as $item) {
				echo "\"".$item[0]."\"";
					if ($i < $c) {
						echo ",";
					}
					$i++;
			}
			echo "]]";
			break;
		case 'gm':
			$gmaps = new gmaps();
			$data = $gmaps->get($url);
			$arr = json_decode($data);
			$i = 1;
			try{
			echo "[\"$url\",[";
				$c = count($arr->suggestion);
				if (empty($c)) {
					throw new Exception("[\"error occured\"]");
				}
			foreach($arr->suggestion as $item) {
				echo "\"".$item->query."\"";
				if ($i < $c) {
					echo ",";
				}
				$i++;
			}
			} catch (Exception $e) {
				die($e->getMessage());
			}
			echo "]]";
			break;
		case 'i':
			$imdb = new imdb();
			$data = $imdb->process($url);
			preg_match('/^imdb\$.*?\((.*?)\)$/ms', $data, $matches); //convert JSONP to JSON
			$json = $matches[1];
			$arr = json_decode($json);
			echo "[\"$url\",[";
			$c = count($arr->d);
			$i = 1;
			try {
				if (!$c) {
					throw new Exception("error occured");
				}
				foreach($arr->d as $item) {
					echo "\"".$item->l."\"";
					if ($i < $c) {
						echo ",";
						$i++;
					}
				}
			} catch (Exception $e) {
				die($e->getMessage());
			}
			echo "]]";
			break;
		default:
			print "c is undefined!";
	}

} else {
	$str = "not found";
	$str = json_encode($str);
	echo "[".$str."]";
}

?>