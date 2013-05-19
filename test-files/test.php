<html>
<head>
<link href='http://twitter.github.io/bootstrap/assets/css/bootstrap.css' rel='stylesheet'>
<script src='http://twitter.github.io/bootstrap/assets/js/jquery.js'></script>
</head>
<body>

<script type="text/javascript">
window.setInterval(function(){
$.getJSON('api.php', function(data) {
	$('.download-bars').remove();
  var items = [];
  $.each(data.jobs, function() {
    var percent = Math.round((this['mb'] - this['mbleft']) / this['mb'] * 100);
    var active = '';
    if (data['state'] == "Downloading")
    {
    	active = 'active';
    }
    items.push('<div class="progress progress-striped ' + active + '"><div class="bar" style="width: ' + percent + '%;"></div></div>');
  });
  $('<div/>', {
    'class': 'download-bars',
    html: items.join('')
  }).appendTo('body');
});
}, 1000);
</script>

</body></html>