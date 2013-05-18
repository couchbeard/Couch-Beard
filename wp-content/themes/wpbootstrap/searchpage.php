<?php
/*
Template Name: Search Page
*/
?>
<?php $ajax_nonce = wp_create_nonce("keyy"); ?>
<?php get_header(); ?>
<?php
if (isset($_GET['id'])) {
	$url = "http://imdbapi.org/?id=".$_GET['id']."&episode=0&limit=1";

	$json = file_get_contents($url);

	$data = json_decode($json);
?>
	<legend><?php echo $data->title; ?></legend>
	<div class="row">
	<div class="row">
		<div class="span3">
			<div class="coverSearch">
    			<img src="<?php echo $data->poster; ?>" class="img-rounded"/>
    			<center><p class="lead"><?php echo $data->rating; ?></p></center>
    		</div>
		</div>
		<div class="span8">
			<div class="row">
				<div class="span6">
  					<p class="lead"><?php echo implode(', ', $data->genres); ?></p>
				</div>
				<div class="span1 pull-right">
					<p class="lead"><?php echo $data->country[0]; ?></p>
  				</div>
  				<div class="span1 pull-right">
					<p class="lead"><?php echo $data->year; ?></p>
  				</div>
			</div>
			<div class="row">
				<div class="span3">
					<p class="lead"><?php echo implode(', ', $data->language); ?></p>
				</div>
				<div class="span3">
					<p class="lead"><?php echo date('g:i', strtotime('today ' . (string) $data->runtime[0])); ?></p>
				</div>
			</div>
		</div>
	</div>
		<div class="span1 pull-right">
			<?php if($data->type == 'M') { ?>
				<button class="btn btn-inverse" id="addMovie"><?php _e('Couchpotato', 'wpbootstrap'); ?></button>
			<?php } else { ?>
				<button class="btn btn-inverse" id="addTV"><?php _e('Sickbeard', 'wpbootstrap'); ?></button>
			<?php } ?>
		</div>
		<div class="span1 pull-right">
			<a href="<?php echo $data->imdb_url; ?>">IMDB</a>
		</div>
	</div>
	
	


<?php
}
function imdb_to_tvdb($imdb)
{
	$xml = simplexml_load_string(file_get_contents("http://thetvdb.com/api/GetSeriesByRemoteID.php?imdbid=".$imdb));
	return (string) $xml->Series->children()->seriesid;
}

?>

<script>
$(function() {     
    $("#addMovie").on("click", function() {
        jQuery.ajax({  
            type: 'POST',
            cache: false,  
            url: "<?php echo home_url() . '/wp-admin/admin-ajax.php'; ?>",  
            data: {  
                action: 'addMovie',
                security: '<?php echo $ajax_nonce; ?>',
                id: '<?php echo $data->imdb_id; ?>'
            },
            success: function(data, textStatus, XMLHttpRequest) {
            	console.log(data);
            	alert('added'); 
            },  
            error: function(MLHttpRequest, textStatus, errorThrown) {
                alert("<?php _e('There was an error adding the movie. The movie was not added.', 'wpbootstrap'); ?>");  
            }  
        });         
    });
});
</script>

<?php get_footer(); ?>