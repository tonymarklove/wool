<?php
	$self->js("/components/colorpicker/js/colorpicker.js");
	$self->css("/components/colorpicker/css/colorpicker.css");
?>
<div class="pad">
	<ol class="breadcrumbs">
		<li><?php echo linkTo("Home", array("controller"=>"auto", "action"=>"index")) ?></li>
		<li>Theme</li>
	</ol>
</div>

<div class="pad">
	<form method="post" enctype="multipart/form-data">
		<div class="pod">
			<div class="head">
				<h2 class="icon iconEdit">Theme Configuration</h2>
			</div>
			
			<div class="body splitBody">
				<div class="s1of2">
					<div class="editPanel">
						<?php foreach ($def["params"] as $name=>$def) { ?>
						<div class="input">
							<label for="<?php echo $name ?>"><?php echo $def["name"] ?></label>
							<?php
								if ($def["type"] == "color") {
									echo color_field_tag($name, $params->valueBy("reference", $name, "value"));
								} else {
									echo file_field_tag($name, $params->valueBy("reference", $name, "value"));
									if ($params->valueBy("reference", $name, "value")) {
										echo '<br /><small>Current File: <a href="' . $params->valueBy("reference", $name, "value") . '" target="_blank">' . $params->valueBy("reference", $name, "value") . "</a></small>";
									}
								}
							?>
						</div>
						<?php } ?>
					</div>
				</div>
				
				<div class="s1of2">
					<div class="pad">
						<img src="<?php echo routeUri(array("action"=>"previewImage")) ?>" alt="" />
					</div>
				</div>
				
				<div class="clear"></div>
			</div>
			
			<div class="foot">
				<input type="submit" class="btnLink icon iconSave" value="Save" />
			</div>
		</div>
	</form>
</div>
