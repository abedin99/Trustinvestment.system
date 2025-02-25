
function logExportActivity(type, model) {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: 'POST',
        url: '/store-export-activity-log',
        data: {
            _token: CSRF_TOKEN,
            type,
            model
        },
        dataType: 'JSON',
        success: function(results) {
            // Handle success if needed
        },
        error: function(err) {
            // Handle error if needed
        }
    });
}

function adminLogExportActivity(type, model) {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: 'POST',
        url: '/admin/store-export-activity-log',
        data: {
            _token: CSRF_TOKEN,
            type,
            model
        },
        dataType: 'JSON',
        success: function(results) {
            // Handle success if needed
        },
        error: function(err) {
            // Handle error if needed
        }
    });
}