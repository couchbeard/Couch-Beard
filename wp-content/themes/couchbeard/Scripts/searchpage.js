//$notifyContainer = $("#notification").notify();

$("#addMovie").on("click", function() {
    jQuery.ajax({
        type: 'POST',
        cache: false,
        url: ajax_url,
        data: {
            action: 'addMovie',
            security: ajax_nonce,
            id: imdbID
        },
        success: function(data, textStatus, XMLHttpRequest) {
			if (data == 1) {
				generate(movie_title, "success");
                //create("default", { title:movie_title, text:tv_msg});
				$('#addMovie').attr("disabled", true);
				$('#addMovie').html('<i>Movie added</i>');
			} else {
                generate(err_msg, "error");
				//create("withIcon", { title:err_title, text:err_msg },{
				//	expires:false});
			}
        },
        error: function(MLHttpRequest, textStatus, errorThrown) {
            alert(movie_error);
        }
    });
});

$("#addTV").on("click", function() {
    jQuery.ajax({
        type: 'POST',
        cache: false,
        url: ajax_url,
        data: {
            action: 'addTV',
            security: ajax_nonce,
            id: imdbID
        },
        success: function(data, textStatus, XMLHttpRequest) {
			if (data == 1) {
                generate(tv_msg, "success");
				//create("default", { title:tv_title, text:tv_msg});
				$('#addTV').attr("disabled", true);
				$('#addTV').html('<i>TV show added</i>');
			} else {
                generate(err_msg, "error");
				//create("withIcon", { title:err_title, text:err_msg, icon: images_src + '/alert.png' },{
				//	expires:false});
			}
        },
        error: function(MLHttpRequest, textStatus, errorThrown) {
            alert(movie_error);
        }
    });
});