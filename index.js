(function() {
    'use strict'
    const forms = document.querySelectorAll('.requires-validation')
    Array.from(forms)
        .forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
})()
$('document').ready(function() {

    //initialize datatable
    const table = new DataTable('#RequestedItems', {
        ajax: 'process.php?action=getRequests',
        columns: [{
                data: 'fName'
            },
            {
                data: 'item'
            },
            {
                data: 'type'
            },
            {
                data: 'action'
            }
        ]
    });

    //show modal
    $("#addRequest").on('click', function(e) {
        e.preventDefault();
        $("#exampleModal").modal('show');
    });

    //remove fields from form
    $("#exampleModal").on('click', '.removeField', function(e) {
        e.preventDefault();
        $(this).parent().remove();
    });

    //add fields to form
    $("#exampleModal").on('click', '.addField', function(e) {
        e.preventDefault();
        var ele = $(this).closest('div').clone(true);
        //console.log(ele);
        $(this).closest('div').after(ele);
        $(this).removeClass('addField').addClass('removeField').text('-');
    });

    //save/update form data
    //update changes initial hidden input value to updateRequest from addRequest
    $("#exampleModal").on('click', '.btn-primary', function(e) {
        e.preventDefault();
        /* if($("#fname").val() == '' || $("select").val() == ''){
            return;
        } */
        let form = $("#requestForm");
        let url = "process.php";
        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(),
            dataType: "json",
            success: function(data) {

                // Ajax call completed successfully
                $("#requestForm").trigger("reset");
                $('.removeField').parent('div').remove();
                $('#exampleModal').modal('toggle');
                //console.log(data);

                //return updated data to main page (datatables)
                table.ajax.url('process.php?action=getRequests').load();
            },
            error: function(data) {

                // Some error in ajax call
                alert(e.responseText);
            }
        });
    });

    //edit form data
    $("#RequestedItems").on('click', '.bi-pencil', function(e) {
        e.preventDefault();
        //console.log($(this).data('id'));

        //open modal and populate form
        $("#exampleModal").modal('show');
        $("#action").val('updateRequest');
        if ($("#id").length == 0) {
            $('#action').after('<input type="hidden" name="id" id="id" value="' + $(this).data('id') + '">');
        } else {
            $("#id").val($(this).data('id'));
        }

        $.ajax({
            type: "GET",
            url: "process.php",
            data: {
                action: 'getRequest',
                id: $(this).data('id')
            },
            dataType: "json",
            success: function(data) {
                // Ajax call completed successfully

                //remove fields from form other than first which has an .addField class instead 
                //of .removeField
                $(".removeField").parent('div').remove();

                //populate form with the name
                $("#fname").val(data.fName);

                //take the items and split them into an array
                let items = data.i.split(',');

                //loop through the array and add the items to the form, set the value of the select 
                //to the item and set up the buttons correctly
                $.each(items, function(i) {
                    let container = $("#requestForm div:last").clone(true);
                    $("#requestForm button").removeClass('addField').addClass('removeField').text('-');
                    $("#requestForm div:last").after(container);
                    let itemVal = parseInt(items[i]);
                    $("#requestForm div:last select").val(itemVal);
                });
                //remove the first requested items field container which we are now not using
                $("#requestForm div").eq(2).remove();
            },
            error: function(data) {
                alert(e.responseText);
            }
        });

    });
});