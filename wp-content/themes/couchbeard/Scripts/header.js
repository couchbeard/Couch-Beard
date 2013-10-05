var timer_download;
var timer_playing;
var running = false;

$(function() {
	$('#movieCarousel').carousel({
        interval: Math.floor(Math.random() * 4000) + 2500
    });

    $('#showCarousel').carousel({
        interval: Math.floor(Math.random() * 4000) + 2500
    });

    $('#movieCarousel').on('slide', function (e) {
        var idx = $('#movieCarousel .item.active').index();
        if (idx == 24) {

		}
    });

    $('.action').on('click', function () {
		$.post(ajax_url,
		{
			action: 'xbmcInputAction',
			security: ajax_nonce,
			input: $(this).data('action')
		});
	});

	$('#eject').on('click', function () {
		$.post(ajax_url,
		{
			action: 'xbmcEjectDrive',
			security: ajax_nonce
		});
	});

    currentDownloading();
    var timer_download = setInterval(currentDownloading, 5000);

	$(document).on('keydown', function(e) {
		if ($('#ctrl').prop('checked'))
		{
			var cmd;
			switch(e.keyCode)
			{
				case 37:
					cmd = 'left';
					break;
				case 38:
					cmd = 'up';
					break;
				case 39:
					cmd = 'right';
					break;
				case 40:
					cmd = 'down';
					break;
				case 13:
					cmd = 'select';
					break;
				case 8:
					cmd = 'back';
					break;
				default:
					return;
			}
			e.preventDefault();
			$.post(ajax_url,
			{
				action: 'xbmcInputAction',
				security: ajax_nonce,
				input: cmd
			});
		}
	});

	$('#notificationbutton').on('click', function () {
		var e = jQuery.Event("keypress");
		e.which = 13;
		$('#notificationfield').trigger(e);
	});

	$('#notificationfield').on('keypress', function(e) {
        if(e.which == 13 && $(this).val()) {
            $.post(ajax_url,
			{
				action: 'xbmcSendNotification',
				security: ajax_nonce,
				message: $('#notificationfield').val()
			})
			.done(function(data)
			{
				if (data == 1) {
                    $('.label-success').show();
                    setTimeout(function() {
                        $("#message").collapse('hide');
                        $('#notificationfield').val('');
                        $('.label-success').fadeOut(500);
                    }, 2000);

                } else {

                    $('.label-important').show();
                    setTimeout(function() {
                        $('.label-important').fadeOut(500);
                    }, 2000);
                }
			})
			.fail(function(data)
			{
				alert(notification_error);
			});
        }
    });

	currentPlaying();
	var timer_playing = setInterval(currentPlaying, 5000);

	$('.icon-play').on('click', function() {
		$(this).toggleClass('icon-play icon-white');
	});

	$('#xbmc_menu_button').on('click', function() {
		if (running) {
			$('#xbmc_menu_box').animate({
				bottom: '+=10',
				height: 'toggle'
			}, 500, function() {});
		} else {
			$('#xbmc_menu_box_mini').animate({
				bottom: '+=10',
				height: 'toggle'
			}, 500, function() {});
		}
	});

	$(document).keydown(function(e) {
		if (e.which == 83 && e.altKey) {
			e.preventDefault();
			$('#movieName').focus();
		}
	});


	$('#playpause').on("click", function () {
		if (running) {
			$.post(ajax_url,
			{
				action: 'xbmcPlayPause',
				security: ajax_nonce,
				player: 1
			}, null, 'json')
			.done(function(data)
			{
				if (data.result.speed)
				{
					$("#playpause").html('<i class="icon-pause icon-white"></i>');
				}
				else
				{
					$("#playpause").html('<i class="icon-play icon-white"></i>');
				}
			});
		}
	});

	$('#stop').on('click', function () {
		$.post(ajax_url,
		{
				action: 'xbmcInputAction',
				security: ajax_nonce,
				input: 'stop'
		})
		.done(function()
		{
			$('#playpause').html('<i class="icon-play icon-white"></i>');
		});
	});

	$('#statusConnection').on('click', function() {
		$(this).fadeOut(500);
		clearInterval(timer);
	});

	getConnections();
	var timer_connections = setInterval(getConnections, 10000);

	$('#search').on('click', function() {
		if ($('#searchBar').css('display') == 'none') {
			$('#searchBar').slideDown('slow', function() {
				// Animation complete.
			});
		} else {
			$('#searchBar').slideUp('slow', function() {
				// Animation complete.
			});
		}
	});

	$(document).keyup(function(e) {
		if (e.keyCode == 27) { // ESC
			if ($('#searchBar').css('display') != 'none') {
				$('#searchBar').slideUp('slow', function() {
					// Animation complete.
				});
			}
		}
	});

	$('.close').on('click', function() {
		if ($('#searchBar').css('display') != 'none') {
			$('#searchBar').slideUp('slow', function() {
				// Animation complete.
			});
		}
	});
});

