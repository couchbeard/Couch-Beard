<div class="span12">
<?php
	$shows = sb_getShows();
	if (empty($shows)) {
		_e('No TV shows wanted', 'wpbootstrap');
	} else {
	?>
	<?php foreach ($shows as $key => $val) { 
		echo $val->show_name . ' (' . $key . ')';
		echo '<br />';
	?>
			
		<!--<div class="row">
			<div class="span3">
				<img src="<?php echo $movie->images->poster_original[0]; ?>" />
			</div>
			<div id="movieInfo" class="span8">
				<div class="row">
					<div class="span7">
						<p class="lead"><?php echo $movie->original_title; ?></p>
					</div>
					<div class="span1 pull-right">
						<p class="lead"><?php echo $movie->year; ?></p>
					</div>
				</div>

			</div>
		</div>
		<br /> -->
		<?php } ?>
<?php 
	$future = sb_getFuture();
	$later = $future->later;
	$missed = $future->missed;
	$soon = $future->soon;
	$today = $future->today;

	if (!empty($today)) { ?>
		<legend><?php _e('Today', 'wpbootstrap'); ?></legend>
		<?php foreach ($today as $t) {
			echo $t->show_name . ' (' . date(__(get_option('date_format'), 'wpbootstrap'), strtotime($t->airdate)) . ')';
		}
	}

	if (!empty($soon)) { ?>
		<legend><?php _e('Soon', 'wpbootstrap'); ?></legend>
		<?php foreach ($soon as $s) {
			echo $s->show_name . ' (' . date(__(get_option('date_format'), 'wpbootstrap'), strtotime($s->airdate)) . ')';
		}
	}

	if (!empty($missed)) { ?>
		<legend><?php _e('Missed', 'wpbootstrap'); ?></legend>
		<?php foreach ($missed as $m) {
			echo $m->show_name . ' (' . date(__(get_option('date_format'), 'wpbootstrap'), strtotime($m->airdate)) . ')';
		}
	}				

	if (!empty($future)) { ?>
		<legend><?php _e('Future', 'wpbootstrap'); ?></legend>
		<?php foreach ($later as $l) {
			echo $l->show_name . ' (' . date(__(get_option('date_format'), 'wpbootstrap'), strtotime($l->airdate)) . ')';
		}
	}
?>
<?php } ?>
</div>
