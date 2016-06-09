<div class="col-md-8 col-md-offset-2 base-box">

<?php if (strpos($page, 'trigger') !== false) {

 $id = str_replace("dashboard?server=", "", $page);
 $id = str_replace("?trigger", "", $id);
 if(!preg_match("/^[0-9]+$/",$id)){ header('Location: index.php?page=dashboard'); }
 if(!checkAccess($id,$USER_ID)) {  header('Location: index.php?page=dashboard'); }

 ?>
   <ul class="nav nav-tabs">
     <li><a href="index.php?page=dashboard">Servers</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>">Overview</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?network">Network</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?cpu">CPU</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?memory">Memory</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?hdd">HDD</a></li>
     <li class="active"><a href="index.php?page=dashboard?server=<?= $id ?>?trigger">Trigger</a></li>
     <li><a href="index.php?page=logout">Logout</a></li>
   </ul>

   <script>
        function addLoadEvent(func) {
        var oldonload = window.onload;
        if (typeof window.onload != 'function') {
          window.onload = func;
        } else {
          window.onload = function() {
            if (oldonload) {
              oldonload();
            }
            func();
          }
        }
      }
  </script>
   <?php

   $msg = "";

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {

   if (isset($_POST['confirm'])) {

    $steal = $_POST['steal'];
    $io = $_POST['io'];
    $load = $_POST['load'];

    $success = true; $reboot = 0; $offline = 0;

    if (!is_numeric($steal)) {$msg = "Not a Number."; $success = false;}
    if (!is_numeric($io)) {$msg =  "Not a Number."; $success = false;}
    if (!is_numeric($load)) {$msg =  "Not a Number."; $success = false;}
    if (isset($_POST['reboot'])) { $reboot = 1;}
    if (isset($_POST['offline'])) { $offline = 1;}

    if ($success == true) {

      $stmt = $mysqli->prepare("UPDATE servers SET cpu_alert = ? ,cpu_steal_alert = ? ,io_wait_alert = ? , reboot_alert = ?, offline_alert = ? WHERE id = ?");
      $stmt->bind_param('iiiiii',$load,$steal,$io,$reboot,$offline,$id);
      $stmt->execute();
      $stmt->close();

    }
    }
  }

  $stmt = $mysqli->prepare("SELECT cpu_alert,cpu_steal_alert,io_wait_alert,reboot_alert,offline_alert  FROM servers WHERE id = ? LIMIT 1");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $stmt->bind_result($db_cpu,$db_steal,$db_wait,$db_reboot,$db_offline_alert);
  $stmt->fetch();
  $stmt->close();


  ?>
       <?php
         if ($msg != "" and $success == false) {
           echo'<div class="alert alert-danger col-md-12" style="text-align: center;">
                 <h2>Error!</h3>
                 <p>'.$msg.'</p>
                 </div>';
         } elseif ($msg != "" and $success == true) {
           echo'<div class="alert alert-success col-md-12" style="text-align: center;">
                 <h2>Okay!</h3>
                 <p>'.$msg.'</p>
                 </div>';
         }
        ?>
        <form class="form-horizontal" role="form" action="index.php?page=dashboard?server=<?= $id ?>?trigger" method="post">
          <div class="col-sm-2">
            <h3>Trigger - CPU</h3>
           <div class="form-group">
             <div class="col-xs-10">
             <label class="" for="email">CPU Load %</label>
             <input type="text" name="load" class="form-control" value="<?= $db_cpu; ?>">
              </div>
           </div>
           <div class="form-group">
             <div class="col-xs-10">
             <label class="" for="pwd">CPU Steal %</label>
             <input type="text" name="steal" class="form-control" value="<?= $db_steal; ?>">
              </div>
           </div>
           <div class="form-group">
               <div class="col-xs-10">
                   <button type="submit" name="confirm" class="btn btn-primary">Save</button>
               </div>
           </div>
           </div>

          <div class="col-sm-2">
            <h3>Trigger - I/O</h3>
           <div class="form-group">
             <div class="col-xs-10">
             <label class="" for="io">I/O Wait %</label>
             <input type="text" name="io" class="form-control" value="<?= $db_wait; ?>">
              </div>
           </div>
           </div>
           <div class="col-sm-2">
             <h3>Trigger - System</h3>
             <div class="checkbox">
               <label><input type="checkbox" class="form-control" name="reboot" id="reboot" checked data-toggle="toggle" value="">Reboot</label>
               <?php
                 if ($db_reboot == 1) {
                  ?>
                  <script> function toggleOnreboot() { $('#reboot').bootstrapToggle('on'); } addLoadEvent(toggleOnreboot); </script>
                  <?php
                } elseif ($db_reboot == 0) { ?>
                  <script> function toggleOffreboot() { $('#reboot').bootstrapToggle('off'); }  addLoadEvent(toggleOffreboot); </script>
                  <?php
                }
              ?>
            </div>
            <div class="checkbox">
              <label><input type="checkbox" class="form-control" name="offline" id="offline" checked data-toggle="toggle" value="">Outage</label>
              <?php
                if ($db_offline_alert == 1) {
                 ?>
                 <script> function toggleOnOffline() { $('#offline').bootstrapToggle('on'); } addLoadEvent(toggleOnOffline); </script>
                 <?php
               } elseif ($db_offline_alert == 0) { ?>
                 <script> function toggleOffOffline() { $('#offline').bootstrapToggle('off'); }  addLoadEvent(toggleOffOffline); </script>
                 <?php
               }
             ?>
            </div>
            </div>
        </form>
</div>

<?php

} elseif (strpos($page, 'network') !== false) {

 $id = str_replace("dashboard?server=", "", $page);
 $id = str_replace("?network", "", $id);
 if(!preg_match("/^[0-9]+$/",$id)){ header('Location: index.php?page=dashboard'); }
 if(!checkAccess($id,$USER_ID)) {  header('Location: index.php?page=dashboard'); }

 ?>
   <ul class="nav nav-tabs">
     <li><a href="index.php?page=dashboard">Servers</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>">Overview</a></li>
     <li class="active"><a href="index.php?page=dashboard?server=<?= $id ?>?network">Network</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?cpu">CPU</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?memory">Memory</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?hdd">HDD</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?trigger">Trigger</a></li>
     <li><a href="index.php?page=logout">Logout</a></li>
   </ul>

   <?php

     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       $range = explode("-", $_POST['selector']);
       if (is_numeric($range['0']) AND is_numeric($range['1'])) {
         $data_start = $range['0'];
         $data_stop = $range['1'];
       } else {
         $data_start = time() - 1500;
         $data_stop = time();
       }
     } else {
       $data_start = time() - 1500;
       $data_stop = time();
     }
    ?>

   <div class="col-md-12 ct-chart"><center><h2>Network Usage</h2></center>
     <form method="post">
       <div class="dropdown dropdown-submit-input">
         <input type="hidden" name="selector" />
         <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
           <?php echo date("d.m.Y H:i",$data_start).'-'.date("H:i",$data_stop); ?>
           <span class="caret"></span>
         </button>
         <ul class="dropdown-menu scrollable-menu" aria-labelledby="dropdownMenu1">
           <?php
           $query = "SELECT server_timestamp FROM servers_data WHERE server_id = ".$id." ORDER by id DESC";

           if ($result = $mysqli->query($query)) {

               $start="";
               $cycles=1;

               /* fetch object array */
               while ($row = $result->fetch_row()) {
                 if ($cycles == 25) {
                   if ($start != "") {
                     echo '<li><a href="#" data-value="'.$row['0'].'-'.$start.'">'.date("d.m.Y H:i",$row['0']).'-'.date("H:i",$start).'</a></li>';
                   }
                   $start = $row['0'];
                   $cycles = 1;
                 }
                 $cycles++;
               }
               /* free result set */
               $result->close();
           }
           ?>
         </ul>
       </div>
     </form>
     <div id="chart"></div>
   </div>

   </div>

   <?php
   $query = "SELECT server_rx_diff,server_tx_diff,server_timestamp FROM servers_data WHERE server_id = ".$id." AND server_timestamp >= ".$data_start." AND server_timestamp <= ".$data_stop."   ORDER by id";

   $server_rx = [];
   $server_tx = [];
   $server_time = [];

   if ($result = $mysqli->query($query)) {

       /* fetch object array */
       while ($row = $result->fetch_row()) {
          array_push($server_rx,round($row['0'] / 1048576 / 60,2));
          array_push($server_tx,round($row['1'] / 1048576 / 60,2));
          array_push($server_time,date("'H:i'",$row['2']));
       }
       /* free result set */
       $result->close();
   }

   ?>
   <script>
   (function($) {

       $('.dropdown-submit-input .dropdown-menu a').click(function (e) {
         e.preventDefault();
         $(this).closest('.dropdown-submit-input').find('input').val($(this).data('value'));
         $(this).closest('form').submit();
       });

     })(jQuery);
     var chart = c3.generate({
       bindto: '#chart',
       data: {
         columns: [
             ['TX', <?php echo implode(',',$server_tx); ?>],
             ['RX', <?php echo implode(',',$server_rx); ?>]
         ],
         types: {
             TX: 'area',
             RX: 'area'
             // 'line', 'spline', 'step', 'area', 'area-step' are also available to stack
         },
         groups: [['RX' , 'TX']]
     },
     axis: {
       x: {
           type: 'category',
           categories: [<?php echo implode(',',$server_time); ?>]
       },
       y: {
           label: 'MB/s'
       },
     }
     });
   </script>
 <?php



}  elseif (strpos($page, 'hdd') !== false) {

$id = str_replace("dashboard?server=", "", $page);
$id = str_replace("?hdd", "", $id);
if(!preg_match("/^[0-9]+$/",$id)){ header('Location: index.php?page=dashboard'); }
if(!checkAccess($id,$USER_ID)) {  header('Location: index.php?page=dashboard'); }

?>
  <ul class="nav nav-tabs">
    <li><a href="index.php?page=dashboard">Servers</a></li>
    <li><a href="index.php?page=dashboard?server=<?= $id ?>">Overview</a></li>
    <li><a href="index.php?page=dashboard?server=<?= $id ?>?network">Network</a></li>
    <li><a href="index.php?page=dashboard?server=<?= $id ?>?cpu">CPU</a></li>
    <li><a href="index.php?page=dashboard?server=<?= $id ?>?memory">Memory</a></li>
    <li class="active"><a href="index.php?page=dashboard?server=<?= $id ?>?hdd">HDD</a></li>
    <li><a href="index.php?page=dashboard?server=<?= $id ?>?trigger">Trigger</a></li>
    <li><a href="index.php?page=logout">Logout</a></li>
  </ul>

  <?php

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $range = explode("-", $_POST['selector']);
      if (is_numeric($range['0']) AND is_numeric($range['1'])) {
        $data_start = $range['0'];
        $data_stop = $range['1'];
      } else {
        $data_start = time() - 1500;
        $data_stop = time();
      }
    } else {
      $data_start = time() - 1500;
      $data_stop = time();
    }
   ?>

  <div class="col-md-12 ct-chart"><center><h2>HDD Usage</h2></center>
    <form method="post">
      <div class="dropdown dropdown-submit-input">
        <input type="hidden" name="selector" />
        <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php echo date("d.m.Y H:i",$data_start).'-'.date("H:i",$data_stop); ?>
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu scrollable-menu" aria-labelledby="dropdownMenu1">
          <?php
          $query = "SELECT server_timestamp FROM servers_data WHERE server_id = ".$id." ORDER by id DESC";

          if ($result = $mysqli->query($query)) {

              $start="";
              $cycles=1;

              /* fetch object array */
              while ($row = $result->fetch_row()) {
                if ($cycles == 25) {
                  if ($start != "") {
                    echo '<li><a href="#" data-value="'.$row['0'].'-'.$start.'">'.date("d.m.Y H:i",$row['0']).'-'.date("H:i",$start).'</a></li>';
                  }
                  $start = $row['0'];
                  $cycles = 1;
                }
                $cycles++;
              }
              /* free result set */
              $result->close();
          }
          ?>
        </ul>
      </div>
    </form>
    <div id="chart"></div>
  </div>

  </div>

  <?php
  $query = "SELECT hdd_usage,hdd_total,server_timestamp FROM servers_data WHERE server_id = ".$id." AND server_timestamp >= ".$data_start." AND server_timestamp <= ".$data_stop."   ORDER by id";

  $hdd_usage = [];
  $hdd_total = [];
  $server_time = [];

  if ($result = $mysqli->query($query)) {

      /* fetch object array */
      while ($row = $result->fetch_row()) {
         array_push($hdd_usage,round($row['0'] / 1024 / 1024 / 1024,1));
         array_push($hdd_total,round($row['1'] / 1024 / 1024 / 1024,1));
         array_push($server_time,date("'H:i'",$row['2']));
      }
      /* free result set */
      $result->close();
  }

  ?>
  <script>
  (function($) {

      $('.dropdown-submit-input .dropdown-menu a').click(function (e) {
        e.preventDefault();
        $(this).closest('.dropdown-submit-input').find('input').val($(this).data('value'));
        $(this).closest('form').submit();
      });

    })(jQuery);
    var chart = c3.generate({
      bindto: '#chart',
      data: {
        columns: [
            ['Usage', <?php echo implode(',',$hdd_usage); ?>],
            ['Total', <?php echo implode(',',$hdd_total); ?>]
        ],
        types: {
            Usage: 'area',
            Total: 'area'
            // 'line', 'spline', 'step', 'area', 'area-step' are also available to stack
        },
    },
    axis: {
      x: {
          type: 'category',
          categories: [<?php echo implode(',',$server_time); ?>]
      },
      y: {
          label: 'GB'
      },
    }
    });
  </script>