function getConnections() {
	jQuery.ajax({
		type: 'POST',
		cache: false,
        url: ajax_url,
		dataType:'json',
		data: {
            action: 'connectionStatus',
            security: ajax_nonce
        },
        success: function(data, textStatus, XMLHttpRequest) {
			if (data.length > 0)
			{
				if ($('#statusConnection').css('display') == 'none') {
					$('#statusConnection').fadeIn(500, function() {
						$('#statusConnection').show();
					});
			}

				$('#statusConnection').attr('title', 'Connection failed to ' + data);
				$('#statusConnection').tooltip({
					placement: 'left'
				});

				$('#statusConnection').tooltip('hide').tooltip('fixTitle');
			}
			else
			{
				if ($('#statusConnection').css('display') != 'none') {
					$('#statusConnection').fadeOut(500, function() {
						$('#statusConnection').hide();
					});
				}
			}
		},
        error: function(MLHttpRequest, textStatus, errorThrown) {
			console.log("error");
        }
	});
}

function currentDownloading() {
    $.post(ajax_url,
    {
        action: 'currentDownloading',
        security: ajax_nonce
    }, null, 'json')
    .done(function(data)
    {
		if (data != -1) {
			if (!running) {
                running = true;
                clearInterval(timer);
                timer = setInterval(currentDownloading, 2000);
            }
            var length = data.length - 1;
            $('#downloads').html('');
            for (var i = length; i >= 0; i--) {
                var listItem = document.createElement('li');
                listItem.innerHTML = data[i].filename;
                $('#downloads').append(listItem);
            }
		} else if (!running) {
            running = false;
            $('#downloads').html(downloads_error);
            clearInterval(timer_download);
            timer_download = setInterval(currentDownloading, 5000);
        }
    })
    .fail(function(data)
    {
        if (running) {
            running = false;
            clearInterval(timer_download);
            timer_download = setInterval(currentDownloading, 5000);
            $('#downloads').html(downloads_error);
        }
    });
}

function currentPlayingTimer() {
	$.post(ajax_url,
	{
		action: 'xbmcPlayerProps',
		security: ajax_nonce
	}, null, 'json')
	.done(function(data)
	{
		if (data != -1) {
			var now = data.result.time.milliseconds + (data.result.time.seconds * 1000) + (data.result.time.minutes * 60 * 1000) + (data.result.time.hours * 60 * 60 * 1000);
			var total = data.result.totaltime.milliseconds + (data.result.totaltime.seconds * 1000) + (data.result.totaltime.minutes * 60 * 1000) + (data.result.totaltime.hours * 60 * 60 * 1000);
			var left = total - now;
			$('#playingRuntime').text(msToTime(left));
			$('#playingProgress').css('width', Math.round(data.result.percentage) + '%');
		}
	});
}

function currentPlaying() {
	$.post(ajax_url,
	{
		action: 'currentPlaying',
		security: ajax_nonce
	}, null, 'json')
	.done(function(data)
	{
		if (data != -1) {
			// Not needed to be updated
			var title = data.label;
			if (data.type == 'episode')
			{
				title = data.showtitle + ' [' + data.season + 'x' + data.episode + '] - ' + data.title;
			}
			if ($('#playingTitle').text() != title) {
				$('#playingTitle').text(title);
				$('#playingCover').attr('src', function() {
					return decodeURIComponent(data.thumbnail.replace('image://', '').replace('.jpg/', '.jpg'));
				});
				clearInterval(timer_playing);
				timer_playing = setInterval(currentPlaying, 1000);
			}

			if (!running && $('#xbmc_menu_box_mini').css('display') == 'none') {
				running = true;
				$("#playpause").html('<i class="icon-pause icon-white"></i>');
				$('#xbmc_menu_box_mini').slideDown(500);
			}

			currentPlayingTimer();
		} else {
			if (running) {
				running = false;
				$('#playpause').html('<i class="icon-play icon-white"></i>');
				$('#xbmc_menu_box').slideUp(500);
				$('#xbmc_menu_box_mini').slideUp(500);
				$('#playingProgress').hide();
				clearInterval(timer_playing);
				timer_playing = setInterval(currentPlaying, 5000);
			}

			if ($('#playingTitle').text() == "" || $('#playingTitle').text() == null) {
				$('#xbmc_menu_box').slideUp(500);
			}
		}
	})
	.fail(function(data)
	{
		if (running) {
			running = false;
			$("#playpause").html('<i class="icon-play icon-white"></i>');
			$('#xbmc_menu_box').slideUp(500);
			$('#xbmc_menu_box_mini').slideUp(500);
			$('#playingProgress').hide();
			clearInterval(timer_playing);
			timer_playing = setInterval(currentPlaying, 5000);
		}
		$('#xbmcConnection').fadeIn(500);
		if ($('#playingTitle').text() == "" || $('#playingTitle').text() == null) {
			$('#xbmc_menu_box').slideUp(500);
		}
	});
}