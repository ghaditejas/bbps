$.validator.addMethod('filesize', function () {
    return ($('input[name="bulk_upload"]')[0].files[0].size<= 200)
}, 'File size must be less than {0}');
$(document).ready(function () {
    $("#bill_details").validate({
        rules: {
            providers: {
                required: true,
            },
            email: {
                required: function(element){
                    return ($("#bulk_upload").val().length == 0);
                },
                email: true,
            },
            bulk_upload: {
                required:function(element){
                    return ($("#email").val().length == 0 && $("#fname").val().length == 0 && $("#lname").val().length == 0 && $("#mobile_no").val().length == 0);
                },
                extension: "csv",
                filesize: true,
                
            },
            fname:{
                required: function(element){
                    return ($("#bulk_upload").val().length == 0);
                },    
            },
            lname:{
                required: function(element){
                    return ($("#bulk_upload").val().length == 0);
                },
            },
            mobile_no:{
                required: function(element){
                    return ($("#bulk_upload").val().length == 0);
                },
                number:true,
                maxlength: 10,
                minlength: 10,
            },
            
        },
        messages: {
            providers: {
                required: "This field is Required",
            },
            email: {
                required: "This field is Required",
                email: "Enter a Valid Email ID"
            },
            bulk_upload: {
                required: "This field is Required",
                extension: "Invalid File Format",
                filesize: "File size must be less than 124kb",
            },
            fname: {
                required: "This field is Required",
            },
            lname: {
                required: "This field is Required",
            },
            mobile_no: {
                required: "This field is Required",
                number: "Only Numbers are allowed",
                maxlength: "The length of input should be 10",
                minlength: "The length of input should be 10",
            },
        },
        submitHandler: function (form) {
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