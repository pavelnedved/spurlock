/**
 *  This script handles functions related to the infinite scrolling of the "list" view
 */

function sticky_relocate() {
    var window_top = $(window).scrollTop();
    var window_bottom = window_top + $(window).height();
    var anchor_top = $('#retTop-anchor').offset().top;
    var anchor_bottom = $('#searchTimer').offset().top;
    var stick_height = window_bottom - anchor_bottom;
    if (window_top > anchor_top) {
        $('#retTop').addClass('sticky');
    } else {
        $('#retTop').removeClass('sticky');
    }
    $('#retTop').css('margin-bottom', ((window_bottom - window_top) / 2) + 'px').css('border-bottom', '1px solid black');
}

$(document).ready(function () {

    //Hide the bottom pagination
    $("#bottomPagination").hide();

    //Unstick the "Return to top" link
    $('#retTop').removeClass('normal');
    //Assign the "Return to top" location handler to the scroll event
    $(window).scroll(sticky_relocate);
    //Set the link's initial location
    sticky_relocate();

    //Assign the functionality to the "Return to top" link (make her go up)
    $("#retTop a").click(function () {
        $("html, body").animate({ scrollTop: 0 }, "slow");
        return false;
    });

    //Change the page enumeration div to say "Showing X" instead of "Showing X of N"
    $("#pageEnumeration").html(function (i, htm) {
        return htm.replace(/^Showing \d+-\d+ of/, "Showing ");
    });

    //Set a margin on the top of the list container
    $("#listCont").css("margin-top", "65px");

    //Enable the infinite scrolling plugin on the list container
    $("#listCont").infinitescroll({
        navSelector: "#pageSelect",
        nextSelector: "#pageSelect a:last",
        itemSelector: ".ajaxLoad",
        bufferPx: 20,
        pathParse: function (thePath, pageNum) {
            newPathFunct = new Object();
            newPathFunct.join = function (currPage) {
                //Overload the join operator to hack in our page select mechanism
                var newPath = thePath.replace(/&start=[0-9]+/, "&start=" + (currPage - 1) * RECORDS_PER_PAGE + "&noFacet=1");
                return newPath;
            }
            return newPathFunct;
        },
        maxPage: function () {
            //Returns the max number of pages based on the total records returned
            var text = $("#pageEnumeration").text();
            var regex = /.* (\d+) records.*/;
            var matches = regex.exec(text);
            //console.log("Max pages: " + Math.ceil(matches[1] / RECORDS_PER_PAGE));
            return Math.ceil(matches[1] / RECORDS_PER_PAGE);
        },
        getCurrPage: function () {
            //Returns the current page number
            //console.log("Current page: " + $("#pageSelect a.bold").first().text());
            return $("#pageSelect a.bold").first().text();
        }
    }, function (arrayOfNewElems) {
        //Set the margin on the new loaded records
        if ($("#leftSidebarOuter").length > 0) {
            $(".ajaxLoad:last").css('margin-left', $("#leftSidebarOuter").width() + 20);
        }
    });

});
