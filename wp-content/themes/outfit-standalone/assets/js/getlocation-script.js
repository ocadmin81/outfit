jQuery(document).ready(function(){

  var geocoder = new google.maps.Geocoder();

  function geocodePosition(pos) {
    geocoder.geocode({
      latLng: pos
    }, function(responses) {
      if (responses && responses.length > 0) {
        updateMarkerAddress(responses[0].formatted_address);
      } else {
        updateMarkerAddress('Cannot determine address at this location.');
      }
    });
  }

  function updateMarkerPosition(latLng) {
    jQuery('#latitude').val(latLng.lat());
    jQuery('#longitude').val(latLng.lng());
  }

  function updateMarkerAddress(str) {
    jQuery('#address').val(str);
  }


  function initialize() {

    geocoder = new google.maps.Geocoder();

  }

  google.maps.event.addDomListener(window, 'load', initialize);

  jQuery(document).ready(function() { 
           
    initialize();
            
    jQuery(function() {
      jQuery("#address").autocomplete({
        //This bit uses the geocoder to fetch address values
        source: function(request, response) {
          geocoder.geocode( {'address': request.term }, function(results, status) {
            response(jQuery.map(results, function(item) {
              return {
                label:  item.formatted_address,
                value: item.formatted_address,
                latitude: item.geometry.location.lat(),
                longitude: item.geometry.location.lng()
              }
            }));
          })
        },
        //This bit is executed upon selection of an address
        select: function(event, ui) {
          jQuery("#latitude").val(ui.item.latitude);
          jQuery("#longitude").val(ui.item.longitude);

          var location = new google.maps.LatLng(ui.item.latitude, ui.item.longitude);
        }
      });
    });
    

    
  });

});