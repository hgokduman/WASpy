$(document).ready(function() {
    PageBindEvents(this);
    $(':mobile-pagecontainer').on( 'pagecontainerchange', function(event, ui) { 
        PageBindEvents(ui.toPage);
    });
});

function PageBindEvents(node) {
    $(node).find('#choose-view :radio').bind('change', function() {
        url = '/presence/' + $(node).find('#ui-title').text().substring(1) + '/' + this.value;
        $.mobile.pageContainer.pagecontainer('change', url, {  } );
    });
    
    $(node).find('#toolbar-header').bind('taphold', function(e) {
        var titleNode = $(node).find('#ui-title');
        if(titleNode.css('visibility') == 'hidden') {
            titleNode.css('visibility', 'visible');
        } else {
            titleNode.css('visibility', 'hidden');
        }
    });
}