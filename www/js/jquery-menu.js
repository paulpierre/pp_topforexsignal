// DOM Ready
$(function() {

    var $el, leftPos, newWidth;
    
    /* Add Magic Line markup via JavaScript, because it ain't gonna work without */
    $("#main-menu ul").append("<li id='magic-line'></li>");
    
    /* Cache it */
    var $magicLine = $("#magic-line");
    
    $magicLine
        .width($(".current_page_item").width())
        .css("left", $(".current_page_item a").position().left)
        .data("origLeft", $magicLine.position().left)
        .data("origWidth", $magicLine.width());
        
    $("#main-menu ul li").find("a").hover(function() {
        $el = $(this);
        leftPos = $el.position().left;
        newWidth = $el.parent().width();
        
        $magicLine.stop().animate({
            left: leftPos,
            width: newWidth
        });
    }, function() {
        $magicLine.stop().animate({
            left: $(".current_page_item a").position().left,
            width: $(".current_page_item").width() 
        });
    });
    
});