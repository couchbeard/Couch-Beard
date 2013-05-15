jQuery(document).ready(function ($) {
    var acs_action = 'myprefix_autocompletesearch';
    $("#movieName").autocomplete({
        minLength   : 2,
        delay       : 500,
        source: function(req, response) {  
            $.getJSON(MyAcSearch.url+'?callback=?&action='+acs_action, req, response);
        },
        select: function(event, ui) { 
            var id = ui.item.id;
            window.location.href = 'http://www.imdb.com/title/' . id;
        }
    }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
        var inner_html = '<a><div class="list_item_container"><div class="poster"><img src="' + item.image + '"></div><div class="label">' + item.title + '</div><div class="description">' + item.type + '</div></div></a>';
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