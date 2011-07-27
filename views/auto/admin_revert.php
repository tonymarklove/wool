<?php
	$self->js("/js/jquery.positionMatch.js");
?>
<div class="pad">
	<ol class="breadcrumbs">
		<li><?php echo linkTo("Home", array("action"=>"index")) ?></li>
		<li><?php echo linkTo(Schema::displayName($table), array("action"=>"table", "table"=>$table)) ?></li>
		<li>Revert <?php echo Schema::displayName($table) ?> #<?php $u = Schema::uniqueColumn($table); echo $item->$u ?></li>
	</ol>
</div>

<div class="pad">
	<?php $self->renderPartial('table_edit') ?>
</div>

<?php $self->renderPartial('selection_grid') ?>

<script>
	var validators = <?php echo live_validators() ?>;
</script>

