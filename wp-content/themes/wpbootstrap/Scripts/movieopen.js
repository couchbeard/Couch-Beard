$(document).on("click", "#movieopen", function () {
    var imdb = $(this).data('id');
    $("#myMovie #title").text( '' );
    $("#myMovie #rating").text( '' );
    $("#myMovie #votes").text( '' );
    $("#myMovie #genres").text( '' );
    $("#myMovie #year").text( '' );
    $("#myMovie #runtime").text( '' );
    $("#myMovie #actors").text( '' );
    $("#myMovie #writers").text( '' );
    $("#myMovie #plot").text( '' );
    $("#myMovie #searchpageCover").attr("src", '');

	jQuery.ajax({
        type: 'POST',
        cache: false,
        url: ajax_url,
        data: {
            action: 'movieInfo',
            security: ajax_nonce,
            imdb: imdb
        },
        dataType:'json',
        success: function(data, textStatus, XMLHttpRequest) {
            $("#myMovie #title").text( data.Title );
            $("#myMovie #rating").text( data.imdbRating );
            $("#myMovie #votes").text( data.imdbVotes );
            $("#myMovie #genres").text( data.Genre );
            $("#myMovie #year").text( data.Year );
            $("#myMovie #runtime").text( data.Runtime );
            $("#myMovie #actors").text( data.Actors );
            $("#myMovie #writers").text( data.Writer );
            $("#myMovie #plot").text( data.Plot );
            $("#myMovie #searchpageCover").attr("src", data.Poster);
        },
        error: function(MLHttpRequest, textStatus, errorThrown) {
            $("#myMovie #title").text(no_movie_found);
        }
    });
});