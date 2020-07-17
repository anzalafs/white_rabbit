<script>

$( "#fileUploadForm" ).submit(function( event ) {
  event.preventDefault();
	var postData = new FormData(document.getElementById("fileUploadForm"));
	$.ajax({
        type: "POST",
        url: "<?=base_url();?>directory_upload/upload_file",
        processData: false,
        cache: false,
        contentType: false,
        data: postData,
        success: function(data){
						var data = JSON.parse(data);
            console.log(data);
						if(data.status==200){
							$("#result").html(data.msg);
	            $("#result").addClass("alert alert-success");
							$('#fileUploadForm').trigger("reset");
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
});
</script>
