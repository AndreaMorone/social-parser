<?

include_once('socialparser.php');

$socialparser = new Socialparser();

$facebook_posts = $socialparser->facebook('535049976526453',15);

print_r($facebook_posts);

$youtube_videos = $socialparser->youtube('SnoopDogg',1);

//print_r($youtube_videos);
$tweets = $socialparser->twitter('andreamorone88',5);

//$google = $socialparser->google('+Ferrari',5);

?>