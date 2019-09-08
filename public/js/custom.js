function setClipboardText(text){
    var id = "mycustom-clipboard-textarea-hidden-id";
    var existsTextarea = document.getElementById(id);

    if(!existsTextarea){
        console.log("Creating textarea");
        var textarea = document.createElement("textarea");
        textarea.id = id;
        // Place in top-left corner of screen regardless of scroll position.
        textarea.style.position = 'fixed';
        textarea.style.top = 0;
        textarea.style.left = 0;

        // Ensure it has a small width and height. Setting to 1px / 1em
        // doesn't work as this gives a negative w/h on some browsers.
        textarea.style.width = '1px';
        textarea.style.height = '1px';

        // We don't need padding, reducing the size if it does flash render.
        textarea.style.padding = 0;

        // Clean up any borders.
        textarea.style.border = 'none';
        textarea.style.outline = 'none';
        textarea.style.boxShadow = 'none';

        // Avoid flash of white box if rendered for any reason.
        textarea.style.background = 'transparent';
        document.querySelector("body").appendChild(textarea);
        console.log("The textarea now exists :)");
        existsTextarea = document.getElementById(id);
    }else{
        console.log("The textarea already exists :3")
    }

    existsTextarea.value = text;
    existsTextarea.select();

    try {
        var status = document.execCommand('copy');
        if(!status){
            console.error("Cannot copy text");
        }else{
            console.log("The text is now on the clipboard");
        }
    } catch (err) {
        console.log('Unable to copy.');
    }
}



function searchInTable(tableName) {
    // Declare variables
    var input, filter, ul, li, a, i, txtValue;
    input = $('#inputSearch-'+tableName);
    filter = input.val().toUpperCase();
    table = $("#table-" + tableName);
    row = table.find('tbody tr');

    // Loop through all row items, and hide those who don't match the search query
    for (i = 0; i < row.length; i++) {
        line = $(row[i]);
        if (line.text().toUpperCase().indexOf(filter) > -1) {
            $(row[i]).show('slow');
            $(row[i]).removeClass('hidden h-0 p-0');
            console.log('show')
        } else {
            $(row[i]).hide('slow');
            $(row[i]).addClass('hidden h-0 p-0');
            console.log('hide')
        }
    }
}
function searchInCard(moduleName) {
    // Declare variables
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById('inputSearch-' + moduleName);
    filter = input.value.toUpperCase();
    ul = document.getElementById("listCard-" + moduleName);
    li = ul.getElementsByTagName('li');

    // Loop through all list items, and hide those who don't match the search query
    for (i = 0; i < li.length; i++) {
        target = li[i];
        target = $(target);
        if (target.text().toUpperCase().indexOf(filter) > -1) {
            $(li[i]).show('speed');
            $(li[i]).removeClass('hidden h-0 p-0');
            console.log('show')
        } else {
            $(li[i]).hide('speed');
            $(li[i]).addClass('hidden h-0 p-0');
            console.log('hide')
        }
    }
}


function updatePorgressBar(id, value) {
    bar = $('#progress-bar-'+id);
    bar.parent('.progress').addClass('progress-bar-warning');
    bar.attr('aria-valuenow', value);
    bar.css('width', value+'%');
    if (value == 100) {
        bar.parent('.progress').removeClass('progress-bar-warning');
        bar.parent('.progress').addClass('progress-bar-success');
        bar.parent('.progress').hide('slow');
        badge = $('#badge-spider-'+id);
        if (badge) {
            badge.show('slow');
        } 
    }
}

function statusSpider(id, route, website, refresh) {
    var spinner = $("#spider-loading-"+id);
    $.ajax({
        type: 'POST',
        url: route,
        data: {'website': website, 'id': id},
        success: function(response) { 
            console.log("Report: ["+id+"]",response);
            updatePorgressBar(id, response.progress.pourcent);
            if (response.job === "progress") {
                spinner.removeClass('hidden');
            } else if (response.job === "finish" ) {
                spinner.hide('slow');
                clearInterval(loopStatus);
                if (refresh) {
                    location.reload();
                }
            }
        },
    });
}