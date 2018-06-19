// $.validator.addMethod('filesize', function () {
//     console.log($('input[name="bulk_upload"]').val());
//     if($('input[name="bulk_upload"]').val()!=""){
//         return ($('input[name="bulk_upload"]')[0].files[0].size<= 1000)
//     } else {
//         return true;
//     }
// }, 'File size must be less than {0}');
$.validator.addMethod("regex",function(value, element, regexp) {
    // console.log('asdasd');
        var check = false;
        return this.optional(element) || regexp.test(value);
    },
    "Please check your input."
);
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
                
            },
            
        },
        messages: {
            providers: {
                required: "Provider is Required",
            },
            bulk_upload: {
                required: "Bulk Upload File is Required",
                extension: "Invalid File Format",
            },
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "bulk_upload")
                {
                    error.insertAfter("a.file-input-wrapper");
                }
                else
                {
                    error.insertAfter(element);
                }
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