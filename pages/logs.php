<div class="container base-box">
 <table class="table table-hover">
 <thead>
   <tr>
     <th>Type</th>
     <th>Message</th>
     <th>Date</th>
   </tr>
 </thead>
 <tbody>

 <?php

    //addLog(2,1,"Test");

    $query = "SELECT id,type,msg,timestamp FROM logs WHERE user_id = ? ORDER by id DESC LIMIT 50";
    $stmt = $database->prepare($query);
    $stmt->bind_param('i', $USER_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {

        echo "<tr>";
        if ($row['type'] == "1") {
          echo "<td>API</td>";
        }
        echo "<td>".escape($row['msg'])."</td>";
        echo "<td>".date("d.m.Y H:i",escape($row['timestamp']))."</td>";

      echo "<tr>";
    }

?>
 </tbody>
</table>
</div>
