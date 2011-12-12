<?php
class SubscriberStats{

	public	$twitter,$rss,$facebook;
	public	$services = array();

	public function __construct($arr){

		$this->services = $arr;

		// Building an array with queries:

		if(trim($arr['feedBurnerURL'])) {
            $query = 'http://feedburner.google.com/api/awareness/1.0/GetFeedData?uri='.end(split('/',trim($arr['feedBurnerURL'],'/')));
            $xml = file_get_contents($query);
            $profile = new SimpleXmlElement($xml, LIBXML_NOCDATA);
            $this->rss = (string) $profile->feed->entry['circulation'];
            //echo '#1#'.$this->rss."*";
        }
		if(trim($arr['twitterName'])) {
			$query = 'http://api.twitter.com/1/users/show.json?screen_name='.$arr['twitterName'];
            $result = json_decode(file_get_contents($query));
            $this->twitter = $result->followers_count;
            //echo '#2#'.$this->twitter."*";
        }
		if(trim($arr['facebookFanPageID'])) {
			$query = 'http://graph.facebook.com/'.urlencode($arr['facebookFanPageID']);
			$result = json_decode(file_get_contents($query));
            $this->facebook = $result->shares;
            //echo '#3#'.$this->facebook."*";
        }

        //Grab Delicious
       /* $url = 'www.queness.com'; 
        $api_page = 'http://feeds.delicious.com/v2/json/urlinfo/data?url=%20www.queness.com';
        $json = file_get_contents ( $api_page );
        $json_output = json_decode($json, true);
        $data['delicious'] = $json_output[0]['total_posts'];*/
	}

	public function generate(){
		$total = number_format($this->rss+$this->twitter+$this->facebook);

		echo '
			<div class="subscriberStats">
				<div class="subscriberCount"
				title="'.$total.'+ Total Social Media Followers">'.$total.'</div>

				<div class="socialIcon"
				title="'.number_format($this->rss).' RSS Subscribers">
					<a href="'.$this->services['feedBurnerURL'].'">
					<img src="'.plugins_url('img/rss.png', __FILE__ ).'" alt="RSS" /></a>
				</div>

				<div class="socialIcon"
				title="'.number_format($this->facebook).' Fans on Facebook">
					<a href="'.$this->services['facebookFanPageURL'].'">
					<img src="'.plugins_url('img/facebook.png', __FILE__ ).'" alt="Facebook" /></a>
				</div>

				<div class="socialIcon"
				title="'.number_format($this->twitter).' Twitter Followers">
				<a href="http://twitter.com/'.$this->services['twitterName'].'">
					<img src="'.plugins_url('img/twitter.png', __FILE__ ).'" alt="Twitter" /></a>
				</div>
			</div>
		';
	}
}
?>