<!DOCTYPE HTML>

<html>
	<head>
		<title>Wool Framework Error Trace</title>
		
		<link rel="stylesheet" href="<?php echo publicUri("/css/system.css") ?>" />
	</head>
	
	<body>
		<div id="pageCanvas">
			<div id="pageHeader">
				<h1>An Error Has Occured</h1>
			</div>
			
			<div id="pageBody">
				<?php echo $body_content ?>
			</div>
		</div>
		
		<div id="pageJavascripts">
			<script src="<?php echo publicUri("/js/system.js") ?>"></script>
		</div>
	</body>
</html>
