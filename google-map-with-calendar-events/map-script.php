<?php
    $pageParams = json_decode( $page['parameters'], true );
    $communityEvents = getCategoryPages( $pdo, $pageParams['communityEventsId'], $site_id );
    $ads = getPage( $pdo, $pageParams['communityAdsPage'], $site_id );
    $mapData = array();
    //Map Data
    foreach ( $communityEvents as $result ){
        $pageData = json_decode( $result['pagedata'], true );
        $locationData = $pageData['location'];
        $timestamp = strtotime( $pageData['date'] );
        $date = date( "D. M j, Y", $timestamp );
        $displayImage = $pageData['image'];
        $mainPageData = json_decode( $page['pagedata'], true );
        if( $displayImage == '' ){
            $displayImage = $mainPageData['image'];
        }

        if( $locationData['latlong'] != '' ){
            $mapData[] = array(
                "position" => $locationData['latlong'],
                "image" => $displayImage,
                "title" => htmlentities( $result['pagetitle'], ENT_QUOTES ),
                "date" => $date,
                "timestamp" => $timestamp,
                "link" => $result['url'],
                "details" => shortenString( $pageData['details'], 200 ),
                "marker" => 'community-icon',
                "type" => 'Community',
                "eventid" => $result['sitepageid']
            );
        }
    }
    $atcEvents = getEvents( $pdo, $site_id, false );
    foreach( $atcEvents as $result ){
        $pageData = json_decode( $result['pagedata'], true );
        $locationData = $pageData['map-location'];
        $timestamp = strtotime( $result['pageeventdate'] );
        $date = date( "D. M j, Y", $timestamp );
        $image =  galleryPics( $result['sitepageid'], $pdo, $site_id );
        $displayImage = $image[0]['imagefile'];
        $mainPageData = json_decode( $page['pagedata'], true );
        if( $displayImage == '' ){
            $displayImage = $mainPageData['image'];
        }

        $details = shortenString( $pageData['summary-details'] != '' ? $pageData['summary-details'] : $result['pagemaincontent'] , 200 );

        if( $locationData['latlong'] != '' ){
            $mapData[] = array(
                "position" => $locationData['latlong'],
                "image" => $displayImage,
                "title" => htmlentities( $result['pagetitle'], ENT_QUOTES ),
                "date" => $date,
                "timestamp" => $timestamp,
                "link" => $result['url'],
                "details" => $details,
                "marker" => 'atc-icon',
                "type" => 'ATC',
                "eventid" => $result['sitepageid']
            );
        }
    }

    function date_compare( $a, $b ) {
        $t1 = $a['timestamp'];
        $t2 = $b['timestamp'];
        return $t1 - $t2;
    }
    usort( $mapData, 'date_compare' );
    $mapData = json_encode( $mapData );
