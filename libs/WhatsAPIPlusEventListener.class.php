<?php

class WhatsAPIPlusEventListener implements WhatsAppEventListener {
    private $gmClient;
    
    public function __construct() {
        $this->gmClient= new GearmanClient();
        $this->gmClient->addServer();   
    }
    
    private function handleEvent($eventName, array $arguments, $sendTask = false) {
        if($sendTask) {
            $this->gmClient->doNormal(WASPY_GMAN . '_' . $eventName, serialize($arguments));
        }
        if(DEBUG_EVENTS) {
            doOutput('Event fired: ' . $eventName);
        }
    }

    public function onClose( 
        $phone, 
        $error  
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onCodeRegister(
        $phone,  
        $login,  
        $pw,     
        $type,   
        $expiration,  
        $kind,   
        $price,  
        $cost,   
        $currency,  
        $price_expiration  
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());      
    }
    
    public function onCodeRegisterFailed(
        $phone,  
        $status,  
        $reason,  
        $retry_after 
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }
    
    public function onCodeRequest(
        $phone, 
        $method,
        $length
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }
    
    public function onCodeRequestFailed(
        $phone, 
        $method, 
        $reason, 
        $value
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }
    
   public function onCodeRequestFailedTooRecent(
        $phone, 
        $method, 
        $reason, 
        $retry_after 
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }
    
   public function onConnect(
        $phone, 
        $socket 
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onCredentialsBad(
        $phone, 
        $status, 
        $reason 
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onCredentialsGood(
        $phone, 
        $login, 
        $pw, 
        $type, 
        $expiration, 
        $kind, 
        $price, 
        $cost, 
        $currency, 
        $price_expiration 
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onDisconnect(
        $phone, 
        $socket 
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onDissectPhone(
        $phone, 
        $country, 
        $cc, 
        $mcc, 
        $lc, 
        $lg 
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onDissectPhoneFailed(
        $phone 
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGetAudio(
        $phone, 
        $from, 
        $msgid, 
        $type, 
        $time, 
        $name, 
        $size, 
        $url, 
        $file, 
        $mimetype,
        $filehash,
        $duration,
        $acodec 
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGetError(
        $phone,
        $id,
        $error 
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGetGroups(
        $phone,
        $groupList
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGetGroupsInfo(
        $phone, 
        $groupList
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGetGroupsSubject(
        $phone, 
        $gId, 
        $time,
        $author,
        $participant,
        $name,
        $subject
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGetImage(
        $phone,
        $from,
        $msgid,
        $type,
        $time,
        $name,
        $size,
        $url,
        $file,
        $mimetype,
        $filehash,
        $width,
        $height,
        $thumbnail
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGetLocation(
        $phone,
        $from,
        $msgid,
        $type,
        $time,
        $name,
        $place_name,
        $longitude,
        $latitude,
        $url,
        $thumbnail
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGetMessage(
        $phone,
        $from,
        $msgid,
        $type,
        $time,
        $name,
        $message
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args(), true);
    }

    public function onGetGroupMessage(
        $phone,
        $from,
        $author,
        $msgid,
        $type,
        $time,
        $name,
        $message
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGetPrivacyBlockedList(
        $phone,
        $children
            /*
        $data,
        $onGetProfilePicture, 
        $phone,
        $from,
        $type,
        $thumbnail
            */
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGetProfilePicture(
        $phone,
        $from,
        $type,
        $thumbnail
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }
    
    public function onGetRequestLastSeen(
        $phone,
        $from,
        $msgid,
        $sec
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args(), true);
    }

    public function onGetServerProperties(
        $phone,
        $version,
        $properties
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGetvCard(
        $phone,
        $from,
        $msgid,
        $type,
        $time,
        $name,
        $contact,
        $vcard
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGetVideo(
        $phone,
        $from,
        $msgid,
        $type,
        $time,
        $name,
        $url,
        $file,
        $size,
        $mimetype,
        $filehash,
        $duration,
        $vcodec,
        $acodec,
        $thumbnail
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGroupsChatCreate(
        $phone,
        $gId
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGroupsChatEnd(
        $phone,
        $gId
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGroupsParticipantsAdd(
        $phone,
        $groupId,
        $participant
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGroupsParticipantsRemove(
        $phone,
        $groupId,
        $participant,
        $author
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onLogin(
        $phone
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onMessageComposing(
        $phone,
        $from,
        $msgid,
        $type,
        $time
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onMessagePaused(
        $phone,
        $from,
        $msgid,
        $type,
        $time
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onMessageReceivedClient(
        $phone,
        $from,
        $msgid,
        $type,
        $time
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onMessageReceivedServer(
        $phone,
        $from,
        $msgid,
        $type
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onPing(
        $phone,
        $msgid
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onPresence(
        $phone,
        $from,
        $type
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args(), true);
    }

    public function onSendMessageReceived(
        $phone,
        $id,
        $from,
        $type
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onSendPong(
        $phone,
        $msgid
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onSendPresence(
        $phone,
        $type,
        $name
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onSendStatusUpdate(
        $phone,
        $msg
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }
    
    public function onUploadFile(
        $phone,
        $name,
        $url
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onUploadFileFailed(
        $phone,
        $name
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onConnectError(
        $phone, 
        $socket
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args()); 
    }

    public function onGetGroupParticipants(
        $phone, 
        $groupId, 
        $groupList
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args()); 
    }

    public function onGetStatus(
        $phone, 
        $from, 
        $type, 
        $id, 
        $t, 
        $status
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onLoginFailed(
        $phone, 
        $tag
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onMediaMessageSent(
        $phone, 
        $to, 
        $id, 
        $filetype, 
        $url, 
        $filename, 
        $filesize,
        $filehash,
        $icon
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onMediaUploadFailed(
        $phone, 
        $id, 
        $node, 
        $messageNode, 
        $reason
    ) {      
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onProfilePictureChanged(
        $phone, 
        $from, 
        $id, 
        $t
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onProfilePictureDeleted(
        $phone, 
        $from, 
        $id, 
        $t
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onSendMessage(
        $phone, 
        $targets, 
        $id, 
        $node
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGetSyncResult(
        $result
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args());
    }

    public function onGetReceipt(
        $from,
        $id,
        $offline,
        $retry
    ) {
        $this->handleEvent(__FUNCTION__, func_get_args(), true);
    }

}
?>