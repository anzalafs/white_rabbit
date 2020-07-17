<script>
$(function(){
	getAllFiles();
});


function getAllFiles(){

  // $.ajax({
  //     url: "<?=base_url();?>directory_listing/fetchAllFiles",
  //     type: 'post',
  //     dataType: "json",
  //     data:{},
  //     success: function(data,status) {
  //         var table = $(".tbody");
  //         table.empty();
  //         $.each(data.lists, function (a, b) {
  //             table.append("<tr><td>"+b.file_id+"</td>" +
  //                 "<td>"+b.file_name+"</td>"+
  //                 "<td>"+b.file_uploaded_date+"</td>"+
  //                 "<td><button type='button' class='btn btn-danger' onclick='deleteFile("+b.file_id+")'>Delete</button></td>"+
  //                 "</tr>");
  //         });
  //         if(data.lists.length==0){
  //           table.append("<tr><td colspan='4'>No data found</td></tr>");
  //         }
  //     },
  //     error: function(xhr, desc, err) {
  //         alert('No data found');
  //     }
	//
  //   });
}

function deleteFile(fileId){
	if(confirm("Are yiu sure?")){
		$.ajax({
        type: "POST",
        url: "<?=base_url();?>directory_listing/delete_file",
        data: {fileId:fileId},
        success: function(data){
						var data = JSON.parse(data);
            console.log(data);
						if(data.status==200){
							$("#result").html(data.msg);
	            $("#result").addClass("alert alert-success");
							// getAllFiles();
							location.reload();
						}else{
							$("#result").html(data.msg);
	            $("#result").addClass("alert alert-danger");
						}
        },
        error: function (xhr, ajaxOptions, thrownError) {
          // alert(xhr.status);
          // alert(thrownError);
        }
    });
	}
}

function deleteSelectedFile(fileName){
	if(confirm("Are yiu sure?")){
		$.ajax({
        type: "POST",
        url: "<?=base_url();?>directory_listing/deleteSelectedFile",
        data: {fileName:fileName},
        success: function(data){
						var data = JSON.parse(data);
            console.log(data);
						if(data.status==200){
							$("#result").html(data.msg);
	            $("#result").addClass("alert alert-success");
							// getAllFiles();
							location.reload();
						}else{
							$("#result").html(data.msg);
	            $("#result").addClass("alert alert-danger");
						}
        },
        error: function (xhr, ajaxOptions, thrownError) {
          // alert(xhr.status);
          // alert(thrownError);
        }
    });
	}
}


</script>
