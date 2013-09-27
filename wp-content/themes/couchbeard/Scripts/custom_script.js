function create( template, vars, opts ) {
	return $notifyContainer.notify("create", template, vars, opts);
}

function writeCookie(name,value,days) {
    var date, expires;
    if (days) {
        date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        expires = "; expires=" + date.toGMTString();
            }else{
        expires = "";
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookie(name) {
    var i, c, ca, nameEQ = name + "=";
    ca = document.cookie.split(';');
    for(i=0;i < ca.length;i++) {
        c = ca[i];
        while (c.charAt(0)==' ') {
            c = c.substring(1,c.length);
        }
		if (c.indexOf(nameEQ) == 0) {
			return c.substring(nameEQ.length,c.length);
        }
    }
    return '';
}

$(window).bind("load", function() {
	var timeout = setTimeout(function() {
		$("img.lazysporty").trigger("sporty")
	}, 2000);
});

function msToTime(s) {
	var ms = s % 1000;
	s = (s - ms) / 1000;
	var secs = s % 60;
	s = (s - secs) / 60;
	var mins = s % 60;
	var hrs = (s - mins) / 60;
	return hrs + ':' + (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + Math.round(secs);
}

$(function() {
	$('img.lazy').lazyload({});
	$("img.lazysporty").lazyload({
		event : "sporty"
	});
});
	$(".rating").jRating({
		step: true,
		length: 10,
		rateMax: 10,
		isDisabled: true,
		decimalLength: 0
	});

	$(".rating").hover(
		function () {
			$('.rating').tooltip('show');
		}
	);