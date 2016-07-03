<?php

class CalendarController extends Zend_Controller_Action
{
    function init()
    {
        $this->_helper->_acl->allow('member', null); // members can access every action...
        $this->_helper->_acl->deny('member', 'blacklist'); // ...except blacklistAction
        $this->_helper->_acl->allow('admin', null); // admins can access every action
    }
    
    public function indexAction()
    {
        // Enter your Google account credentials
        $email = '';
        $passwd = '';
        try {
           $client = Zend_Gdata_ClientLogin::getHttpClient($email, $passwd, 'cl');
        } catch (Zend_Gdata_App_AuthException $ae) {
           echo 'Problem authenticating: ' . $ae->exception() . '';
        }

        $service = new Zend_Gdata_Calendar($client);

        try {
            $listFeed= $service->getCalendarListFeed();
        } catch (Zend_Gdata_App_Exception $e) {
            echo 'Error: ' . $e->getResponse();
        }

        echo '<h1>Calendar List Feed</h1>';
        echo '<ul>';
        foreach ($listFeed as $calendar) {
            echo '<li><a href="' . $calendar->id . '" title="' . $calendar->title . '">' . $calendar->title . '</a>';
            $query = $service->newEventQuery();
            $query->setUser('default');
            $query->setVisibility('private');
            $query->setProjection('full');
            $query->setOrderby('starttime');
            $query->setFutureevents(true);
            echo $query->getQueryUrl();
            $eventFeed = $service->getCalendarEventFeed($query);
            /*
            // option 2
            // $eventFeed = $gdataCal->getCalendarEventFeed($query->getQueryUrl());
            echo '<dl>';
            foreach ($eventFeed as $event) {
                echo '<dt>' . $event->title .  ' (' . $event->id . ')' . '</dt>';
                foreach ($event->when as $when) {
                    echo '<dd>Starts: ' . $when->startTime . '</dd>';
                }
            }
            echo '</dl>';
             */
            echo '</li>';
        }
        echo '</ul>';

        $this->_helper->viewRenderer->setNoRender();
    }

    public function youTubeAction()
    {
        $yt = new Zend_Gdata_YouTube();
        $playlistListFeed = $yt->getPlaylistListFeed('underwatercomau'); 

        echo '<dl>';
        foreach ($playlistListFeed as $playlistEntry) {
            echo '<dt><a href="' . $playlistEntry->getPlaylistVideoFeedUrl() . '">' . $playlistEntry->title->text . '</a></dt>';
            echo '<dd>' .  $playlistEntry->description->text . '</dd>';
            $query = $yt->newVideoQuery($playlistEntry->getPlaylistVideoFeedUrl());
            $videoFeed = $yt->getVideoFeed($query);
            echo '<dd><dl>';
            foreach ($videoFeed as $videoEntry) {
                $id = substr(strrchr($videoEntry->getFlashPlayerUrl(), '/'), 1);
                echo '<dt><img src="" /><a href="/calendar/video/id/' . $id . '/">' . $videoEntry->mediaGroup->title->text . '</a></dt>';
                echo '<dd>' .  $videoEntry->mediaGroup->description->text . '</dd>';
            }
            echo '</dl></dd>';
        }
        echo '</dl>';
        $this->_helper->viewRenderer->setNoRender();
    }

    public function videoAction() 
    {

        $videoId = $this->getRequest()->getParam('id');
        $yt = new Zend_Gdata_YouTube();
        $videoEntry = $yt->getVideoEntry($videoId);
        echo '<object width="425" height="355"><param name="movie" value="http://www.youtube.com/v/' . $videoId . '&rel=0&color1=0x006699&color2=0x54abd6"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/' . $videoId . '&rel=0&color1=0x006699&color2=0x54abd6" type="application/x-shockwave-flash" wmode="transparent" width="425" height="355"></embed></object>';
        $this->_helper->viewRenderer->setNoRender();
    }
}