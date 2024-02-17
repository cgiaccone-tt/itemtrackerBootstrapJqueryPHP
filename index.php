<?php

require_once('classes/db.php');
require_once('classes/Item.php');
$db = new db('mysql:host=localhost;dbname=ascendion', 'root', '', array());
$itemObj = new Item($db);
$items = $itemObj->getItems();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
    <title>Ascendion</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="index.css">
    
</head>





<body>
    <div class="container px-4 py-4">
        <div class="row">
            <div class="col">
                <h1>Requested Items</h1>
                <div class="d-flex justify-content-center">
                    <button type="button" id="addRequest" class="btn btn-primary m-2 ml-50">
                        Add Request
                    </button>
                </div>

                <table id="RequestedItems" class="display table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Requested Items</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add Request</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body mx-5">
                    <form id="requestForm" action="">
                        <div class="mt-4 input-group">
                            <label for="fname" class="col-sm-2 col-form-label formLabel">User</label>
                            <div class="col-sm">
                                <input type="text" name="fName" class="form-control formInput" id="fname" placeholder="First Name">
                            </div>
                        </div>
                        <div class="mt-4 input-group">
                            <label for="item" class="col-sm-2 col-form-label formLabel">Requested Items</label>
                            <select name="itemName[]" class="form-select">
                                <option selected>Choose...</option>
                                <?php foreach ($items as $item) : ?>
                                    <option value="<?= $item['id'] ?>"><?= $item['item'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="button" class="btn btn-secondary addRemoveBtns addField">+</button>
                        </div>
                        <input type="hidden" name="action" id="action" value="addRequest">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="Save">Save</button>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
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
            //reset the form
            $("#requestForm").trigger("reset");
            $('.removeField').parent('div').remove();
            $('#exampleModal').modal('toggle');
            $("#exampleModal").modal('show');
        });

        //remove select fields from form
        $("#exampleModal").on('click', '.removeField', function(e) {
            e.preventDefault();
            $(this).parent().remove();
        });

        //add select fields to form
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
            }else{
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
</script>

</html>