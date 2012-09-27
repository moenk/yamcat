<script type="text/javascript">
<!--
var lon=parseFloat(document.getElementById("lon").value);
var lat=parseFloat(document.getElementById("lat").value);
var zoom=2
var markers=null;
var meinmarker;

OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {
  defaultHandlerOptions: {
    'single': true,
    'double': false,
    'pixelTolerance': 0,
    'stopSingle': false,
    'stopDouble': false
  },

  initialize: function(options) {
    this.handlerOptions = OpenLayers.Util.extend(
    {}, this.defaultHandlerOptions
  );

  OpenLayers.Control.prototype.initialize.apply(
    this, arguments
  ); 

  this.handler = new OpenLayers.Handler.Click(
    this, {
      'click': this.click_trigger
    }, this.handlerOptions
  );
}, 

  click_trigger: function(karte) {
    // erstmal gleich in GPS Koordinaten umwandeln
    var lonlat = map.getLonLatFromViewPortPx(karte.xy).transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));
    document.getElementById("lon").value = lonlat.lon;
    document.getElementById("lat").value = lonlat.lat;
    markers.removeMarker(meinmarker);
    // sicherheitshalber zurueck rechnen und dann erst den marker setzen
    meinmarker=new OpenLayers.Marker(lonlat  .transform(
      new OpenLayers.Projection("EPSG:4326"), 
      new OpenLayers.Projection("EPSG:900913") 
    ));
    markers.addMarker(meinmarker);
  }
});

var map = new OpenLayers.Map('map-id');
var zentrum;
var mapnik = new OpenLayers.Layer.OSM("OpenStreetMap");
map.addLayer(mapnik);
var scaleline = new OpenLayers.Control.ScaleLine();
map.addControl(scaleline);

function zeigsmir() {
  zentrum=new OpenLayers.LonLat(lon,lat) // Center of the map
    .transform(  
      new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
      new OpenLayers.Projection("EPSG:900913") // to Spherical Mercator Projection
    )
  map.setCenter(zentrum,zoom);
  meinmarker=new OpenLayers.Marker(zentrum);
  markers.addMarker(meinmarker);
}

if (markers == null) {
  markers = new OpenLayers.Layer.Markers("Position"); // global
  map.addLayer(markers);
}
zeigsmir();

var click = new OpenLayers.Control.Click();
map.addControl(click);
click.activate();

// -->
</script>


