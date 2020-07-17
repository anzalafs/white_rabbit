<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<form class="form-inline" action="" id="searchForm" method="get">
  <div class="form-group">
    <input type="text" class="form-control" id="" name="file_name" placeholder="Enter file name" >
  </div>
  <button type="submit" class="btn btn-default">Search</button>
</form><br>

<?php
$this->load->helper('directory');
$map = directory_map('./uploads/');
// echo "<pre>";print_r($map);

$files = scandir('./uploads/');
$search = '';
if(isset($_GET['file_name']) && $_GET['file_name']!=''){
  $lists = array();
  $search = $_GET['file_name'];
  foreach ($files as $file) {
      if (strstr($file, $search)) {
           //file found
           array_push($lists,$file);
      }
  }
}else{
  $lists = array_slice(scandir('./uploads/'), 2); //scandir('./uploads/');
}

?>
<table class="table table-bordered">
  <thead>
    <tr>
      <th>File Name</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody class="tbody">
    <?php
    if(count($lists)>0){
      foreach($lists as $row){
        ?>
        <tr>
          <td><?=$row;?></td>
          <td><button type="button" class="btn btn-danger" onclick="deleteSelectedFile('<?=$row;?>')">Delete</button></td>
        </tr>
        <?php
      }
    }
    ?>
  </tbody>
</table>

<center><div id="result"></div></center>
<?php
/*
<table class="table table-bordered">
  <thead>
    <tr>
      <th>ID</th>
      <th>File Name</th>
      <th>Uploaded Date</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody class="tbody">
    <?php
    if(count($results)>0){
      foreach($results as $row){
        ?>
        <tr>
          <td><?=$row->file_id;?></td>
          <td><?=$row->file_name;?></td>
          <td><?=date("d M, Y", strtotime($row->file_uploaded_date));?></td>
          <td><button type="button" class="btn btn-danger" onclick="deleteFile(<?=$row->file_id;?>)">Delete</button></td>
        </tr>
        <?php
      }
    }
    ?>
  </tbody>
</table>
*/
?>

<p><?php echo $links; ?></p>
