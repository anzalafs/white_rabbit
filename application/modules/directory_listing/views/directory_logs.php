<center><div id="result"></div></center>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>ID</th>
      <th>Log Action</th>
      <th>File Name</th>
      <th>Date</th>
    </tr>
  </thead>
  <tbody class="tbody">
    <?php
    if(count($results)>0){
      foreach($results as $row){
        ?>
        <tr>
          <td><?=$row->log_id;?></td>
          <td><?=$row->log_action;?></td>
          <td><?=$row->file_name;?></td>
          <td><?=date("d M, Y", strtotime($row->log_action_date));?></td>
        </tr>
        <?php
      }
    }
    ?>
  </tbody>
</table>

<p><?php echo $links; ?></p>