<?php

} elseif (strpos($page, 'cpu') !== false) {

 $id = str_replace("dashboard?server=", "", $page);
 $id = str_replace("?cpu", "", $id);
 if(!preg_match("/^[0-9]+$/",$id)){ header('Location: index.php?page=dashboard'); }
 if(!checkAccess($id,$USER_ID)) {  header('Location: index.php?page=dashboard'); }


 ?>
   <ul class="nav nav-tabs">
     <li><a href="index.php?page=dashboard">Servers</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>">Overview</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?network">Network</a></li>
     <li class="active"><a href="index.php?page=dashboard?server=<?= $id ?>?cpu">CPU</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?memory">Memory</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?hdd">HDD</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?trigger">Trigger</a></li>
     <li><a href="index.php?page=logout">Logout</a></li>
   </ul>

   <?php

     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       $range = explode("-", $_POST['selector']);
       if (is_numeric($range['0']) AND is_numeric($range['1'])) {
         $data_start = $range['0'];
         $data_stop = $range['1'];
       } else {
         $data_start = time() - 1500;
         $data_stop = time();
       }
     } else {
       $data_start = time() - 1500;
       $data_stop = time();
     }
    ?>

   <div class="col-md-12 ct-chart"><center><h2>CPU Usage</h2></center>
     <form method="post">
       <div class="dropdown dropdown-submit-input">
         <input type="hidden" name="selector" />
         <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
           <?php echo date("d.m.Y H:i",$data_start).'-'.date("H:i",$data_stop); ?>
           <span class="caret"></span>
         </button>
         <ul class="dropdown-menu scrollable-menu" aria-labelledby="dropdownMenu1">
           <?php
           $query = "SELECT server_timestamp FROM servers_data WHERE server_id = ".$id." ORDER by id DESC";

           if ($result = $mysqli->query($query)) {

               $start="";
               $cycles=1;

               /* fetch object array */
               while ($row = $result->fetch_row()) {
                 if ($cycles == 25) {
                   if ($start != "") {
                     echo '<li><a href="#" data-value="'.$row['0'].'-'.$start.'">'.date("d.m.Y H:i",$row['0']).'-'.date("H:i",$start).'</a></li>';
                   }
                   $start = $row['0'];
                   $cycles = 1;
                 }
                 $cycles++;
               }
               /* free result set */
               $result->close();
           }
           ?>
         </ul>
       </div>
     </form>
     <div id="chart"></div>
   </div>

   </div>

   <?php
   $query = "SELECT cpu_load,io_wait,cpu_steal,server_timestamp FROM servers_data WHERE server_id = ".$id." AND server_timestamp >= ".$data_start." AND server_timestamp <= ".$data_stop."   ORDER by id";

   $cpu_usage = [];
   $io_wait = [];
   $cpu_steal = [];
   $server_time = [];

   if ($result = $mysqli->query($query)) {

       /* fetch object array */
       while ($row = $result->fetch_row()) {
          array_push($cpu_usage,round($row['0'],2));
          array_push($io_wait,round($row['1'],2));
          array_push($cpu_steal,round($row['2'],2));
          array_push($server_time,date("'H:i'",$row['3']));
       }
       /* free result set */
       $result->close();
   }

   ?>
   <script>
   (function($) {

       $('.dropdown-submit-input .dropdown-menu a').click(function (e) {
         e.preventDefault();
         $(this).closest('.dropdown-submit-input').find('input').val($(this).data('value'));
         $(this).closest('form').submit();
       });

     })(jQuery);
     var chart = c3.generate({
       bindto: '#chart',
       data: {
         columns: [
             ['Usage', <?php echo implode(',',$cpu_usage); ?>],
             ['Steal', <?php echo implode(',',$cpu_steal); ?>],
             ['I/O Wait', <?php echo implode(',',$io_wait); ?>]
         ],
         types: {
             Usage: 'area',
             Steal :'area',
             'I/O Wait' : 'area'

             // 'line', 'spline', 'step', 'area', 'area-step' are also available to stack
         },
     },
     size: {
       height: 200
     },
     axis: {
       x: {
           type: 'category',
           categories: [<?php echo implode(',',$server_time); ?>]
       },
       y: {
           label: '%'
       },
     }
     });
   </script>
 <?php

} elseif (strpos($page, 'memory') !== false) {


 $id = str_replace("dashboard?server=", "", $page);
 $id = str_replace("?memory", "", $id);
 if(!preg_match("/^[0-9]+$/",$id)){ header('Location: index.php?page=dashboard'); }
 if(!checkAccess($id,$USER_ID)) {  header('Location: index.php?page=dashboard'); }

 ?>
   <ul class="nav nav-tabs">
     <li><a href="index.php?page=dashboard">Servers</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>">Overview</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?network">Network</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?cpu">CPU</a></li>
     <li class="active"><a href="index.php?page=dashboard?server=<?= $id ?>?memory">Memory</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?hdd">HDD</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?trigger">Trigger</a></li>
     <li><a href="index.php?page=logout">Logout</a></li>
   </ul>

   <?php

     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       $range = explode("-", $_POST['selector']);
       if (is_numeric($range['0']) AND is_numeric($range['1'])) {
         $data_start = $range['0'];
         $data_stop = $range['1'];
       } else {
         $data_start = time() - 1500;
         $data_stop = time();
       }
     } else {
       $data_start = time() - 1500;
       $data_stop = time();
     }
    ?>

   <div class="col-md-12 ct-chart"><center><h2>Memory Usage</h2></center>
     <form method="post">
       <div class="dropdown dropdown-submit-input">
         <input type="hidden" name="selector" />
         <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
           <?php echo date("d.m.Y H:i",$data_start).'-'.date("H:i",$data_stop); ?>
           <span class="caret"></span>
         </button>
         <ul class="dropdown-menu scrollable-menu" aria-labelledby="dropdownMenu1">
           <?php
           $query = "SELECT server_timestamp FROM servers_data WHERE server_id = ".$id." ORDER by id DESC";

           if ($result = $mysqli->query($query)) {

               $start="";
               $cycles=1;

               /* fetch object array */
               while ($row = $result->fetch_row()) {
                 if ($cycles == 25) {
                   if ($start != "") {
                     echo '<li><a href="#" data-value="'.$row['0'].'-'.$start.'">'.date("d.m.Y H:i",$row['0']).'-'.date("H:i",$start).'</a></li>';
                   }
                   $start = $row['0'];
                   $cycles = 1;
                 }
                 $cycles++;
               }
               /* free result set */
               $result->close();
           }
           ?>
         </ul>
       </div>
     </form>
     <div id="chart"></div>
   </div>

   </div>

   <?php
   $query = "SELECT memory_total,memory_free,memory_cached,memory_buffer,memory_active,memory_inactive,server_timestamp FROM servers_data WHERE server_id = ".$id." AND server_timestamp >= ".$data_start." AND server_timestamp <= ".$data_stop."   ORDER by id";

   $memory_total = [];
   $memory_free = [];
   $memory_cached = [];
   $memory_buffer = [];
   $memory_inactive = [];
   $memory_active = [];
   $memory_used = [];
   $server_time = [];

   $cache_rx = 0; $cache_tx = 0;
   if ($result = $mysqli->query($query)) {

       /* fetch object array */
       while ($row = $result->fetch_row()) {
          array_push($memory_total, round($row['0'] / 1024,1));
          array_push($memory_free,round($row['1'] / 1024,1));
          array_push($memory_cached,round($row['2'] / 1024,1));
          array_push($memory_buffer,round($row['3'] / 1024,1));
          array_push($memory_active,round($row['4'] / 1024,1));
          array_push($memory_inactive,round($row['5'] / 1024,1));
          array_push($server_time,date("'H:i'",$row['6']));
          $used = $row['0']-($row['1']);
          array_push($memory_used, round($used / 1024,1));

       }
       /* free result set */
       $result->close();
   }

   ?>
   <script>
   (function($) {

       $('.dropdown-submit-input .dropdown-menu a').click(function (e) {
         e.preventDefault();
         $(this).closest('.dropdown-submit-input').find('input').val($(this).data('value'));
         $(this).closest('form').submit();
       });

     })(jQuery);
     var chart = c3.generate({
       bindto: '#chart',
       data: {
         columns: [
             ['Free', <?php echo implode(',',$memory_free); ?>],
             ['Cached', <?php echo implode(',',$memory_cached); ?>],
             ['Buffer', <?php echo implode(',',$memory_buffer); ?>],
             ['Used', <?php echo implode(',',$memory_used); ?>],
             ['Active', <?php echo implode(',',$memory_active); ?>],
             ['Inactive', <?php echo implode(',',$memory_inactive); ?>]
         ],
         types: {
             Free: 'line',
             Cached: 'line',
             Buffer: 'line',
             Used: 'line',
             Active: 'line',
             Inactive: 'line',
             // 'line', 'spline', 'step', 'area', 'area-step' are also available to stack
         },
     },
     axis: {
       x: {
           type: 'category',
           categories: [<?php echo implode(',',$server_time); ?>]
       },
       y: {
           label: 'MB'
       },
     }
     });
   </script>
 <?php

} else {

 $id = str_replace("dashboard?server=", "", $page);
 if(!preg_match("/^[0-9]+$/",$id)){ header('Location: index.php?page=dashboard'); }
 if(!checkAccess($id,$USER_ID)) {  header('Location: index.php?page=dashboard'); }

 $stmt = $mysqli->prepare("SELECT server_name,server_ip,server_uptime,server_kernel,server_cpu,server_cpu_cores,server_cpu_mhz FROM servers WHERE id = ? LIMIT 1");
 $stmt->bind_param('i', $id);
 $stmt->execute();
 $stmt->bind_result($server_name,$server_ip,$server_uptime,$server_kernel,$server_cpu,$server_cpu_cores,$server_cpu_mhz);
 $stmt->fetch();
 $stmt->close();

 ?>
 <meta http-equiv="refresh" content="60">
   <ul class="nav nav-tabs">
     <li><a href="index.php?page=dashboard">Servers</a></li>
     <li class="active"><a href="index.php?page=dashboard?server=<?= $id ?>">Overview</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?network">Network</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?cpu">CPU</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?memory">Memory</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?hdd">HDD</a></li>
     <li><a href="index.php?page=dashboard?server=<?= $id ?>?trigger">Trigger</a></li>
     <li><a href="index.php?page=logout">Logout</a></li>
   </ul>
   <div class="col-md-12">
     <h3><?= escape($server_name); ?></h3>
   </div>

   <div class="col-md-6">
     <p>Uptime: <?= escape(secondsToTime($server_uptime)); ?></p>
     <p>Kernel: <?= escape($server_kernel); ?></p>

   </div>
   <div class="col-md-6 text-left">
     <p>CPU Model: <?= escape($server_cpu); ?></p>
     <p>CPU Speed: <?= escape($server_cpu_cores); ?>x<?= escape($server_cpu_mhz); ?> Mhz</p>

   </div>
   <div class="col-md-12 ct-chart"><center><h2>Memory Usage</h2></center><div id="chart-memory"></div></div>

   <div class="col-md-12 ct-chart_cpu"><center><h2>CPU Usage</h2></center><div id="chart-cpu"></div></div>

   <div class="col-md-12 ct-chart_net"><center><h2>Network Usage</h2></center><div id="chart-net"></div></div>

   <div class="col-md-12 ct-chart_hdd"><center><h2>HDD Usage</h2></center><div id="chart-hdd"></div></div>

   <div class="row col-md-12">

   </div>

   <?php
   $atm = time() - 1300;
   $query = "SELECT memory_total,memory_free,memory_cached,memory_buffer,cpu_load,server_rx_diff,server_tx_diff,memory_active,memory_inactive,hdd_usage,hdd_total,cpu_steal,io_wait,server_timestamp FROM servers_data WHERE server_id = ".$id." AND server_timestamp >= ".$atm." ORDER by id";

   $memory_total = [];
   $memory_free = [];
   $memory_cached = [];
   $memory_buffer = [];
   $memory_inactive = [];
   $memory_active = [];
   $memory_used = [];
   $cpu_usage = [];
   $server_rx = [];
   $server_tx = [];
   $hdd_usage = [];
   $hdd_total = [];
   $cpu_steal = [];
   $io_wait = [];
   $server_time = array();

   $cache_rx = 0; $cache_tx = 0;
   if ($result = $mysqli->query($query)) {

       /* fetch object array */
       while ($row = $result->fetch_row()) {
          array_push($memory_total, round($row['0'] / 1024,1));
          array_push($memory_free,round($row['1'] / 1024,1));
          array_push($memory_cached,round($row['2'] / 1024,1));
          array_push($memory_buffer,round($row['3'] / 1024,1));
          array_push($cpu_usage,round($row['4'],2));
          array_push($server_rx,round($row['5'] / 1048576 / 60,2));
          array_push($server_tx,round($row['6'] / 1048576 / 60,2));
          array_push($memory_active,round($row['7'] / 1024,1));
          array_push($memory_inactive,round($row['8'] / 1024,1));
          array_push($hdd_usage,round($row['9']  / 1024 / 1024 / 1024,1));
          array_push($hdd_total,round($row['10']  / 1024 / 1024 / 1024,1));
          array_push($cpu_steal,round($row['11'],2));
          array_push($io_wait,round($row['12'],2));
          array_push($server_time,date("'H:i'",$row['13']));
          $used = $row['0']-($row['1']);
          array_push($memory_used, round($used / 1024,1));

       }
       /* free result set */
       $result->close();
   }

   ?>
   <script>
   var chart = c3.generate({
     bindto: '#chart-memory',
     data: {
       columns: [
           ['Free', <?php echo implode(',',$memory_free); ?>],
           ['Cached', <?php echo implode(',',$memory_cached); ?>],
           ['Buffer', <?php echo implode(',',$memory_buffer); ?>],
           ['Used', <?php echo implode(',',$memory_used); ?>],
           ['Active', <?php echo implode(',',$memory_active); ?>],
           ['Inactive', <?php echo implode(',',$memory_inactive); ?>]
       ],
       types: {
           Free: 'line',
           Cached: 'line',
           Buffer: 'line',
           Used: 'line',
           Active: 'line',
           Inactive: 'line',
           // 'line', 'spline', 'step', 'area', 'area-step' are also available to stack
       },
   },
   size: {
     height: 200
   },
   axis: {
     x: {
         type: 'category',
         categories: [<?php echo implode(',',$server_time); ?>]
     },
     y: {
         label: 'MB'
     },
   }
   });
   var chart = c3.generate({
     bindto: '#chart-cpu',
     data: {
       columns: [
           ['Usage', <?php echo implode(',',$cpu_usage); ?>],
           ['Steal', <?php echo implode(',',$cpu_steal); ?>],
           ['I/O Wait', <?php echo implode(',',$io_wait); ?>]
       ],
       types: {
           Usage: 'area',
           Steal :'area',
           'I/O Wait' : 'area'

           // 'line', 'spline', 'step', 'area', 'area-step' are also available to stack
       },
   },
   size: {
     height: 200
   },
   axis: {
     x: {
         type: 'category',
         categories: [<?php echo implode(',',$server_time); ?>]
     },
     y: {
         label: '%'
     },
   }
   });
   var chart = c3.generate({
     bindto: '#chart-net',
     data: {
       columns: [
           ['TX', <?php echo implode(',',$server_tx); ?>],
           ['RX', <?php echo implode(',',$server_rx); ?>]
       ],
       types: {
           TX: 'area',
           RX: 'area'
           // 'line', 'spline', 'step', 'area', 'area-step' are also available to stack
       },
       groups: [['RX' , 'TX']]
   },
   size: {
     height: 200
   },
   axis: {
     x: {
         type: 'category',
         categories: [<?php echo implode(',',$server_time); ?>]
     },
     y: {
         label: 'MB/s'
     },
   }
   });
   var chart = c3.generate({
     bindto: '#chart-hdd',
     data: {
       columns: [
           ['Usage', <?php echo implode(',',$hdd_usage); ?>],
           ['Total', <?php echo implode(',',$hdd_total); ?>]
       ],
       types: {
           Usage: 'area',
           Total: 'area'
           // 'line', 'spline', 'step', 'area', 'area-step' are also available to stack
       },
   },
   size: {
     height: 200
   },
   axis: {
     x: {
         type: 'category',
         categories: [<?php echo implode(',',$server_time); ?>]
     },
     y: {
         label: 'GB'
     },
   }
   });
   </script>
 </div>
 <?php

}

 ?>
