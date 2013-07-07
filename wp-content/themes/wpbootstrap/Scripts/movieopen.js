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

    $.post(ajax_url,
    {
        action: 'movieInfo',
        security: ajax_nonce,
        imdb: imdb
    }, null, 'json')
    .done(function(data)
    {
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
    })
    .fail(function()
    {
        $("#myMovie #title").text(no_movie_found);
    });
});