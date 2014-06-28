
OmniturePlugin.prototype.init = function(){
 
var mapping = {
  clip_type   : 'video',//perma hook
  clip_title    : '',//hooked
  clip_series   : '',//hooked value of the prop/eVar is the video show. i.e. American Dad
  clip_season   : '',//hooked value of the prop/eVar is the video season as a number. i.e. 7
  clip_episode  : '',//hooked value of the prop/eVar is the video episode as a number. i.e. 1
  clip_rating   : '',//unhooked - not needed
  clip_id     : '',//hooked
  clip_duration : '',//hookedmapping.authenticated
  clip_time   : '',//hooked -current time
  guid      : '',//hooked
  format      : '',//hooked - short-form :: long-form
  playlist    : '',//UNHOOKED -value of prop/eVar is playlist name.  When no playlist is present, set to 'no playlist'
  ad_advertiser : '',//hooked
  ad_campaign   : '',//unhooked - where is this stored? 
  ad_title    : '',//hooked - value of prop/eVar is Ad Title. i.e. Fox_Gatorade_Q3_Online_LF_Video_20021587_mauer_revised
  ad_pod      : '',//hooked - value of prop/eVar is Ad type. i.e. preroll, midroll 1, midroll 2, postroll, etc.
  ad_position   : '',//hooked - value of prop/eVar is the Ad Position in the Ad pod, i.e. position 1, position 2, position 3
  ad_duration : '',
  network     : '',//hooked - value of prop/eVar is the internal network. i.e. fox or fx network
  host_domain   : '',//hooked - value of the prop/eVar is the embedded video host domain.  i.e. fox.com or mydomain.com
  player_id   : '',//hooked | verify
   subscription_type: '',//unhooked | value of the prop/eVar is the content subscription type. Values are entitled or public.
  authenticated : '',//unhooked 
  mvpd      : '',//unhooked - value from page loadw
  share_name    : '',//hooked - 
  error     : '',//unhooked - value of the prop/eVar is the error message in a friendly format.
  member_name   : '',//unhooked - member name via page load
  member_type   : '',//umhooked - member type via page load
  loadTime    : '',
  current_clip_id : '',
};

var sOmni;
/**
Definition:
{event:[EVENT TYPE],vars:[COMMA SEPARATED LIST OF VARIABLE NUMBERS], varType:[prop, evar or both], extraVars:[CSV OF DIRECT VAR MAPPING]},
**/
var events = {
  OnMediaStart_ad       : {event:'event6',vars:"3,15,28,29,30,31,32,33,34,36,41,44,46,47,48,49,50,56,57,58,60,61,62", varType:"evar", extraVars:"prop29,prop46,prop57,prop58"},
  OnMediaEnd_ad       : {event:'event7',vars:"3,15,28,29,30,31,32,33,34,36,41,44,46,47,48,49,50,56,57,58,60,61,62",varType:"evar",extraVars:""},
  //user authenticated
  OnMediaStart_clip_true    : {event:'event4,event70',vars:"3,15,28,30,32,33,34,36,41,44,47,48,49,50,60,61,62", varType:"both", extraVars:""},
  //user unauthenticted
  OnMediaStart_clip_false   : {event:'event4,event71',vars:"3,15,28,30,32,33,34,36,41,44,47,48,49,50,60,61,62", varType:"both", extraVars:""},
  OnMediaComplete_clip    : {event:'event5,event72',vars:"3,15,28,30,32,33,34,36,41,44,47,48,49,50,60,61,62",varType:"evar",extraVars:""},
  milestone25Reached      : {event:'event26,event72',vars:"3,15,28,30,32,33,34,36,41,44,47,48,49,50,60,61,62",varType:"evar",extraVars:""},
  milestone50Reached      : {event:'event27,event72',vars:"3,15,28,30,32,33,34,36,41,44,47,48,49,50,60,61,62",varType:"evar",extraVars:""},
  milestone75Reached      : {event:'event28,event72',vars:"3,15,28,30,32,33,34,36,41,44,47,48,49,50,60,61,62",varType:"evar",extraVars:""},
  OnSocialEvent_event29   : {event:'event29',vars:"3,15,16,28,30,32,33,34,36,41,44,47,48,49,50,61,62",varType:"evar",extraVars:""},
  OnMediaError        : {event:'event91',vars:"74",varType:"evar",extraVars:""},// Video: Error Served
  OnSocialEvent_event30   : {event:'event30',vars:"3,15,16,28,30,32,33,34,36,41,44,47,48,49,50,61,62",varType:"evar",extraVars:""},// Video: Get Embed Link
  OnShowFullScreen      : {event:'event33',vars:"3,15,28,30,32,33,34,36,41,44,47,48,49,50,60,61,62",varType:"evar",extraVars:""},// Video: View Full Screen
  OnGetSubtitleLanguage_on  : {event:'event38',vars:"3,15,28,30,32,33,34,36,41,44,47,48,49,50,60,61,62",varType:"evar",extraVars:""},// Video: Closed Captions On
  OnGetSubtitleLanguage_off : {event:'event39',vars:"3,15,28,30,32,33,34,36,41,44,47,48,49,50,60,61,62",varType:"evar",extraVars:""},// Video: Closed Captions Off
  OnSocialEvent_event44   : {event:'event44',vars:"3,15,16,28,30,32,33,34,36,41,44,47,48,49,50,61,62",varType:"evar",extraVars:""},// Social Media Share
  OnSocialEvent_event47   : {event:'event47',vars:"3,15,16,28,30,32,33,34,36,41,44,47,48,49,50,61,62",varType:"evar",extraVars:""},//  eMail Sent
  OnSocialEvent_event64   : {event:'event64',vars:"3,15,16,28,30,32,33,34,36,41,44,47,48,49,50,61,62",varType:"",extraVars:""},//  Tweet Sent
  authenticated       : {event:'event70',vars:"3,15,28,30,32,33,34,36,41,44,47,48,49,50,60,61,62",varType:"evar",extraVars:""},//  Video: Authenticated
  notAuthenticated      : {event:'event71',vars:"3,15,28,30,32,33,34,36,41,44,47,48,49,50,60,61,62",varType:"evar",extraVars:""},//  Video: Not Authenticated
  OnMediaPause        : {event:'event72',vars:"3,15,28,30,32,33,34,36,41,44,47,48,49,50,60,61,62",varType:"evar",extraVars:""}// Video: Not Authenticated
//  totalTimePlayed       : {event:'event72',vars:"",varType:"",extraVars:""},// Video: Total Time Played 
};

var milestones      = {"25":false, "50":false, "75":false};

/**
contain props and evars.  populated by updatePropVars after media load 
**/
var propsAndVars;

function initializeOmniPlugin(){
  var nuQ       = $pdk.jQuery;

  var omniturePlugin = function(){
  
    var _this     = {
      event4Sent    : false, //TODO:find a better way to prevent additional event4 beacons. 
      controller    : tpController,
      playlist    : '',
      pluginVars    : FDM_Player_vars.omniConfig,
      customAdMetadata: FDM_Player_vars.omniConfig.customAdMetadata,
      //updated with each media playback
      mediaObject   : {},
      mediaLength   : -1,
      loadTimeSent  : false,
      adLength    : -1,
      //flag indicating the media started playback
      didClipStart  : false,
      //indicate current playback is of ad type
      clipType    : "",
      isPlaying   : false,
      adPosition    : 0,
      prevAdPosition  : 0,
      isCCActive    : false,
      guid      : "",
      previousEvent : "",
      prevEvent   : {"type":"","timestamp":new Date().getTime()},//prevent duplicate events from being dispatched if they happen within 1sec of one another.  
      /**load omniture library**/
      loadSMedia    : function(callback){
        var loadBackupSCode  = false;
        if(typeof s_analytics == "undefined"){

            loadBackupSCode = true;
        
        }else if(typeof s_analytics.Media == "undefined"){
        
            loadBackupSCode = true;
        
        }
      
        if(loadBackupSCode){
            var sAttributes = {
              src: FDM_Player_vars.host+'/shared/1.4.527/js/s_code.js',
              language:'javascript'
            };

        // Is Omniture Loaded?
        var loadedscripts = document.getElementsByTagName("script"),
        omnitureLoaded  = false;
        
        for(i=0;i<loadedscripts.length;i++) {
          if(loadedscripts[i].src == sAttributes.src) {
            omnitureLoaded = true;
            break;
          }
        }
        if (!omnitureLoaded) {
          $pdk.jQuery.getScript(sAttributes.src, function () {
                    sOmni = sfdm;
                    if(_this.pluginVars.accountInfo != undefined){
                      sOmni.s_account     = _this.pluginVars.accountInfo.account;
                      sOmni.trackingServer  = _this.pluginVars.accountInfo.trackingServer;
                      
                      //ah, it all makes sense now.  this is how to dynamically change the reporting suite value. 
                      //aderpe, sorry for overlooking the obvious variables "fun" and "un".  
                      sOmni.fun = sOmni.un  = _this.pluginVars.accountInfo.account;
                    }
                    callback();
                });
              }
          }else{
              if (s_analytics.Media == "undefined")
                sOmni = fdm_s_analytics;
              else
                sOmni = s_analytics;
              
              if(_this.pluginVars.accountInfo != undefined){
                sOmni.s_account     = _this.pluginVars.accountInfo.account;
                sOmni.trackingServer  = _this.pluginVars.accountInfo.trackingServer;
                      
                sOmni.fun = sOmni.un  = _this.pluginVars.accountInfo.account;
              }
              callback();
            }
          },
      
      /**reset variables for new clip**/
      reset     : function(){
        //milestones
        nuQ.each(milestones, function(property, item){
          milestones[property]  = false;
        });
      },
      /**
      set context properties after each media start
      **/
      setProperties : function(event){      
        var type;
        var customData  = event.data.baseClip.contentCustomData;
      
        if(event.type == "OnMediaStart" || event.type == "OnMediaEnd" || event.type == "OnMediaLoadStart"){
          if(event.data.baseClip.isAd){
            // ad counter > event.data.chapter.index
            if(_this.customAdMetadata){
              if(customData == undefined)
                customData = _this.customAdMetadata;
              
            }else if(customData == undefined){
              return;
            }
            
            if(event.data.chapter != null){
              if(event.data.chapter.index > -1 && _this.prevAdPosition > -1 && event.type == "OnMediaStart"){ 
                _this.adPosition++;
                _this.prevAdPosition      = event.data.chapter.index;
              }
            }
            //hh:mm:ss
            mapping.ad_duration   = "00:"+_this.util.getClipTime(event.data.mediaLength);
            //populate ad data
            mapping.ad_advertiser = (customData)? customData["fw:category"] : "";
            mapping.ad_campaign   = (customData)? customData['episode'] : "";
            mapping.ad_title    = (event.data.title)? event.data.title :customData["fw:subcategory"];
            mapping.ad_pod      = (customData)? customData["fw:type"] : "";
            mapping.ad_position   = (customData)? _this.adPosition : customData["fw:time_position_class"]; 
            mapping.guid      =  (_this.guid)?_this.guid:"no wpr id";
            type          = "ad";

          }else if(event.data.chapter){//has chapters so it's clip?
          //populate clip data
            mapping.clip_title    = event.data.title;
            mapping.clip_series   = (_this.util.getSeries(event.data.baseClip))?_this.util.getSeries(event.data.baseClip) : "no show";

            if(_this.util.getConfig("trackCategory") == "true" || _this.util.getConfig("trackCategory") == true)
            mapping.clip_categories   = (_this.util.getCategories(event.data.baseClip))?_this.util.getCategories(event.data.baseClip) : "unable to read category format";

            mapping.clip_season   = (customData)? customData['season'] : "no season";
            mapping.clip_episode  = (customData)? customData['episode'] : "";
            mapping.clip_id     =  event.data.baseClip.id;
            mapping.clip_duration = _this.util.getClipTime;//7718976
            mapping.guid      =  (_this.guid)?_this.guid:"no wpr id";
            mapping.format      = _this.util.getFormat();
            mapping.source      = (_this.util.getConfig("source") != 'fail')?_this.util.getConfig("source") : _this.util.getConfig("network");
            mapping.network     = _this.util.getConfig("network");
            mapping.host_domain   = _this.util.getHost();
            mapping.player_id   = _this.util.getExtraInfo("prop34") || _this.util.getConfig("playerId");
            mapping.subscription_type= (_this.util.getConfig("entitled") == "entitled")?"entitled":"public";
            
            //short-forms can never have "entitled" subscription type
            if (_this.util.getFormat() == "short-form"){
              mapping.subscription_type = "public";
            } 
            
            //mapping.authenticated = _this.util.getExtraInfo("auth"); //add either event70 or event71 to OnMediaStart_clip event
            mapping.mvpd      = _this.util.getConfig("mvpd") || "no mvpd";
            mapping.playlist    = (_this.playlist.playlistID)?_this.playlist.playlistID:"unknown";
            type          = "clip"; 
          }
          
        }else if(event.type == "OnMediaComplete"){
          if(event.data.baseClip.isAd){
            type          = "ad";
          }else if(event.data.chapter){
            type          = "clip"; 
          }
        }
        return type;
      },
      
      /**
      build props, vars and event beacon
      **/
      updatePropVars  : function(eventMap){
        if(eventMap == undefined)
          return;
        var vars  = {
          "3"   :mapping.clip_series + '|' + mapping.clip_season + '|' + mapping.clip_title + '|' + mapping.format,
          "15"  :mapping.clip_type,
          "16"  :mapping.share_name,
          "25"  :mapping.member_name,
          "26"  :mapping.member_type,
          "28"  :mapping.playlist,
          "29"  :mapping.ad_advertiser,
          "30"  :mapping.format,
          "31"  :"rating",//not currently implemented
          "32"  :mapping.source,
          "33"  :mapping.network,
          "34"  :mapping.player_id,
          "36"  :mapping.clip_id,
          "41"  :mapping.clip_title +"|"+_this.util.getClipTime(_this.mediaLength),
          "44"  :mapping.guid,
          "46"  :mapping.ad_position,
          "47"  :mapping.clip_series,
          "48"  :mapping.clip_season,
          "49"  :mapping.clip_episode,
          "50"  :mapping.host_domain,
          "56"  :mapping.ad_advertiser,
          "57"  :mapping.ad_title +"|"+ mapping.ad_duration,
          "58"  :mapping.ad_pod,
          "60"  :mapping.mvpd,
          "61"  :mapping.subscription_type,
          "62"  :mapping.clip_categories,
          "74"  :mapping.error
        };

        var variables   = eventMap.vars.split(",");
        var csvAllVariables = "";
        var response    = {};
  
        //only grab variables of interest
        for(var i = 0; i < variables.length;i++){
        
          if(eventMap.varType     == "evar"){
        
            sOmni["eVar"+variables[i]] = vars[variables[i]];
            
            csvAllVariables     += "eVar"+variables[i]+",";
            
          }else if(eventMap.varType == "both"){
  
            sOmni["prop"+variables[i]] = sOmni["eVar"+variables[i]] = vars[variables[i]]; 
  
            csvAllVariables     += "eVar"+variables[i]+",";
            csvAllVariables     += "prop"+variables[i]+",";
          }else{
          
            sOmni["prop"+variables[i]] = vars[variables[i]];
          
            csvAllVariables     += "prop"+variables[i]+",";
          } 
        }
  
        //check for extra vars
        if(eventMap.extraVars.length > 0){
                
          var extraVars   = eventMap.extraVars.split(",");
          
          for ( i=0; i < extraVars.length; i++){
            //get var number
            var varNumber = extraVars[i].match(/\d+/g);
            
            //make sure prop/var doesn't already exist
            if(!response.hasOwnProperty(extraVars[i])){
              //apply
              sOmni[extraVars[i]] = vars[varNumber];
              csvAllVariables     += extraVars[i]+",";
            }    
          }
        }
        
        //finally we apply the event type
        if(sOmni.events != "")
          sOmni.events  = sOmni.events +","+eventMap.event;
        else
          sOmni.events  = eventMap.event;

        //finally we apply the event type
        sOmni.Media.trackVars=csvAllVariables+"events,products";
        sOmni.linkTrackEvents=sOmni.events;
        sOmni.linkTrackVars=csvAllVariables+"events,products";
        

        return response;
  
      },
      
      /**
      pdk event to omniture event translation
      **/
      translateEvent  : function(event){
        var type  = event.type;
        
        //_this.clearBeacon();

        if(event.data) 
          _this.mediaObject   = event.data;//update media object
        _this.clearBeacon();
        if(type == "OnReleaseStart"){
          _this.reset();

          if(!_this.event4sent){
              //humpty dumpty sat on a wall, humpty dumpty guards this call. 
              _this.mediaLength = 0;
          }
          
          _this.playlist = event.data;
          //debugger;
          //find content type short-form or long-form.  
          //short-form content doesn't trigger a second OnMediaStart - for actual content
          //long-form content depends on this event for it's clip data. 
          //so if this is short-form, we get all available metadata here. 
          /*
          if("short-form" == _this.util.getFormat()){
            Upload and test if issue still exists.  
          }
          */
          return;
        }else if(type == "podStart"){
          _this.adPosition= 0;
          return;
        }else if(type == "OnMediaPlaying"){
          _this.isPlaying   = true;
              
          //return if of ad type
          if(_this.clipType == "ad" || _this.clipType != "clip")
            return;
                
          var response  = _this.onMediaPlayback(event);

          if(response == "false")
            return;
              
              
          if(response != "false"){

            sOmni.products  = ";;;;event72="+_this.util.viewTimer.viewTime;
                
            //reset timer after each milestone
            _this.util.viewTimer.reset();
                
            //send beacon
            milestones[response]    = true;
            var omniEventMap = events["milestone"+response+"Reached"];
                
          }
              
        }else if(type == "OnMediaStart" || type == "OnMediaLoadStart"){

          if(event.data.baseClip.guid != undefined && event.data.baseClip.guid != null){
            _this.guid    = event.data.baseClip.guid;
            
          }
        

          if(event.data.chapter && !event.data.baseClip.isAd){//only store clip clip time. 
            _this.mediaLength   = event.data.mediaLength;
          }
              
          //BUG WATCH: 
          //Sending adStart or clipStart beacons depend on OnMediaStart being sent before each clip type.  This worked
          //long-form content but not for short-form content that fails to play a preroll.  In this case, the OnMediaStart
          //is triggered only for the preroll and not content.  
          //The current work around is to use OnMediaLoadStart(fires for both clip and ad) then store metadata.
          //set properties
          var clip_type = _this.setProperties(event);
              

          if(event.data.baseClip.isAd && type == "OnMediaStart"){
            _this.util.adTimer.reset();
            _this.util.adTimer.start();
          }

          if(type == "OnMediaLoadStart")//only use OnMediaLoadStart to get metadata
            return;
          
          if(typeof clip_type == "undefined")return;

              

          if(clip_type == "ad")
            _this.clipType  = "ad";
          else if(clip_type == "clip")
            _this.clipType  = "clip";
              

          //get map of event and variables
          var omniEventMap;
              
          if(clip_type == "ad"){      
            omniEventMap = events[type+"_"+clip_type];
          }else if (clip_type == "clip"){
            omniEventMap = events[type+"_"+clip_type+"_"+_this.util.getConfig("auth")];
          }
              
          //set load time vars
          //happens once per content stream
          if(!_this.loadTimeSent && clip_type == "clip"){
            if (!_this.loadTimeSent){
              //get current load time
              mapping.loadTime = FOXNEO_Player_getLoadTime();
            }

            if(sOmni.products)//if products exist, append event
              sOmni.products  = ",;;;;event56="+Math.round(mapping.loadTime)+","+sOmni.products;
            else//if no product exist create new products property
              sOmni.products  = ";;;;event56="+Math.round(mapping.loadTime);

            if(sOmni.events){//if exist, append
              sOmni.events = sOmni.events+",event56";
            }else{//if not create new prop
              sOmni.events = "event56";
            }


            _this.loadTimeSent  = true;
            //reset since it only gets used once.  
            //mapping.loadTimer= 0;
          }

          /*
          sOmni.Media.open("Movie Title",100, "Custom Player Name");
          sOmni.Media.play("Movie Title", 0);
          */
          //OnMediaStart doesn't fire for actual clip but only for ad,  
          //so we wont' get the true clip time for short-form.  long-form content flows through as expected. 
          if(_this.clipType == "clip"){
            if(!_this.didClipStart)
              _this.didClipStart    = true;
            
            _this.util.viewTimer.start(); 
            //if the clip id ever changes (end of a clip and new one starts playing, 
            //user clicks on a thumbnail while one is currently playing), then
            //event4sent is set to false and has the plugin send another event4 beacon for the new clip
            //can't rely on clip_id because it changes for each segment on long-form content. 
            var clip_signature      = mapping.clip_series + mapping.clip_season + mapping.clip_title;
            if (_this.current_clip_id != clip_signature){
              _this.current_clip_id = clip_signature;
              _this.event4sent    = false;
            } 
            
            
          }
                      
          }else if(type == "OnMediaEnd"){
            //OnMediaComplete will dispatch clip end beacon.  
            //pass if only of "ad" type
            if(_this.clipType != "ad"){
              _this.util.viewTimer.stop();
              return;
            }
            if(sOmni.events){//if exist, append
              sOmni.events = sOmni.events+",event57";
            }else{//if not create new prop
              sOmni.events = "event57";
          }

          if(sOmni.products)//if products exist, append event
            sOmni.products  = ",;;;;event57="+_this.util.adTimer.adTime+","+sOmni.products;
          else//if no product exist create new products property
            sOmni.products  = ";;;;event57="+_this.util.adTimer.adTime;

          _this.util.adTimer.stop();
          //append to event

          //set properties
          var clip_type = _this.setProperties(event);

          //get map of event and variables
          var omniEventMap= events[type+"_"+clip_type];

          //issue with pdk not firing OnMediaStart on short-form clips after ad completes. 
          //work around pending thePlatform response. 
          _this.clipType  = "clip";
          }else if(type == "OnShowFullScreen"){
            if(event.data == false)
              return;
            //get map of event and variables
            var omniEventMap= events[type];
              
            }else if(type == "OnSocialEvent"){
            
              //getLink, getEmbed, socialShare, emailSent, tweeted
              //get map of event and variables
              var omniEventMap= events[type+"_"+event.eventType];
              
            }else if(type == "OnOmnitureSocialTrackingEvent" || type == "OnOmnitureTracking"){ //the social event is known as 'OnOmnitureTrackingEvent' in html5
              var omniEventMap    = events["OnSocialEvent_"+event.data.events];
              
                var varsObject;
              if(event.data.vars)//html5 support
                varsObject    = event.data.vars; 
              else//meh as3 support. we should probably change this in the flash plugin
                varsObject    = event.data; 
                            
              nuQ.each(varsObject, function(property, value){

              if(property.indexOf("16") > -1)
                mapping.share_name  = varsObject[property]; 
                  
            });
                
            }else if(type == "OnMediaComplete"){
              
              //set properties
              var clip_type = _this.setProperties(event);
              
              if(clip_type != "clip")
                return;

              //stop timer
              _this.util.viewTimer.stop();
              sOmni.products  = ";;;;event72="+_this.util.viewTimer.viewTime;
              _this.util.viewTimer.reset();
              //get map of event and variables
              var omniEventMap= events[type+"_"+clip_type];
              
              _this.didClipStart    = false;
              
              _this.guid            = "";
              
//              _this.loadTimeSent    = false;
              _this.event4sent    = false;
              
            }else if(type == "OnGetSubtitleLanguage"){
              var state   = "off";
            
              if(event.data.langCode != "none")
                state       = "on";
              
              if(state == "on"){
                _this.isCCActive  = true;
              }else{
                _this.isCCActive  = false;
              }
                
              var omniEventMap    = events[type+"_"+state];
              
            }else if(type == "OnMediaPause"){
              if(!_this.isPlaying)
                return;
              //get map of event and variables
              var omniEventMap= events[type];
                sOmni.products  = ";;;;event72="+_this.util.viewTimer.viewTime;
                
              _this.isPlaying   = false;
              
            }   
                    
            //update vars and values
            var beacon  = _this.updatePropVars(omniEventMap);
            
            _this.sendBeacon(type);
          },
      
        /**
        called every second of media playback
        **/
        onMediaPlayback : function(e){
          var response    = "false";
          var percent = (e.data.isAggregate ? e.data.percentCompleteAggregate : e.data.percentComplete);    

          nuQ.each(milestones, function(prop,val){
            if(percent > Number(prop) && !val){
              //send milestone
              response  = prop;
              return false;
            }
            
          });
        
        return response;
      },
      
      /**
      set event listeners
      **/
      setListeners  : function(){
        _this.controller.addEventListener("podStart", _this.translateEvent);
        _this.controller.addEventListener("OnMediaStart", _this.translateEvent);
        _this.controller.addEventListener("OnMediaLoadStart",_this.translateEvent);
        _this.controller.addEventListener("OnMediaEnd", _this.translateEvent);
        _this.controller.addEventListener("OnMediaComplete", _this.translateEvent);
        _this.controller.addEventListener("OnMediaError", _this.translateEvent);
        _this.controller.addEventListener("OnMediaPlaying", _this.translateEvent);
        _this.controller.addEventListener("OnShowFullScreen", _this.translateEvent);
        _this.controller.addEventListener("OnReleaseStart",_this.translateEvent);
        _this.controller.addEventListener("OnGetSubtitleLanguage", _this.translateEvent);//cc value
        //TODO:  change OnOmnitureTracking to OnSocialEvent in plugins
        _this.controller.addEventListener("OnOmnitureSocialTrackingEvent", _this.translateEvent);
        _this.controller.addEventListener("OnOmnitureTracking", _this.translateEvent);
        _this.controller.addEventListener("OnMediaPause", function(e){
          if(e.data.userInitiated) {
            _this.util.viewTimer.stop();
            _this.translateEvent({type:"OnMediaPause"});
          }else if(navigator.userAgent.toLowerCase().indexOf("iphone") > -1){
            _this.util.viewTimer.stop();
            _this.translateEvent({type:"OnMediaPause"});          
          }
        });
        _this.controller.addEventListener("OnMediaUnpause", function(e){
          if(e.data.clip.baseClip.isAd)
            return;
          _this.util.viewTimer.reset();
          _this.util.viewTimer.start();
        });
      },
      /**
      dispatch beacons
      **/
      sendBeacon    : function(type){
                
        if(_this.event4sent && sOmni.events.indexOf("ent4") > 0 && sOmni.events != "event44")
          return;
          

        if(sOmni.products == "" && sOmni.events == "")
          return;
        if( !sOmni.trackLink ){
          sOmni.tl(sOmni.pageURL, "o", type);
        } else {
          sOmni.trackLink(sOmni.pageURL, "o", type);  
        }

        if(sOmni.events.indexOf("ent4") > -1){
          _this.event4sent  = true;
        }

        //this._s.tl(this._s.pageURL,"o",this.MEDIA_PERCENT+percent); 
        //sOmni.track();
        _this.clearBeacon();
        
        _this.timeStamper     = new Date().getTime();

      },
      
      /**
      clear beacon - typically called after being sent
      **/
      clearBeacon   : function(){
        for (var property in sOmni){
          if(property.toLowerCase().indexOf("prop") > -1 || 
            property.toLowerCase().indexOf("evar") > -1 ||
            property.toLowerCase().indexOf("eVar") > -1){
              sOmni[property] = "";
          }
        }

        sOmni.products      = "";
        
        sOmni.linkTrackEvents="";
        sOmni.linkTrackVars="";
        
        
        sOmni.Media.trackVars="";
        
        
        //finally we apply the event type
        sOmni["events"] = "";
        
        /*
          //clear props & vars
          nuQ.each(sOmni, function(property, val){
            if(property.toLowerCase().indexOf("prop") > -1 || 
            property.toLowerCase().indexOf("evar") > -1 ||
             property.toLowerCase().indexOf("eVar") > -1){
              sOmni[property] = "";
            }
          });
        */
        
      },
      
      /**useful utilities**/
      util      : {
        /**find series**/
        getSeries: function(clip) {
          var response;
          var parts = [];
          var categories  = clip.categories;

          if(categories) {
            for(var i=0; i < categories.length; i++) {
              if(categories[i].name.toLowerCase().indexOf("series/") == 0) 
              {
                parts = categories[i].name.split("/");
                response = parts[parts.length-1];
                break;
              }else if(categories[i].name.toLowerCase().indexOf("shows/") == 0){
                parts = categories[i].name.split("/");
                response = parts[parts.length-1];
                break;
              }
            }
          }   
          return response;  
        },
        getCategories: function(clip) {
          var response;
          var parts = [];
          var categories  = clip.categories;
          if(categories) {
            for(var i=0; i < categories.length; i++) {
              if(categories[i].name.toLowerCase().indexOf("categories/") == 0) 
              {
                parts = categories[i].name.split("/");
                response = parts[parts.length-1];
                break;
              }
            }

            if(!response){
              response = categories[0].name;
            }
          }   
          return response;  
        },
        getConfig : function(property){
          var response;
          
          if(!_this.pluginVars)
            return;
  
          //get property base on config object
          if(_this.pluginVars.hasOwnProperty(property)){
            response    = _this.pluginVars[property];
          }else
            response    = "fail";
            
          return response;
        },
        getExtraInfo  : function(property){
          var response;
          
          if(_this.pluginVars.extraInfo == null)
            return false;
  
          //get property base on config object
          if(_this.pluginVars.extraInfo().hasOwnProperty(property)){
            response    = _this.pluginVars.extraInfo()[property];
          }else
            response    = false;  
            
          return response;
        },
        /**get time in mm:ss format**/
        getClipTime :   function(len){
          var minutes = Math.floor(len / 60000);
          var seconds = Math.floor((len - minutes * 60000) / 1000);
          return  minutes + ":" + seconds;//this._clipTitle + '|' + minutes + ":" + seconds; //NOTE: pass length as mm:ss format
        },
        /**determine whether it's long or short form**/
        getFormat: function() {
                                  //don't change. it's called 
                                  //_this.playlist.chapters.chapters
          if(_this.playlist && _this.playlist.chapters && _this.playlist.chapters.chapters.length > 1)
            return "long-form";
            
          return "short-form";  
        },
        getHost : function(){
          
          var pageUrl = sOmni.pageURL;
          var response; 
          //if(!pageUrl)return;
          
          var host;
          if(pageUrl){
            if(pageUrl.indexOf("/", 9) == -1) {
              host = pageUrl;
            } 
            else {
              host = pageUrl.substr(0, pageUrl.indexOf("/", 9));
            }
          }
          //artf169663 change request
          response =  parent.embedded_host || FDM_Player_vars.embedded_host || host || window.location.href;
            
          return response;  
        },
        adTimer : {
          //track total ad time
            adTime      : 0,
            timer     : {},
          start : function(){
            _this.util.adTimer.timer      = setInterval(function(){
              _this.util.adTimer.adTime++
              },1000);
          },
          stop  : function(){
            clearInterval(_this.util.adTimer.timer);
          },
          reset : function (){
            _this.util.adTimer.adTime   = 0;
          }
        },
        viewTimer : {
          //track total view time
            viewTime    : 0,
            timer     : {},
          start : function(){
            _this.util.viewTimer.timer      = setInterval(function(){
              _this.util.viewTimer.viewTime++
              },1000);
          },
          stop  : function(){
            clearInterval(_this.util.viewTimer.timer);
          },
          reset : function (){
            _this.util.viewTimer.viewTime   = 0;
          }
        },
        isValidPlaylist: function() {
          // check for valid chapters 
          if(typeof _this.playlist.chapters == "undefined")
            return false;
      
          if(typeof _this.playlist.chapters.chapters == "undefined")
            return false;
      
          return true;
        }
      },
    };

    _this.loadSMedia(_this.setListeners);
  
  }
  
  omniturePlugin();
  
}
  initializeOmniPlugin();

}

function OmniturePlugin() {
  if(!this instanceof OmniturePlugin) {
    return new OmniturePlugin();
  }
}


var omniPlugin    = new OmniturePlugin();
  omniPlugin.init(); 