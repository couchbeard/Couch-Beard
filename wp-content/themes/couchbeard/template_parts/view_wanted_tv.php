<div class="span12">
<?php

	try {
		$sb = new sickbeard();
		$shows = $sb->getShows();
	} catch (Exception $e) {
		_e('Sickbeard is not online.', 'couchbeard');
		echo '</div>';
		return;
	}

	
	if (empty($shows)) {
		_e('No TV shows wanted', 'couchbeard');
	} else {
	?>
	<?php foreach ($shows as $key => $val):
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
		<?php endforeach; ?>
<?php 
	$future = $sb->getFuture();
	$later = $future->later;
	$missed = $future->missed;
	$soon = $future->soon;
	$today = $future->today;

	if (!empty($today)) { ?>
		<legend><?php _e('Today', 'couchbeard'); ?></legend>
		<?php foreach ($today as $t) {
			echo $t->show_name . ' (' . date(__(get_option('date_format'), 'couchbeard'), strtotime($t->airdate)) . ')';
		}
	}

	if (!empty($soon)) { ?>
		<legend><?php _e('Soon', 'couchbeard'); ?></legend>
		<?php foreach ($soon as $s) {
			echo $s->show_name . ' (' . date(__(get_option('date_format'), 'couchbeard'), strtotime($s->airdate)) . ')';
		}
	}

	if (!empty($missed)) { ?>
		<legend><?php _e('Missed', 'couchbeard'); ?></legend>
		<?php foreach ($missed as $m) {
			echo $m->show_name . ' (' . date(__(get_option('date_format'), 'couchbeard'), strtotime($m->airdate)) . ')';
		}
	}				

	if (!empty($future)) { ?>
		<legend><?php _e('Future', 'couchbeard'); ?></legend>
		<?php foreach ($later as $l) {
			echo $l->show_name . ' (' . date(__(get_option('date_format'), 'couchbeard'), strtotime($l->airdate)) . ')';
		}
	}
?>
<?php } ?>
</div>