?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAsvEZ4c-EPedV3RjNeEETfvdyOKpNFbSI&libraries=geometry"></script>
<script type="text/javascript" src="//cdn.dynamixse.com/js/gmapsjs.js"></script>
<script type="text/javascript">
$(function(){
    var map, locations, ads, styles, centerLocation, defaultLocation, center, windowHeight, infowindowHeight;
    var uniqueLocationList = [], filteredEventList = [], bounds = [];
    windowHeight = $( window ).height();
    locations = <?= $mapData ?>;
    ads = <?= $ads['loopinginputs']; ?>;
    defaultLocation = new google.maps.LatLng( 33.8204, -84.3607 );

    map = new GMaps({
          div: '#map',
          scrollwheel: false,
          draggable: true,
          disableDefaultUI: true,
          zoomControl: true
    });

    styles = [
      {
        "stylers": [
          { "visibility": "on" }
        ]
      },
      {
        "featureType": "water",
        "stylers": [
          { "lightness": -40 },
          { "hue": "#4fbfed" }
        ]
      },
      {
        "featureType": "poi",
        "stylers": [
          { "visibility": "off" }
        ]
      },
      {
        "featureType": "road.highway",
        "elementType": "geometry.fill",
        "stylers": [
          { "color": "#bebebe" },
          { "visibility": "simplified" }
        ]
      },
      {
        "featureType": "road.arterial",
        "elementType": "geometry.fill",
        "stylers": [
          { "color": "#d5d5d5" },
          { "visibilty" : "simplified" }
        ]
      }
    ];

    map.addStyle({
        styledMapName: "Styled Map",
        styles: styles,
        mapTypeId: "map_style"
    });

    map.setStyle( "map_style" );

    // Intialize Map
    buildMap();
    sidebarBottom();

    // Add small-screen class on load if necessary
    $( '.layout-community-calendar' )[ $( '.current-filters' ).is( ':visible' ) ? 'addClass' : 'removeClass' ]( 'small-screen' );

    //  Set max-height on calendar on load if necessary
    $( '.calendar-map' ).css( 'max-height', windowHeight - 20 );

    // Resize function to add/remove small-screen class to layout and set max-height on calendar
    $( window ).bind( 'resize orientationchange', function() {
        $( '.layout-community-calendar' )[ $( '.current-filters' ).is( ':visible' ) ? 'addClass' : 'removeClass' ]( 'small-screen' );
        windowHeight = $( window ).height();
        $( '.calendar-map' ).css( 'max-height', windowHeight - 20 );
        sidebarBottom();
    });

/// *************** ///
/*** Create Marker ***/
/// *************** ///

    function createMarker( place ) {
        var markerIcon, markerImage, position, marker, myLatLng, eventNumber, overlay;
        markerIcon = place['marker'];
        markerImage = {
          url: "/sites/<?= $site_path ?>/exceptions/stylesheets/images/map/" + markerIcon + ".png",
          scaledSize: new google.maps.Size( 62, 92 )
        };
        position = place['position'].split( ", " );
        marker = map.addMarker({
            icon: markerImage,
            lat: position[0],
            lng: position[1],
            click: function( e ) {
                 map.setCenter( position[0],position[1] );
                 google.maps.event.addListener( this.infoWindow, 'domready', function() {
                     openInfoWindow();
                 });
            },
            infoWindow: {
                content: createInfoWindow( place ),
                maxWidth: 280
            }
        });

        eventNumber = place['nearbyEvents'].length + 1;
        if( eventNumber > 1 ){
            if( eventNumber > 9 ) eventNumber = "9+";
            overlay = map.drawOverlay({
                lat: position[0],
                lng: position[1],
                content: '<div class="multi-event-marker">' + eventNumber + '</div>'
            });
        }

        myLatlng = new google.maps.LatLng( position[0], position[1] );
        bounds.push( myLatlng );
    }

    // Actions when infowindow opens
    function openInfoWindow(){
        $( '.gm-style-iw' ).parent().addClass( 'infowindow-container' );
        infowindowHeight = $( '.gm-style-iw' ).height();
        highlightEvent();
    }

/// ******************** ///
/*** Template Functions ***/
/// ******************** ///

    // Infowindow template
    function createInfoWindow( place ){
        var title, image, date, link, details, eventid, nearbyEvents, display;

        title = place['title'];
        image = place['image'];
        date = place['date'];
        markericon = place['marker'];
        link = place['link'];
        details = place['details'];
        eventid = place['eventid'];
        nearbyEvents = place['nearbyEvents'];

        template = '<div class="infowindow-wrapper" data-eventid="' + eventid +  '">';
            template += '<div class="infowindow-image">' + srcSetJs( image, title, 70, 70, false, false, "class='marker-image'" ) + '</div>';
            template += '<div class="infowindow-content">';
                template += '<h2 class="infowindow-title">' + title + '</h2>';
                template += '<div class="infowindow-date">' + date + '</div>';
                template += '<p class="infowindow-details">' + details + '</p>';
            template += '</div>';
            template += '<div class="infowindow-link-container">';
                template += '<a class="infowindow-link" href="' + link + '">More Info</a>';
            template += '</div>';
            if( nearbyEvents.length ){
                template += '<div class="additional-events">';
                template += '<p><strong>Other events near here</strong></p>';
                    template += '<ul>';
                        template += '<li style="display: none;"><a href="#" class="more-events" data-eventid="' + eventid + '">' + title + '</a></li>';
                    for( var i = 0; i < nearbyEvents.length; i++ ){
                        template += '<li><a href="#" class="more-events" data-eventid="' + nearbyEvents[i].eventid + '">' + nearbyEvents[i].title + '</a></li>';
                    }
                    template += '</ul>';
                template += '</div>';
            }
        template += '</div>';
        return template;
    }

    // Update infowindow template
    function updateTemplate( eid ){
        whiteout( '.gm-style-iw', '.infowindow-link' );
        var iwEventId, result, iwOffset;
        result = locations.filter(function( obj ) {
            return obj.eventid == eid;
        });

        $( '.infowindow-wrapper' ).attr( 'data-eventid', result[0].eventid );
        $( '.infowindow-title' ).html( result[0].title );
        $( '.infowindow-date' ).html( result[0].date );
        $( '.infowindow-image' ).html( srcSetJs( result[0].image, result[0].title, 70, 70, false, false, "class='marker-image'" ) );
        $( '.infowindow-details' ).html( result[0].details );
        $( '.infowindow-link' ).attr( 'href', result[0].link ? result[0].link : '#' );

        iwEventId = $( '.infowindow-wrapper' ).attr( 'data-eventid' );
        $( '.infowindow-wrapper' ).find( '.additional-events li' ).each( function(){
            var $this = $( this );
            $this.show();
            if( $this.find( 'a' ).attr( 'data-eventid' ) === iwEventId ) $this.hide();
        });

        iwOffset = 0;
        if( infowindowHeight < $( '.gm-style-iw' ).height() ) {
            iwOffset = infowindowHeight - $( '.gm-style-iw' ).height();
            infowindowHeight = $( '.gm-style-iw' );
            map.panBy( 0, iwOffset );
        }
    }

    // Sidebar template
    function createEvent ( place ){
        var title, image, date, eventTemplate;
        title = place['title'];
        image = place['image'];
        date = place['date'];
        eventid = place['eventid'];

        eventTemplate = "<div class='sidebar-item sb-event' data-eventid='" + eventid + "'>";
            eventTemplate += "<div class='sb-image-container'>";
                eventTemplate += srcSetJs( image, title, 70, 70, false, false, "class='event-image'" );
            eventTemplate += "</div>";
            eventTemplate += "<div class='sb-content-container'>";
                eventTemplate += "<h2 class='event-title'>" + title + "</h2>";
                eventTemplate += "<div class='event-date'>" + date + "</div>";
            eventTemplate += "</div>";
        eventTemplate += "</div>";
        return eventTemplate;
    }

/// ******************* ///
/*** Filter Functions  ***/
/// ******************* ///

    // Function to filter by date or event type
    function filterEventList(){
        filteredEventList = [];
        var now, day, week, month, months6, year, timevar, type;

        now = $.now()/1000;
        day = 60 * 60 * 24;
        // week changed from 7 days to 14 days
        week = now + ( day * 14 );
        month = now + ( day * 30 );
        months6 = now + ( day * 30 * 6 );
        year = now + ( day * 365 );

        if( $( '.date-filter.active' ).hasClass( 'filter-week' ) ) timevar = week;
        if( $( '.date-filter.active' ).hasClass( 'filter-month' ) ) timevar = month;
        if( $( '.date-filter.active' ).hasClass( 'filter-months6' ) ) timevar = months6;
        if( $( '.date-filter.active' ).hasClass( 'filter-year' ) ) timevar = year;

        for( var i = 0; i < locations.length; i++ ){
            var $this, timestamp, atcOnly;
            $this = locations[i];
            timestamp = $this.timestamp;
            atcOnly = $( '#atc-only' ).prop( "checked" ) ? $this.type === 'ATC' : true;
            if( timestamp > now && timestamp < timevar && atcOnly ) filteredEventList.push( $this );
        }
    }

    // Function to filter by unique location
    function uniqueLocation() {
        var eventlist = filteredEventList;
        uniqueLocationList = [];
        for( var i = 0; i < eventlist.length; i++ ){
            var place, location, origLoc, dontAddToList;
            place = eventlist[i];
            place.nearbyEvents = [];
            place.parentOf = [];
            location = place.position;
            origLoc = location.split( ', ' );
            dontAddToList = false;

            for( var n = 0; n < uniqueLocationList.length - 1; n++ ) {
                var nplace, newLoc, origLatLng, newLatLng, distanceFromHere;
                nplace = uniqueLocationList[n];
                newLoc = nplace.position.split( ', ');

                origLatLng = new google.maps.LatLng( parseFloat( origLoc[0] ), parseFloat( origLoc[1] ) );
                newLatLng = new google.maps.LatLng( parseFloat( newLoc[0] ), parseFloat( newLoc[1] ) );
                distanceFromHere = google.maps.geometry.spherical.computeDistanceBetween( origLatLng, newLatLng ) * 0.000621371;

                if( distanceFromHere <= 1.5 ){
                    dontAddToList = true;
                    nplace.parentOf.push( place.eventid );
                    nplace.nearbyEvents.push( place );
                    break;
                }
            }
            if( ! dontAddToList ) uniqueLocationList.push( place );
        }
    }

/// ************************* ///
/*** Filter Helper Functions ***/
/// ************************* ///

    // Close filter container when filter is selected on small screens
    function closeSlidingContainers( event ){
        if( $( event.currentTarget ).closest( '.small-screen' ).length ) {
            $( '.event-container-outer.expanded' ).removeClass( 'expanded' );
            $( '.all-filters-container' ).hide( '400' );
        }
    }

    // Clear location search
    function clearSearch(){
        buildMap();
        centerLocation = false;
        $( '.location-center' ).hide();
    }

    // Perform search by location
    function findLocation(){
        GMaps.geocode({
            address: $('.location-search').val(),
            callback: function( results, status ) {
                if ( status == 'OK' ) {
                    whiteout( '#map' );
                    var latlng = results[0].geometry.location;
                    map.setCenter( latlng.lat(), latlng.lng() );
                    map.setZoom( 13 );
                }
            }
        });
    }

/// ********************* ///
/*** Filter Interactions ***/
/// ********************* ///

    // Show filters with click
    $( '.change-filters' ).click( function( event ){
        event.preventDefault();
        $( '.all-filters-container' ).slideToggle();
    });

    // Center and zoom on location
    $( '.location-search' ).closest( 'form' ).submit( function( event ){
        event.preventDefault();
        findLocation();
        centerLocation = true;
        $( '.location-center' ).html( $( '.location-search' ).val() ).show();
        closeSlidingContainers( event );
    });

    // Clear location search on delete
    $( '.location-search' ).keyup( function( event ){
        if( ( event.keyCode || event.which ) === ( 8 || 46 ) && $( '.location-search' ).val() === '' && centerLocation ) clearSearch();
        $( '.clear-search' )[ $( '.location-search' ).val() != '' ? 'addClass' : 'removeClass' ]( 'visible' );
    });

    // Clear location search by click
    $( '.clear-search' ).click( function( event ){
        event.preventDefault();
        $( '.location-search' ).val( '' );
        $( '.clear-search' ).removeClass( 'visible' );
        if( centerLocation ) clearSearch();
    });

    // Filter by date
    $( '.date-filter' ).click( function( event ){
        var $this = $( event.currentTarget );
        event.preventDefault();
        $this.addClass( 'active' ).siblings().removeClass( 'active' );
        buildMap();
        closeSlidingContainers( event );
    });

    // Filter by type
    $( '.event-type-checkbox' ).change( function( event ){
        buildMap();
        closeSlidingContainers( event );
    });

/// ******************** ///
/*** Other click Events ***/
/// ******************** ///

    // Click on sidebar event to open infowindow
    $( document ).on( 'click', '.sb-event', function( event ) {
        var $this, markerlist, eventid;
        $this = $( event.currentTarget );
        markerlist = uniqueLocationList;
        eventid = $this.data( 'eventid' );
        for( var i = 0; i < markerlist.length; i++ ){
            map.markers[i].infoWindow.close();
            if( markerlist[i].eventid == eventid || markerlist[i].parentOf.filter( checkEventId ) == eventid ) {
                google.maps.event.addListener( map.markers[i].infoWindow, 'domready', function() {
                    openInfoWindow();
                });
                map.markers[i].infoWindow.open( map.map, map.markers[i] );
                map.setCenter( map.markers[i].position.lat(), map.markers[i].position.lng() );
            }
        }
        function checkEventId( eid ){
            return eid == eventid;
        }
        updateTemplate( eventid );
    });

    // Reload template by clicking link inside infowindow
    $( document ).on( 'click', '.more-events', function( event ){
        event.preventDefault();
        var $this = $( event.currentTarget );
        var eventid = $this.data( 'eventid' );
        updateTemplate( eventid );
        highlightEvent();
    });

    // Expand sidebar on small screens
    $( '.event-container-expand' ).click( function( event ){
        event.preventDefault();
        $( this ).closest( '.event-container-outer' ).toggleClass( 'expanded' );
    });

    // Close sidebar and filters on mobile when sidebar event is clicked
    $( document ).on( 'click', '.expanded .sb-event', function( event ){
        event.preventDefault();
        closeSlidingContainers( event );
    });

/// *********************** ///
/*** Misc Helper Functions ***/
/// *********************** ///

    // Create srcset image
    function srcSetJs( image, alt, cWidth, cHeight, rHeight, rWidth, etc ){
        if( cWidth ) {
            $image = "<img src='https://szcafj.cloudimage.io/s/crop/" + cWidth + "x" + cHeight + "/https://cdn.dynamixse.com/<?= $site_path; ?>/" + image + "' alt='" + alt + "'";
            $image += "srcset='https://szcafj.cloudimage.io/s/crop/" + cWidth + "x" + cHeight + "/https://cdn.dynamixse.com/<?= $site_path; ?>/" + image + " 1x, ";
            $image += "https://szcafj.cloudimage.io/s/crop/" + ( cWidth * 2 ) + "x" + ( cHeight * 2 ) + "/https://cdn.dynamixse.com/<?= $site_path; ?>/" + image + " 2x' ";
            $image += etc + " />";
        } else if( rWidth ) {
            $image = "<img src='https://szcafj.cloudimage.io/s/resize/" + cWidth + "x" + cHeight + "/https://cdn.dynamixse.com/<?= $site_path; ?>/" + image + "' alt='" + alt + "'";
            $image += "srcset='https://szcafj.cloudimage.io/s/resize/" + cWidth + "x" + cHeight + "/https://cdn.dynamixse.com/<?= $site_path; ?>/" + image + " 1x, ";
            $image += "https://szcafj.cloudimage.io/s/resize/" + ( cWidth * 2 ) + "x" + ( cHeight * 2 ) + "/https://cdn.dynamixse.com/<?= $site_path; ?>/" + image + " 2x' ";
            $image += etc + " />";
        }
        return $image;
    }

    // Whiteout while content changes;
    function whiteout( el, extra ){
        $( el ).addClass( 'whiteout' );
        $( extra ).addClass( 'whiteout-extra' );
        setTimeout( function(){
            $( el ).removeClass( 'whiteout' );
            $( extra ).removeClass( 'whiteout-extra' );
        }, 400 );
    }

    // Highlight and scroll to event in sidebar
    function highlightEvent(){
        if( $( '.infowindow-wrapper' ).length ){
            var iwEventId, targt, scrollHere;
            iwEventId = $( '.infowindow-wrapper' ).attr( 'data-eventid' );
            target = $( '.sb-event[data-eventid=' + iwEventId + ']' );
            target.addClass( 'highlight' ).siblings().removeClass( 'highlight' );
            scrollHere = target.offset().top - $( '.event-container' ).offset().top + $( '.event-container' ).scrollTop() - 50;
            $( '.event-container' ).animate( { scrollTop: scrollHere }, 600 );
        }
    }

/// ************************************ ///
/*** Functions to build map and sidebar ***/
/// ************************************ ///

    // Function to build map
    function buildMap(){
        var updatedEventList, eventlist, locationlist;
        updatedEventList = [];
        filterEventList();
        uniqueLocation();
        eventlist = filteredEventList;
        locationlist = uniqueLocationList;
        whiteout( '.calendar-map' );
        bounds = [];
        map.removeMarkers();
        for ( var i = 0; i < eventlist.length; i++){
            var place = eventlist[i];
            updatedEventList.push( createEvent( place ) );
        }
        for( var j = 0; j < locationlist.length; j++ ){
            var jplace = locationlist[j];
            createMarker( jplace );
        }

        if( ! eventlist.length ){
            bounds.push( defaultLocation );
            map.fitLatLngBounds( bounds );
            map.setZoom( 12 );
            $( '.calendar-map' ).addClass( 'no-events' );
        } else {
            map.fitLatLngBounds( bounds );
            if( map.getZoom() > 16 ) map.setZoom( 16 );
            $( '.calendar-map' ).removeClass( 'no-events' );
        }

        $( '.event-container' ).html( updatedEventList );
        mixAds();

        if( $( '.location-search' ).val() ){
            findLocation();
        }

        $( '.timeframe' ).html( $( '.date-filter.active' ).text() );
        $( '.event-type-checkbox' ).each( function(){
            if( this.checked ) $( '.event-type' ).html( $( 'label[for="' + this.id + '"]' ).text() );
        });

        $( '.multi-event-marker' ).parent().remove();
    }

    // Mix in ads
    function mixAds(){
        $( '.sb-event:nth-of-type( 3n + 1 )' ).each( function( idx ){
            var ad, adTemplate, adImage, adLink;
            ad = ads[idx];
            if( ! ad || ad.image === '' && ad.code === '' ) return false;
            adImage = srcSetJs( ad.image, ad.name, 260, 200, false, false, null );
            adLink = '<a href="' + ad.link + '">' + adImage + '</a>';
            adTemplate = '<div class="sidebar-item sb-ad">';
                adTemplate += ad.code ? ad.code : ( ad.link ? adLink : adImage );
            adTemplate += '</div>';
            $( this ).before( adTemplate );
        });
    }

    // Make space for sidebar bottom
    function sidebarBottom(){
        var sbBotPadding = $( '.event-sidebar-bottom' ).outerHeight();
        $( '.event-container' ).css( 'padding-bottom', sbBotPadding );
    }

/// ******************** ///
/*** Resizing functions ***/
/// ******************** ///

    google.maps.event.addDomListener(window, 'resize', resize);

    // Get center of map each time click is registered
    google.maps.event.addDomListener( window, 'click', function(){
        center = map.getCenter();
    });

    // Function to maintain center while resizing
    function resize() {
        if( ! center ) center = map.getCenter();
        if( ! $( '.infowindow-container' ).length ) map.setCenter( { lat: center.lat(), lng: center.lng() } );
    }
});

</script>
