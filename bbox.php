<div id="map" style="width:460px;height:320px;"></div>		
<script src="external/OpenLayers/lib/OpenLayers.js"></script>
<script type="text/javascript">
            var lon = 5;
            var lat = 40;
            var zoom = 1;
            var map = new OpenLayers.Map({
        div: "map",
    });

  var mapnik = new OpenLayers.Layer.OSM("OpenStreetMap");
  
                var control = new OpenLayers.Control();
                OpenLayers.Util.extend(control, {
                    draw: function () {
                        // this Handler.Box will intercept the shift-mousedown
                        // before Control.MouseDefault gets to see it
                        this.box = new OpenLayers.Handler.Box( control,
                            {"done": this.notice},
                            {keyMask: OpenLayers.Handler.MOD_CTRL});
                        this.box.activate();
                    },

                    notice: function (bounds) {
                        var ll = map.getLonLatFromPixel(new OpenLayers.Pixel(bounds.left, bounds.bottom))
									.transform(  
										new OpenLayers.Projection("EPSG:900913"), // form Spherical Mercator Projection
										new OpenLayers.Projection("EPSG:4326") // transform to from WGS 1984
									); 
                        var ur = map.getLonLatFromPixel(new OpenLayers.Pixel(bounds.right, bounds.top))
									.transform(  
										new OpenLayers.Projection("EPSG:900913"), // form Spherical Mercator Projection
										new OpenLayers.Projection("EPSG:4326") // transform to from WGS 1984
									); 
						; 
						document.getElementById("westbc").value=ll.lon.toFixed(4);
						document.getElementById("southbc").value=ll.lat.toFixed(4);
						document.getElementById("eastbc").value=ur.lon.toFixed(4);
						document.getElementById("northbc").value=ur.lat.toFixed(4);
                    }
                });
                map.addLayer(mapnik);
                map.addControl(control);
                map.setCenter(new OpenLayers.LonLat(lon, lat), zoom);
        </script>
<i>Hold CTRL key and drag a rectangle with the mouse</i>
