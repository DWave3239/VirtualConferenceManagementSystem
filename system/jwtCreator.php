<?php 
/*****************************************************
 *
 * this script has been taken partly from 
 * https://github.com/8x8/jaas_demo/blob/main/jaas-jwt-samples/php/jaas-jwt-firebase.php
 *
 *****************************************************/

require_once VENDOR_DIR.'/autoload.php';
use Firebase\JWT\JWT;

class JWTCreator {

    // variables that are identical for every token created
    private $apiKey;
    private $appId;
    private $privateKey;

    function __construct($keyFilePath){
        /**
         * Change the variables below. Found in jaas Backend
         */
        $this->apiKey = "vpaas-magic-cookie-253dbc50398b4685819dc7167cedbb79/981aa3";
        $this->appId = "vpaas-magic-cookie-253dbc50398b4685819dc7167cedbb79";

        // Read your private key from file see https://jaas.8x8.vc/#/apikeys
        $this->privateKey = file_get_contents($keyFilePath);
    }

    
    // Use the following function to generate your JaaS JWT.
    function createJaasToken($userData, $permissions, $delays, $room) {
        $payload = array(
            'iss' => 'chat',
            'aud' => 'jitsi',
            'exp' => $delays['expDelay'], // format of time
            'nbf' => $delays['nbfDelay'],
            'room'=> $room,
            'sub' => $this->appId,
            'context' => [
                'user' => [
                    'moderator' => $userData['userIsModerator'],
                    'email' => $userData['userEmail'],
                    'name' => $userData['userName'],
                    'avatar' => $userData['userAvatarUrl'],
                    'id' => $userData['userId']
                ],
                'features' => [
                    'recording' => $permissions['recordingEnabled'] ? "true" : "false",
                    'livestreaming' => $permissions['liveStreamingEnabled'] ? "true" : "false",
                    'transcription' => $permissions['transcriptionEnabled'] ? "true" : "false",
                    'outbound-call' => $permissions['outboundEnabled'] ? "true" : "false"
                ]
            ]
        );

        return JWT::encode($payload, $this->privateKey, "RS256", $this->apiKey);
    }
}
?>