var ajaxUrl = window.location.protocol + "//" + window.location.host + "/wp-admin/admin-post.php";

(function($) {
    $(document).on('click', '#btn-generate', function() {
        $.ajax({
            url: ajaxUrl + "?action=generate_key",
            type: 'GET',
            cache: false,
            success: function(response) {
                $('#app-key').val(response);
            },
            error: function(error) {
                console.log(error);
            }
        });
    });

    $(document).on('click', '#btn-create', function() {
        var appName = $('#app-name').val();
        var appKey = $('#app-key').val();
        if (appName && appKey) {
            $('.result').css({
                "display": "none"
            });
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                cache: false,
                data: {
                    action: 'insert_app',
                    name: $('#app-name').val(),
                    key: $('#app-key').val()
                },
                success: function(response) {
                    if (response == 'success') {
                        $('.result').css({
                            "display": "block",
                            "color": "#008000"
                        });
                        $('.result').text("App Created Successfully !");
                        location.reload();
                    } else {
                        $('.result').css({
                            "display": "block",
                            "color": "#ff0000"
                        });
                        $('.result').text("Something Went Wrong ! Try Again");
                    }

                    $('#app-name').val('');
                    $('#app-key').val('');
                },
                error: function(error) {
                    $('.result').css({
                        "display": "block",
                        "color": "#ff0000"
                    });
                    $('.result').text("Something Went Wrong ! Try Again");
                    $('#app-name').val('');
                    $('#app-key').val('');
                }
            });
        } else {
            $('.result').css({
                "display": "block",
                "color": "#ff0000"
            });
            $('.result').text("Insert App Name And App Key");
        }
    });
})(jQuery);

function deleteApp(id) {
    (function($) {
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            cache: false,
            data: {
                action: "delete_app",
                id: id
            },
            success: function(response) {
                if (response == "success") {
                    location.reload();
                }
            },
            error: function(error) {
                console.log("error:" + error.Message);
            }
        });
    })(jQuery);
}

function showKey(title, key) {
    (function($) {
        $("#dialog").dialog({
            autoOpen: false,
            width: 500,
            title: title,
            closeText: "",
            buttons: {
                "Close": function() {
                    $(this).dialog("close")
                }
            }
        });

        $('#dialog').text(key);
        $('#dialog').dialog("open");

    })(jQuery);
}
