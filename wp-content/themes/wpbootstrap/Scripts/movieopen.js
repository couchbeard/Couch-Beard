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

$(document).on("click", "#movieopen_owned", function () {
    var id = $(this).data('id');
    $("#myMovie #title").text( '' );
    $("#myMovie #rating").text( '' );
    $("#myMovie #votes").text( '' );
    $("#myMovie #genres").text( '' );
    $("#myMovie #year").text( '' );             
    $("#myMovie #runtime").text( '' );
    $("#myMovie #actors").text( '' );               
    $("#myMovie #writers").text( '' );
    $("#myMovie #plot").text( '' );             
    $("#myMovie #poster").attr("src", '');  
    $("#myMovie #play").data("id", '');    
    $("#play").button('reset');
    $("#play").removeAttr("disabled");

    $.post(ajax_url,
    {
        action: 'movieXbmcInfo',
        security: ajax_nonce,
        movieid: id
    }, null, 'json')
    .done(function(data)
    {
        $("#myMovie #title").text( data.label );
        $("#myMovie #rating").text( data.rating.toFixed(1) );
        $("#myMovie #genres").text( data.genre );
        $("#myMovie #year").text( data.year );              
        $("#myMovie #runtime").text( formatSeconds(data.runtime) );
        $("#myMovie #plot").text( data.plot );              
        $("#myMovie #poster").attr('src', decodeURIComponent(data.thumbnail.replace('image://', '').replace('.jpg/', '.jpg')));
        $("#myMovie #play").data('id', data.movieid);
    })
    .fail(function(data)
    {
        $('#myMovie #title').text(no_movie_found); 
    });
});

    function formatSeconds(sec) {
        var hour = Math.floor(sec / 3600);
        sec -= hour * 3600;
        var min = Math.floor(sec / 60);
        sec -= min * 60;
        return hour + ":" + (min < 10 ? '0' + min : min) + ":" + (sec < 10 ? '0' + sec : sec);
    }

$(function() {
    $('#play').on("click", function () {
        var id = $(this).data('id');  

        $.post(ajax_url,
        {
            action: 'xbmcPlayMovie',
            security: ajax_nonce,
            movieid: id
        }, null, 'json')
        .done(function(data)
        {
            if (data.result == 'OK')
            {
                $("#play").button('loading');
                $("#play").attr("disabled", "disabled");
            }
        });
    });
});