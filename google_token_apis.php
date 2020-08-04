<?php

require_once 'src/Google_Client.php';
require_once 'src/contrib/Google_PlusService.php';
require_once 'src/contrib/Google_Oauth2Service.php';

class TickleGoogleApis    
{   

    public $client;

    protected $ClientId;

    protected $ClientSecret;

    function __construct($config) {

        $this->ClientId = (isset($config['client_id']))?$config['client_id']:'799405691032-er3cilvjgrqgtlfreuffllvkp2ouvrjb.apps.googleusercontent.com';
        $this->ClientSecret = (isset($config['client_secret']))?$config['client_secret']:'QYmRweaDw20scMLTidBR8MRB';
        $this->client = new Google_Client();
        $this->client->setClientId($this->ClientId); // paste the client id which you get from google API Console
        $this->client->setClientSecret($this->ClientSecret); // set the client secret
    }



    public function isTokenExpire($created_at,$debug_dates = false){

        $google_token_expire_at = ($created_at+3600);
        $time = time();

        if($debug_dates){
            echo "<br>".$time;
            echo "  Time now = ".date('Y-m-d h:i:s a',$time);
            echo "<br>".$google_token_expire_at;
            echo " Token expire time = ".date('Y-m-d h:i:s a',$google_token_expire_at);
            echo "<br>";
        }else{
            return ( ($time) >= ($google_token_expire_at));
               
        }

    }


    public function getNewToken($refresh_token)
    {   
        $this->client->refreshToken($refresh_token);
        $getGoogleToken = $this->client->getAccessToken();
        return json_decode($getGoogleToken, true);
    }

    public function updateUserToken($arr,$user_id,$type = "tickleuser")
    {
        if(is_array($arr)) {
            if($type == "tickleuser"){
                mysqli_query($db->conn,"update google_auth_tokens set access_token='" . $arr['access_token'] . "' , expires_in='" . $arr['expires_in'] . "' , created='" . $arr['created'] . "' where userid='" . $user_id . "' ");
            }else{
                
            }
        }
    }


}


?>