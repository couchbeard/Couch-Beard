jQuery(document).ready(function ($) {
    var acs_action = 'myprefix_autocompletesearch';

    $("#movieName").autocomplete({
        minLength   : 2,
        delay       : 50,
        cache       : true,
        source: function(req, response) {
            $.getJSON(MyAcSearch.url+'?callback=?&action='+acs_action, req, response);
        },
        select: function(event, ui) {
            window.location.href = '?page_id=' + ui.item.searchpageid + '&id=' + ui.item.imdbid;
        }
    }).focus(function() {
        $(this).data("autocomplete").search($(this).val());
    }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
        if (item === null || item.imdbid == -1) {
            return $( "<li></li>" )
            .data( "item.autocomplete", item )
            .append(item.title)
            .appendTo(ul);
        }
        var inner_html =
        '<a id="movieSearch">' +
                '<div class="list_item_container">' +
                    '<div class="poster pull-left">' +
                        '<img class="img-rounded" src="' + item.image + '">' +
                    '</div>' +
                    '<div class="badge badge-info pull-right">' +
                        item.type +
                    '</div>' +
                    '<div class="pull-left">' +
                        '<div class="yearlabel label label-inverse">' + item.year + '</div>' +
                    '</div><br />' +
                        '<div class="title">' +
                            '<small>' + item.title + '</small>' +
                        '</div>' +
                '</div>' +
        '</a>';
        return $( "<li></li>" )
            .data( "item.autocomplete", item )
            .append(inner_html)
            .appendTo( ul );
    }
    $( "#movieName" ).autocomplete({ position: { my : "right top", at: "right bottom" } });
});