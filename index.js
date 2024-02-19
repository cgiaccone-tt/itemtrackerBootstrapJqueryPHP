$("document").ready(function () {
  //initialize datatable
  const table = new DataTable("#RequestedItems", {
    ajax: "process.php?action=getRequests",
    columns: [
      {
        data: "fName",
      },
      {
        data: "item",
      },
      {
        data: "type",
      },
      {
        data: "action",
      },
    ],
  });

  //show modal
  $("#addRequest").on("click", function (e) {
    e.preventDefault();
    //reset the form
    $("#requestForm").trigger("reset");
    $("#action").val("addRequest");
    $("#id").remove();
    $(".removeField").parent("div").remove();
    populateSelect();
    $("#requestForm button")
      .removeClass("removeField")
      .addClass("addField")
      .text("+");
    $("#exampleModal").modal("show");
  });

  //remove select fields from form
  $("#exampleModal").on("click", ".removeField", function (e) {
    e.preventDefault();
    $(this).parent().remove();
  });

  //add select fields to form
  $("#exampleModal").on("click", ".addField", function (e) {
    e.preventDefault();
    let val = $(this).closest("div").find("select").val();
    if (val == "Choose...") {
      alert("Please select an item before adding another field");
      return false;
    }
    optionRemoveFunction(val);
    var ele = $(this).closest("div").clone(true);
    //console.log(ele);
    $(this).closest("div").after(ele);
    $(this).removeClass("addField").addClass("removeField").text("-");
  });

  /* $(".addField").on("click", function (e) {
    console.log("clicked");
    let val = $(this).closest("div").find("select").val();
    if (val == "Choose...") {
      alert("Please select an item before adding another field");
      return false;
    }
    optionRemoveFunction(val);
  }); */

  //save/update form data
  //update changes initial hidden input value to updateRequest from addRequest
  $("#exampleModal").on("click", ".btn-primary", function (e) {
    e.preventDefault();
    let form = $("#requestForm");
    let url = "process.php";
    $.ajax({
      type: "POST",
      url: url,
      data: form.serialize(),
      dataType: "json",
      success: function (data) {
        // Ajax call completed successfully
        $("#requestForm").trigger("reset");
        $(".removeField").parent("div").remove();
        $("#exampleModal").modal("toggle");
        //console.log(data);

        //return updated data to main page (datatables)
        table.ajax.url("process.php?action=getRequests").load();
      },
      error: function (data) {
        // Some error in ajax call
        alert(e.responseText);
      },
    });
  });

  //edit form data
  $("#RequestedItems").on("click", ".bi-pencil", function (e) {
    e.preventDefault();

    //open modal and populate form
    $("#exampleModal").modal("show");
    $("#action").val("updateRequest");
    $("#type").remove();
    if ($("#id").length == 0) {
      $("#action").after(
        '<input type="hidden" name="id" id="id" value="' +
          $(this).data("id") +
          '">'
      );
    } else {
      $("#id").val($(this).data("id"));
    }

    populateSelect();

    $.ajax({
      type: "GET",
      url: "process.php",
      data: {
        action: "getRequest",
        id: $(this).data("id"),
      },
      dataType: "json",
      success: function (data) {
        // Ajax call completed successfully
        console.log(data);

        //remove fields from form other than first which has an .addField class instead
        //of .removeField
        $(".removeField").parent("div").remove();

        //populate form with the name
        $("#fname").val(data.fName);

        $("#id").after(
          '<input type="hidden" name="type" id="type" value="' + data.id + '">'
        );

        $("#id").val(data.action);

        //take the items and split them into an array
        let items = data.i.split(",");

        //loop through the array and add the items to the form, set the value of the select
        //to the item and set up the buttons correctly
        let itemVal = 0;
        $.each(items, function (i) {
          let container = $("#requestForm div:last").clone(true);
          $("#requestForm button")
            .removeClass("addField")
            .addClass("removeField")
            .text("-");
          $("#requestForm div:last").after(container);
          itemVal = parseInt(items[i]);
          $("#requestForm div:last select").val(itemVal);
        });

        optionRemoveFunction(itemVal);

        //remove the first requested items field container which we are now not using
        $("#requestForm div").eq(2).remove();
      },
      error: function (data) {
        alert(e.responseText);
      },
    });
  });

  //remove options from select list to enforce only one of each type of item
  function optionRemoveFunction(val) {
    $.ajax({
      type: "GET",
      url: "process.php",
      data: {
        action: "getItemsOfSameType",
        id: val,
      },
      dataType: "json",
      success: function (data) {
        // Ajax call completed successfully
        //Remove the selected items from the select list which are not of the same type as the selected item
        $.each(data, function (i) {
          $("#exampleModal option[value='" + data[i].id + "']").remove();
        });
      },
      error: function (data) {
        alert(e.responseText);
      },
    });
  }

  function populateSelect() {
    $.ajax({
      type: "GET",
      async: false,
      url: "process.php",
      data: {
        action: "getItems",
      },
      dataType: "json",
      success: function (data) {
        // Ajax call completed successfully
        $("#exampleModal select").find("option").remove();
        $("#exampleModal select").append('<option value="">Choose...</option>');
        $.each(data, function (i) {
          $("#exampleModal select").append(
            '<option value="' + data[i].id + '">' + data[i].item + "</option>"
          );
        });
      },
      error: function (e) {
        alert(e.responseText);
      },
    });
  }
});
