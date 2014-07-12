<?php

/*
Class Name: Social Parser
Author: Andrea Morone
Author URI: http://andreamorone.github.io
Project URI: http://andreamorone.github.io/social-parser 
Description: This class can retrieve latest posts from facebook, twitter, youtube and google plus.
*/


class Socialparser {

	/** FACEBOOK PARSE **/
	public function facebook($id,$num){
		$loader = 'https://ajax.googleapis.com/ajax/services/feed/load?v=1.0&num='.$num.'&q=';
		$UriEncoded = $this->encodeURIComponent('https://www.facebook.com/feeds/page.php?id='.$id.'&format=rss20');
		$url = $loader.$UriEncoded;
		$response = $this->curl_call($url);
		$entries = $response['responseData']['feed']['entries'];
		$posts = array();
		foreach($entries as $post){						
			array_push($posts,$post);			
		}
		return $posts;
	}
	/** END FACEBOOK PARSE **/
	
	/** YOUTUBE PARSE **/		
	public function youtube($id,$num){
		$loader = 'https://ajax.googleapis.com/ajax/services/feed/load?v=1.0&num='.$num.'&q=';
		$UriEncoded= $this->encodeURIComponent('https://gdata.youtube.com/feeds/base/users/'.$id.'/uploads?alt=rss&v=2&orderby=published&client=ytapi-youtube-profile');
		$data = $loader.$UriEncoded;
		$response = $this->curl_call($data);
		return $response;
	}		
	/** END YOUTUBE PARSE **/			

	/** TWITTER PARSE **/		
	public function twitter($id,$num){

		require_once("twitteroauth.php");
		 
		$consumerkey = "";
		$consumersecret = "";
		$accesstoken = "";
		$accesstokensecret = "";
		 
		function getConnectionWithAccessToken($cons_key, $cons_secret, $oauth_token, $oauth_token_secret) {
		  $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_token_secret);
		  return $connection;
		}
		 
		$connection = getConnectionWithAccessToken($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
			
		$tweets = $connection->get('statuses/user_timeline', array('screen_name' => $id, 'exclude_replies' => 'true', 'include_rts' => 'true', 'count' => $num));
				
		if(!empty($tweets)) {
		    foreach($tweets as $tweet) {
		    	$tweetImage = $tweet->entities->media[0]->media_url;
		        $tweetText = $tweet->text;
		        $tweetText = preg_replace("#(http://|(www.))(([^s<]{4,68})[^s<]*)#", '<a href="http://$2$3" target="_blank">$1$2$4</a>', $tweetText);
		        $tweetText = preg_replace("/@(w+)/", '<a href="http://www.twitter.com/$1" target="_blank">@$1</a>', $tweetText);
		        $tweetText = preg_replace("/#(w+)/", '<a href="http://search.twitter.com/search?q=$1" target="_blank">#$1</a>', $tweetText);
		        echo $tweetText.'<br>';
		        echo $tweetImage.'<br>';
		    }
		}		
	}
	/** END TWITTER PARSE **/	

	/** GOOGLE PLUS PARSE **/				
	public function google($id,$num){
		$key = 'INSERT YOUR GOOGLE API KEY';
		$url = 'https://www.googleapis.com/plus/v1/people/'.$id.'/activities/public?key='.$key.'&maxResults='.$num;
		$response = $this->curl_call($url);		
		print_r($response);
	}
	/** END GOOGLE PLUS PARSE **/		

	/** MISC **/		
	private function curl_call($url){
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  			
		$output = curl_exec($ch);
		$response = json_decode($output, true);
		return $response;
        curl_close($ch);      				
	}
	
	private function encodeURIComponent($str) {
	    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
	    return strtr(rawurlencode($str), $revert);
	}
	/** END MISC **/		
}

?>