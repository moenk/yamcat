<?php
$url=$_REQUEST['url'];
if ($url=="") die();
$bbox=$_REQUEST['bbox'];
if ($bbox=="") die();
// fix missing "?" and add params to be sure they are there
if (strpos($url,'?')==false) $url.="?";
$url.="&SERVICE=WMS&REQUEST=GetCapabilities";
$xml = file_get_contents($url);
$xml=str_replace('xlink:','xlink_',$xml);
$xml = simplexml_load_string($xml);
// print "<pre>"; print_r($xml); die();
include "conf/config.php";
$title=(string)$xml->ServiceException;
if ($title=="") $title=(string)$xml->Service->Title;
$subtitle="";
$attribution=(string)$xml->Service->AccessConstraints;
$layers=$xml->Capability->Layer->Layer;
$getmapurl=(string)$xml->Capability->Request->GetMap->DCPType->HTTP->Get->OnlineResource[xlink_href];
include "header.php";
include "navigation.php";
?>
<script src="external/OpenLayers/lib/OpenLayers.js"></script>
<script type="text/javascript">
    var lon = 0;
    var lat = 0;
    var zoom = 2;

	function init(){
		var map = new OpenLayers.Map({
			div: "map-id",
			controls: [],
			numZoomLevels : 20,
			fractionalZoom: true,
			projection: new OpenLayers.Projection("EPSG:4326") // every layer should be able to do this
		});
		map.addControl(new OpenLayers.Control.Navigation());
		map.addControl(new OpenLayers.Control.PanZoomBar());
		map.addControl(new OpenLayers.Control.Attribution());
        var baselayer = new OpenLayers.Layer.WMS( 
			"OpenLayers WMS",
            "http://vmap0.tiles.osgeo.org/wms/vmap0", 
			{layers: 'basic'},
			{attribution: "<?php print $attribution; ?>"} // one should fit for all 
		);
		var myLayerSwitcher = new OpenLayers.Control.LayerSwitcher(); 
		map.addControl(myLayerSwitcher);
		myLayerSwitcher.maximizeControl();
<?php
if ($getmapurl=="") $getmapurl=substr($url,0,strpos($url,'?'));
$layernummer=1;
$layerlist="[baselayer";
foreach ($layers as $layer) {
	$layername=(string)$layer->Name;
	$titel=(string)$layer->Title;
	$isbaselayer="false";
	print '        var layer'.$layernummer.' = new OpenLayers.Layer.WMS("'.$titel.'",
            "'.$getmapurl.'", 
            {layers: "'.$layername.'", transparent: "true"';
	// if running our mapserv, use png with 24 bit, otherwise whatever server likes to give us			
	if (strpos($url,"mapserv")!=false) print ', format: "image/png; mode=24bit"';
	// set baselayer according to variable check above
	print '},
			{isBaseLayer: '.$isbaselayer.'}
        );
';
	$layerlist.=",layer".$layernummer;
	$layernummer++;
}
$layerlist.="]";
?>

map.addLayers(<?php print $layerlist; ?>); 
			
//            map.setCenter(new OpenLayers.LonLat(lon, lat), zoom);

bounds = OpenLayers.Bounds.fromArray(<?php print $bbox; ?>);
map.zoomToExtent(bounds);
}
    </script>
  </head>
  <body onload="init()">

<div id="map-id" style="width:100%;height:600px;"></div>
 
<?php
include "footer.php";
?>




