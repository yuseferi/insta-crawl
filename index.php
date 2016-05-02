 <html>
<body>
<form action="index.php" method="post">
    Instagram photo unique id ( as example:BEwFBF0SXDE ): <input type="text" name="name" value="<?=$_POST['name'];?>"><br>
<input name="submit" value="Crawl" type="submit">
</form>

</body>
</html> 
<?php

if($_POST['name']){
$img_uq = $_POST['name'];
$main_url ="https://www.instagram.com/p/$img_uq/?taken-by=sokhananemandegar&__a=1";
$InstagramInfo = file_get_contents($main_url);
$results =json_decode($InstagramInfo,true);
$count = $results['media']['comments']['count'];
//echo $count;
if($count){

//$servername = "localhost";
//$username = "tekno_insta";
//$password = "iTy2u9%5";
//$db = "tekno_insta";
//
//// Create connection
//$conn = new mysqli($servername, $username, $password,$db);
//// Check connection
//if ($conn->connect_error) {
//    die("Connection failed: " . $conn->connect_error);
//}
//$conn->set_charset("utf8");
//
$start_time = $results['media']['comments']["page_info"]['start_cursor'];
$end_time = $results['media']['comments']["page_info"]['end_cursor'];
$has_next =true;
$comments=array();
$counter=1;
//if($count>=40){
    $temp_comments = $results['media']['comments']["nodes"];
foreach($temp_comments as $item){
     $comments[]=array('text' => $item['text'],'user'=>$item['user']['username']);
//     $sql = "INSERT INTO url_checklist (username, comment) VALUES ( '" . $item['text'] . "','". $res_row["brand"]."',0)";
     $counter++;
    }

//}
while($counter<=$count){
$time = $start_time;
$str="ig_shortcode($img_uq){comments.before($time,20){count,nodes {id,created_at,text,user{id,profile_pic_url,username}},page_info}}";
$encoded= urlencode($str);
$encoded = str_replace(urlencode("{"),"{",$encoded);
$encoded = str_replace(urlencode("}"),"}",$encoded);
$encoded = str_replace(urlencode("("),"(",$encoded);
$encoded = str_replace(urlencode(")"),")",$encoded);
$url = "https://www.instagram.com/query/?q=".$encoded;
$InstagramInfo = file_get_contents($url);
$results =json_decode($InstagramInfo,true);
//var_dump($results["comments"]);
if($results["comments"]["page_info"]["has_next_page"]){
	$start_time = $results["comments"]["page_info"]["end_cursor"];
	$has_next=true;
}
else {
	$has_next=false;
}
$temp_comments = $results["comments"]["nodes"];
foreach($temp_comments as $item){
     $comments[]=array('text' => $item['text'],'user'=>$item['user']['username']);
//     $sql = "INSERT INTO url_checklist (username, comment) VALUES ( '" . $item['text'] . "','". $res_row["brand"]."',0)";
     $counter++;
    }
}




$filename = "comments-".$img_uq.".csv";
try {
    $csvfile = fopen($filename, "w");
    if (! $csvfile ) {
        throw new Exception("Could not open the file!");
    }
}
catch (Exception $e) {
    echo "Error (File: ".$e->getFile().", line ".
          $e->getLine()."): ".$e->getMessage();
}
fprintf($csvfile, "\xEF\xBB\xBF");
fputcsv($csvfile, ["id", "user", "comment"]);
foreach($comments as $i=>$item){
fputcsv($csvfile, [$i, $item['user'], $item['text']]);
}
fclose($csvfile);
echo ($counter-1)." comment imorted.";
echo '<a href="./'.$filename.'" target="_blank">Download file</a>';


}
}
?>