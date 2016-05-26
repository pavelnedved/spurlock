/**
 *  This script handles functions related to the infinite scrolling of the "grid" view
 */

//Relocates the "Return to top" link depending on the current location of the browser view-port
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

    //Set a margin on the top of the grid container
    $("#gridCont").css("margin-top", "70px");

    //Enable the infinite scrolling plugin on the grid container
    $("#gridCont").infinitescroll({
        navSelector: "#pageSelect",
        nextSelector: "#pageSelect a:last",
        itemSelector: ".ajaxLoad",
        bufferPx: 100,
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
            return Math.ceil(matches[1] / RECORDS_PER_PAGE);
        },
        getCurrPage: function () {
            //Returns the current page number
            return $("#paginationViewSelectCont a.bold").text();
        }
    }, function (arrayOfNewElems) {
        //Iterates over the newly added elements and adds tooltips to them
        if (arrayOfNewElems == null || arrayOfNewElems.length < 1) {
            return;
        }
        $(arrayOfNewElems + ".artCont > a").each(function () {
            $(this).qtip({
                content: {
                    text: $(this).parent().find(".artInfo").html(),
                    title: {
                        text: $(this).parent().find(".artTitle").html(),
                        button: 'Close'
                    }
                },
                hide: {
            fixed: true
            },
                position: {
                    my: "bottom center",
                    at: "top center",
                    target: $(this).parent().find("img"),
                    adjust: {
                    y: 5
                    },
                    effect: false // Disable positioning animation
                },
                show: {
                    event: "hover",
                    solo: true // Only show one tooltip at a time
                },
                style: {
                    classes: "qtip-wiki qtip-light qtip-shadow"
                }
            })
        });
    });

    //Add tooltips to each result
    $(".artCont > a").each(function () {
        //console.log(this);
        $(this).qtip({
            content: {
                text: $(this).parent().find('.artInfo').html(),
                title: {
                    text: $(this).parent().find('.artTitle').html(),
                    button: 'Close'
                }
                
            },
            hide: {
            fixed: true
            },
            position: {
                my: 'bottom center',
                at: 'top center',
                target: $(this).parent().find('img'),
                adjust: {
                    y: 5
                    },
                effect: false // Disable positioning animation
            },
            show: {
                event: 'mouseenter mouseleave', //This line was "event: 'hover',"
                solo: true // Only show one tooltip at a time
            },
            style: {
                classes: 'qtip-wiki qtip-light qtip-shadow'
            }
        })
    });

});
