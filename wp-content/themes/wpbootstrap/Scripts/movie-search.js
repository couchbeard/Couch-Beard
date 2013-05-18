jQuery(document).ready(function ($) {
    var acs_action = 'myprefix_autocompletesearch';
    $("#movieName").autocomplete({
        minLength   : 2,
        delay       : 500,
        source: function(req, response) {  
            $.getJSON(MyAcSearch.url+'?callback=?&action='+acs_action, req, response);
        },
        select: function(event, ui) {
            window.location.href = '?page_id=' + ui.item.searchpageid + '&id=' + ui.item.imdbid;
        }
    }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
        var inner_html =
        '<a id="movieSearch">' +
            '<div class="row">' +
                '<div class="list_item_container">' +
                    '<div class="poster span1 pull-left">' +
                        '<img class="img-rounded" src="' + item.image + '">' +
                    '</div>' +
                    '<div class="label label-info">' +
                        item.type +
                    '</div>' +
                    '<div class="pull-right">' +
                        '<div class="label">' + item.year + '</div>' +
                    '</div>' +
                    '<div class="row">' +
                        '<div class="span2">' +
                            '<small>' + item.title + '</small>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</a>' +
        '<hr>';
        return $( "<li></li>" )
            .data( "item.autocomplete", item )
            .append(inner_html)
            .appendTo( ul );
    };
});
/*
$.fn.defaultText = function(value){ 
    
    var element = this.eq(0);
    element.data('defaultText',value);
    
    element.focus(function(){
        if(element.val() == value){
            element.val('').removeClass('defaultText');
        }
    }).blur(function(){
        if(element.val() == '' || element.val() == value){
            element.addClass('defaultText').val(value);
        }
    });
    
    return element.blur();
}*/




