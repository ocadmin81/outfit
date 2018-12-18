jQuery(document).ready(function(){

  var geocoder = new google.maps.Geocoder();

  function initialize() {

    geocoder = new google.maps.Geocoder();

  }

  function addressComponents2Hash(addressComponents) {

    var res = {
      aal1: '',
      aal2: '',
      aal3: '',
      locality: ''
    };

    jQuery.each(addressComponents, function (key, value) {
      if (value.types[0] == 'locality') {
        res.locality = value.short_name;
      }
      else if (value.types[0] == 'administrative_area_level_3') {
        res.aal3 = value.short_name;
      }
      else if (value.types[0] == 'administrative_area_level_2') {
        res.aal2 = value.short_name;
      }
      else if (value.types[0] == 'administrative_area_level_1') {
        res.aal1 = value.short_name;
      }
    });
    return res;
  }

  google.maps.event.addDomListener(window, 'load', initialize);

  jQuery(document).ready(function() { 
           
    initialize();
            
    jQuery(function() {
      jQuery("input.address").autocomplete({
        //This bit uses the geocoder to fetch address values
        source: function(request, response) {
          geocoder.geocode( {'address': request.term }, function(results, status) {
            response(jQuery.map(results, function(item) {
              return {
                label:  item.formatted_address,
                value: item.formatted_address,
                latitude: item.geometry.location.lat(),
                longitude: item.geometry.location.lng(),
                hash: addressComponents2Hash(item.address_components)
              }
            }));
          })
        },
        //This bit is executed upon selection of an address
        select: function(event, ui) {
          var parent = jQuery(this).parent();
          var elem = jQuery(this);
          parent.find(".latitude").val(ui.item.latitude);
          parent.find(".longitude").val(ui.item.longitude);
          parent.find(".locality").val(ui.item.hash.locality);
          parent.find(".aal3").val(ui.item.hash.aal3);
          parent.find(".aal2").val(ui.item.hash.aal2);
          parent.find(".aal1").val(ui.item.hash.aal1);
          elem.val(ui.item.value);
          var obj = {
            address: ui.item.value,
            latitude: ui.item.latitude,
            longitude: ui.item.longitude,
            locality: ui.item.hash.locality,
            aal3: ui.item.hash.aal3,
            aal2: ui.item.hash.aal2,
            aal1: ui.item.hash.aal1
          };
          jQuery(this).trigger('addresschange', [obj]);
          //jQuery(this).trigger('blur', [ui.item.formatted_address]);
        },
        change: function(event, ui) {
          var elem = jQuery(this);
          var parent = jQuery(this).parent();
          if (elem.val() == '') {
            parent.find(".latitude").val('');
            parent.find(".longitude").val('');
            parent.find(".locality").val('');
            parent.find(".aal3").val('');
            parent.find(".aal2").val('');
            parent.find(".aal1").val('');
          }
        },
        minLength: 3
      });
    });
    

    
  });

});