<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
</head>
<body>
<?php
header('Content-Type: text/html; charset=utf-8');
set_time_limit(300);
$files = glob('*.xml');

function strpos_all($haystack, $needle) {
    $offset = 0;
    $allpos = array();
    while (($pos = strpos($haystack, $needle, $offset)) !== FALSE) {
        $offset   = $pos + 1;
        $allpos[] = $pos;
    }
    return $allpos;
}

$reactions = array(
	'_3j7o' =>"Haha",
	'_3j7m' =>"Love",
	'_3j7l' =>"Like",
	'_3j7n' =>"Wow",
	'_3j7q' =>"Angry",
	'_3j7r' =>"Sad",
);

$post_info =array(
	'10215647130824678.xml' => array('12/09/2018, 08:51','fb_user/10215647130824678')
);

foreach($files as $file){
$xml_name = $file;
$myXMLData = file_get_contents($xml_name, true);
$myXMLData =  str_replace("role=\"img\" style=\"width:40px;height:40px\">","role=\"img\" style=\"width:40px;height:40px\"></img>",$myXMLData);
$xml=simplexml_load_string($myXMLData) or die("Error: Cannot create object");

$i=0;
$post_link = $post_info[$xml_name][1];
$post_date = $post_info[$xml_name][0];
$reaction_array = array();
foreach($xml->children() as $li) { 
	$friend = $xml->li[$i]->div[0]->a->div->img['aria-label']." ";
	$link = $xml->li[$i]->div[0]->div->div->div->div[1]->div->a['href']." ";
	$is_friend = $xml->li[$i]->div[0]->div->div->div[1]->div[1]->span->div->button[0]['class']; 
	$friend_stat = 0;
	if(strpos($is_friend, 'hidden_elem')){
		$friend_stat = 1;
	}
	$link_exploded = explode("?", $link);
	$link = $link_exploded[0];
	if($link== 'https://www.facebook.com/profile.php'){
		$link.= '?'. $link_exploded[1];
		$link_exploded = explode("&", $link);
		$link = $link_exploded[0];
	}
	$reaction = $xml->li[$i]->div[0]->a->div->div->span->i['class'];
	$reaction = substr($reaction, 0, 5);
	$reaction_array[$i]['friend'] = $friend;
	$reaction_array[$i]['link'] = $link;
	$reaction_array[$i]['reaction'] = $reaction_name =$reactions[$reaction];
	echo $str = "INSERT INTO reaction_details(reaction, link, name, post_link, post_date_time, is_friend) VALUES 
			('$reaction_name','$link','$friend','$post_link','$post_date', '$friend_stat'); ";
	echo '</br>';
	$i++;
}

$love = 0;
$like = 0;
$haha = 0;
$wow = 0;
$sad = 0;
$angry = 0;
$total = 0;
for($i=0;$i<count($reaction_array);$i++){
	switch($reaction_array[$i]['reaction']){
		case 'Love' : 
			$love++;
			break;
		case 'Like' : 
			$like++;
			break;
		case 'Haha' : 
			$haha++;
			break;
		case 'Wow' : 
			$wow++;
			break;
		case 'Sad' : 
			$sad++;
			break;
		case 'Angry' : 
			$angry++;
			break;
	}
}

$total = $love+$like+$haha+$wow+$sad+$angry;
echo 'Love  : ' . $love. '</br>';
echo 'like  : ' . $like. '</br>';
echo 'haha  : ' . $haha. '</br>';
echo 'wow   : ' . $wow. '</br>';
echo 'sad   : ' . $sad. '</br>';
echo 'angry : ' . $angry. '</br>';
echo 'Total : ' . $total. '</br>';

}

?>

</body>
</html>