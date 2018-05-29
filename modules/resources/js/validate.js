$.validator.addMethod('filesize', function () {
    console.log($('input[name="bulk_upload"]')[0].files[0].size);
    return ($('input[name="bulk_upload"]')[0].files[0].size<= 200)
}, 'File size must be less than {0}');
$(document).ready(function () {
    $("#bill_details").validate({
        rules: {
            providers: {
                required: true,
            },
            bulk_upload: {
                required:function(element){
                    var valid=false;
                    // return ($("#email").val().length == 0 && $("#fname").val().length == 0 && $("#lname").val().length == 0 && $("#mobile_no").val().length == 0);
                    $('.dynamic_field').each(function() {
                        if ($(this).val() == "") {
                           valid=true;
                           return false;
                        }
                    });
                   return valid; 
                },
                extension: "csv",
                filesize: true,
                
            },
            
        },
        messages: {
            providers: {
                required: "This field is Required",
            },
            bulk_upload: {
                required: "This field is Required",
                extension: "Invalid File Format",
                filesize: "File size must be less than 124kb",
            },
        },
        submitHandler: function (form, event) {
                form.submit();
        }
    });

    $("#remove_filter").validate({
        rules: {
            utility: {
                required: true,
            },
            providers: {
                required: true,
            },
            
        },
        messages: {
            utility: {
                required: "This field is Required",
            },
            providers: {
                required: "This field is Required",
            }
        },
        submitHandler: function (form) {
            form.submit();
        }
    });

    $("#payment").validate({
        rules: {
            merchant: {
                required: true,
            },
            invoice_amount: {
                required: true,
            },
            agree: {
                required: true,
            }
            
        },
        messages: {
            merchant: {
                required: "This field is Required",
            },
            invoice_amount: {
                required: "This field is Required",
            },
            agree: {
                required: "This field is Required",
            }
        },
        submitHandler: function (form) {
            form.submit();
        }
    });
});