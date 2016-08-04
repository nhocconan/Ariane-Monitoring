<?php

$id = str_replace("dashboard?server=", "", $page);
$id = str_replace("?network", "", $id);
$id = str_replace("?cpu", "", $id);
$id = str_replace("?memory", "", $id);
$id = str_replace("?hdd", "", $id);
$id = str_replace("?trigger", "", $id);

if(!preg_match("/^[0-9]+$/",$id)){ header('Location: index.php?page=dashboard'); }
if(!checkAccess($id,$USER_ID)) {  header('Location: index.php?page=dashboard'); }

 ?>

<nav class="navbar navbar-default navbar-fixed-top navbar-opacity">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php?page=dashboard">Ariane</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="index.php?page=dashboard">Servers</a></li>
            <li><a href="index.php?page=dashboard?server=<?= $id ?>">Overview</a></li>
            <?php if (strpos($page, 'network') !== false) {
                  $title = '<div class="col-md-12 ct-chart"><center><h2>Network Usage</h2></center>';
                  $navbar_elments .= '<li class="active"><a href="index.php?page=dashboard?server='.$id.'?network">Network</a></li>';
            } else {
                  $navbar_elments .= '<li><a href="index.php?page=dashboard?server='.$id.'?network">Network</a></li>';
            }
             if (strpos($page, 'cpu') !== false) {
                  $title = '<div class="col-md-12 ct-chart"><center><h2>CPU Usage</h2></center>';
                  $navbar_elments .= '<li class="active"><a href="index.php?page=dashboard?server='.$id.'?cpu">CPU</a></li>';
            } else {
                  $navbar_elments .= '<li><a href="index.php?page=dashboard?server='.$id.'?cpu">CPU</a></li>';
            }
            if (strpos($page, 'memory') !== false) {
                  $title = '<div class="col-md-12 ct-chart"><center><h2>Memory Usage</h2></center>';
                  $navbar_elments .= '<li class="active"><a href="index.php?page=dashboard?server='.$id.'?memory">Memory</a></li>';
            } else {
                  $navbar_elments .= '<li><a href="index.php?page=dashboard?server='.$id.'?memory">Memory</a></li>';
            }
             if (strpos($page, 'hdd') !== false) {
                  $title = '<div class="col-md-12 ct-chart"><center><h2>HDD Usage</h2></center>';
                  $navbar_elments .= '<li class="active"><a href="index.php?page=dashboard?server='.$id.'?hdd">HDD</a></li>';
            } else {
                  $navbar_elments .= '<li><a href="index.php?page=dashboard?server='.$id.'?hdd">HDD</a></li>';
            }
             if (strpos($page, 'trigger') !== false) {
                  $navbar_elments .= '<li class="active"><a href="index.php?page=dashboard?server='.$id.'?trigger">Trigger</a></li>';
            }  else {
                  $navbar_elments .= '<li><a href="index.php?page=dashboard?server='.$id.'?trigger">Trigger</a></li>';
            }
              echo $navbar_elments;
              ?>
            <li><a href="index.php?page=logout">Logout</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

<div class="container base-box">

  <?php

   if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     $range = explode("-", $_POST['selector']);
     if (is_numeric($range['0']) AND is_numeric($range['1'])) {
       $data_start = $range['0'];
       $data_stop = $range['1'];
     } else {
       $data_start = time() - 6000;
       $data_stop = time();
     }
   } else {
     $data_start = time() - 6000;
     $data_stop = time();
   }

   $navbar_elments = "";

   ?>


<?= $title ?>
<?php if (strpos($page, 'trigger') !== false) {

 ?>

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

      $stmt = $database->prepare("UPDATE servers SET cpu_alert = ? ,cpu_steal_alert = ? ,io_wait_alert = ? , reboot_alert = ?, offline_alert = ? WHERE id = ?");
      $stmt->bind_param('iiiiii',$load,$steal,$io,$reboot,$offline,$id);
      $stmt->execute();
      $stmt->close();

    }
   }
  }

  $stmt = $database->prepare("SELECT cpu_alert,cpu_steal_alert,io_wait_alert,reboot_alert,offline_alert  FROM servers WHERE id = ? LIMIT 1");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $stmt->bind_result($db_cpu,$db_steal,$db_wait,$db_reboot,$db_offline_alert);
  $stmt->fetch();
  $stmt->close();


  ?>
        <form class="form-horizontal" role="form" action="index.php?page=dashboard?server=<?= escape($id) ?>?trigger" method="post">
          <div class="col-sm-12 space-top">
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
         </div>
          <div class="col-sm-2">
            <h3>Trigger - CPU</h3>
           <div class="form-group">
             <div class="col-xs-10">
             <label class="" for="email">CPU Load %</label>
             <input type="text" name="load" class="form-control" value="<?= escape($db_cpu); ?>">
              </div>
           </div>
           <div class="form-group">
             <div class="col-xs-10">
             <label class="" for="pwd">CPU Steal %</label>
             <input type="text" name="steal" class="form-control" value="<?= escape($db_steal); ?>">
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
             <input type="text" name="io" class="form-control" value="<?= escape($db_wait); ?>">
              </div>
           </div>
           </div>
           <div class="col-sm-4">
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

<?php

} elseif (strpos($page, 'network') !== false OR strpos($page, 'cpu') !== false OR strpos($page, 'memory') !== false OR strpos($page, 'hdd') !== false) {

  ?>

  <form method="post">
    <div class="dropdown dropdown-submit-input">
      <input type="hidden" name="selector" />
      <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <?php echo escape(date("d.m.Y H:i",$data_start)).'-'.escape(date("H:i",$data_stop)); ?>
        <span class="caret"></span>
      </button>
      <ul class="dropdown-menu scrollable-menu" aria-labelledby="dropdownMenu1">
        <?php
        $query = "SELECT server_timestamp FROM servers_data WHERE server_id = ".$id." ORDER by id DESC";

        if ($result = $database->query($query)) {

            $start="";
            $cycles=1;

            /* fetch object array */
            while ($row = $result->fetch_row()) {
              if ($cycles == 60) {
                if ($start != "") {
                  echo '<li><a href="#" data-value="'.escape($row['0']).'-'.escape($start).'">'.escape(date("d.m.Y H:i",$row['0'])).'-'.escape(date("H:i",$start)).'</a></li>';
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

  <?php

if (strpos($page, 'network') !== false) {

    ?>

     <div id="chart-net"></div>

   <?php $Bacon = generateBacon($id,0,$data_start,$data_stop); ?>

   <script>
   (function($) {

       $('.dropdown-submit-input .dropdown-menu a').click(function (e) {
         e.preventDefault();
         $(this).closest('.dropdown-submit-input').find('input').val($(this).data('value'));
         $(this).closest('form').submit();
       });

     })(jQuery);
     var server_timestamp = [<?php echo implode(',',$Bacon['server_timestamp']); ?>];
     var server_tx_diff = <?php echo json_encode($Bacon['server_tx_diff']); ?>;
     var server_rx_diff = <?php echo json_encode($Bacon['server_rx_diff']); ?>;
   </script>
   <script src="../js/net.js"></script>
 <?php



}  elseif (strpos($page, 'hdd') !== false) {

   ?>
    <div id="chart-hdd"></div>

   <?php $Bacon = generateBacon($id,0,$data_start,$data_stop); ?>

  <script>
  (function($) {

      $('.dropdown-submit-input .dropdown-menu a').click(function (e) {
        e.preventDefault();
        $(this).closest('.dropdown-submit-input').find('input').val($(this).data('value'));
        $(this).closest('form').submit();
      });

    })(jQuery);
    var server_timestamp = [<?php echo implode(',',$Bacon['server_timestamp']); ?>];
    var hdd_usage = <?php echo json_encode($Bacon['hdd_usage']); ?>;
    var hdd_total = <?php echo json_encode($Bacon['hdd_total']); ?>;
  </script>
  <script src="../js/hdd.js"></script>
<?php

} elseif (strpos($page, 'cpu') !== false) {

    ?>
     <div id="chart-cpu"></div>

   <?php $Bacon = generateBacon($id,0,$data_start,$data_stop); ?>

   <script>
   (function($) {

       $('.dropdown-submit-input .dropdown-menu a').click(function (e) {
         e.preventDefault();
         $(this).closest('.dropdown-submit-input').find('input').val($(this).data('value'));
         $(this).closest('form').submit();
       });

     })(jQuery);
     var server_timestamp = [<?php echo implode(',',$Bacon['server_timestamp']); ?>];
     var cpu_load = <?php echo json_encode($Bacon['cpu_load']); ?>;
     var cpu_steal = <?php echo json_encode($Bacon['cpu_steal']); ?>;
     var io_wait = <?php echo json_encode($Bacon['io_wait']); ?>;
   </script>
   <script src="../js/cpu.js"></script>
 <?php

} elseif (strpos($page, 'memory') !== false) {

    ?>
     <div id="chart-memory"></div>

   <?php $Bacon = generateBacon($id,0,$data_start,$data_stop); ?>

   <script>
   (function($) {

       $('.dropdown-submit-input .dropdown-menu a').click(function (e) {
         e.preventDefault();
         $(this).closest('.dropdown-submit-input').find('input').val($(this).data('value'));
         $(this).closest('form').submit();
       });

     })(jQuery);
     var server_timestamp = [<?php echo implode(',',$Bacon['server_timestamp']); ?>];
     var memory_free = <?php echo json_encode($Bacon['memory_free']); ?>;
     var memory_cached = <?php echo json_encode($Bacon['memory_cached']); ?>;
     var memory_buffer = <?php echo json_encode($Bacon['memory_buffer']); ?>;
     var memory_used = <?php echo json_encode($Bacon['memory_used']); ?>;
     var memory_active = <?php echo json_encode($Bacon['memory_active']); ?>;
     var memory_inactive = <?php echo json_encode($Bacon['memory_inactive']); ?>;
   </script>
   <script src="../js/mem.js"></script>
 <?php

}

} else {

 $timeframe = 2;

if (isset($_POST['timeframe'])) {
  if (is_numeric($_POST['timeframe'])) {
    $timeframe = $_POST['timeframe'];
  }
}

 $stmt = $database->prepare("SELECT server_name,server_ip,server_uptime,server_kernel,server_cpu,server_cpu_cores,server_cpu_mhz FROM servers WHERE id = ? LIMIT 1");
 $stmt->bind_param('i', $id);
 $stmt->execute();
 $stmt->bind_result($server_name,$server_ip,$server_uptime,$server_kernel,$server_cpu,$server_cpu_cores,$server_cpu_mhz);
 $stmt->fetch();
 $stmt->close();

 ?>
 <meta http-equiv="refresh" content="60">
   <div class="col-md-12">
     <h3><?= escape($server_name); ?></h3>

   <div class="col-md-6">
     <p>Uptime: <?= escape(secondsToTime($server_uptime)); ?></p>
     <p>Kernel: <?= escape($server_kernel); ?></p>
   </div>

   <div class="col-md-6 text-left">
     <p>CPU Model: <?= escape($server_cpu); ?></p>
     <p>CPU Speed: <?= escape($server_cpu_cores); ?>x<?= escape($server_cpu_mhz); ?> Mhz</p>
   </div>
  </div>

  <form method="post" id="selector">
     <div class="btn-group pull-right" role="group">
     <?php
     if ($timeframe == 1) {
       echo '<button type="submit" class="btn btn-primary" name="timeframe" value="1">1h</button>
             <button type="submit" class="btn btn-default" name="timeframe" value="2">2h</button>
             <button type="submit" class="btn btn-default" name="timeframe" value="4">4h</button>
             <button type="submit" class="btn btn-default" name="timeframe" value="12">12h</button>
             <button type="submit" class="btn btn-default" name="timeframe" value="24">24h</button>';
    } elseif ($timeframe == 2) {
      echo '<button type="submit" class="btn btn-default" name="timeframe" value="1">1h</button>
            <button type="submit" class="btn btn-primary" name="timeframe" value="2">2h</button>
            <button type="submit" class="btn btn-default" name="timeframe" value="4">4h</button>
            <button type="submit" class="btn btn-default" name="timeframe" value="12">12h</button>
            <button type="submit" class="btn btn-default" name="timeframe" value="24">24h</button>';
    } elseif ($timeframe == 4) {
      echo '<button type="submit" class="btn btn-default" name="timeframe" value="1">1h</button>
            <button type="submit" class="btn btn-default" name="timeframe" value="2">2h</button>
            <button type="submit" class="btn btn-primary" name="timeframe" value="4">4h</button>
            <button type="submit" class="btn btn-default" name="timeframe" value="12">12h</button>
            <button type="submit" class="btn btn-default" name="timeframe" value="24">24h</button>';
    } elseif ($timeframe == 12) {
      echo '<button type="submit" class="btn btn-default" name="timeframe" value="1">1h</button>
            <button type="submit" class="btn btn-default" name="timeframe" value="2">2h</button>
            <button type="submit" class="btn btn-default" name="timeframe" value="4">4h</button>
            <button type="submit" class="btn btn-primary" name="timeframe" value="12">12h</button>
            <button type="submit" class="btn btn-default" name="timeframe" value="24">24h</button>';
    } elseif ($timeframe == 24) {
      echo '<button type="submit" class="btn btn-default" name="timeframe" value="1">1h</button>
            <button type="submit" class="btn btn-default" name="timeframe" value="2">2h</button>
            <button type="submit" class="btn btn-default" name="timeframe" value="4">4h</button>
            <button type="submit" class="btn btn-default" name="timeframe" value="12">12h</button>
            <button type="submit" class="btn btn-primary" name="timeframe" value="24">24h</button>';
     } else {
       echo '<button type="submit" class="btn btn-default" name="timeframe" value="1">1h</button>
             <button type="submit" class="btn btn-default" name="timeframe" value="2">2h</button>
             <button type="submit" class="btn btn-default" name="timeframe" value="4">4h</button>
             <button type="submit" class="btn btn-default" name="timeframe" value="12">12h</button>
             <button type="submit" class="btn btn-default" name="timeframe" value="24">24h</button>';
         }
      ?>
    </div>
  </form>

   <div class="col-md-12 ct-chart"><center><h2>Memory Usage</h2></center><div id="chart-memory"></div></div>

   <div class="col-md-12 ct-chart_cpu"><center><h2>CPU Usage</h2></center><div id="chart-cpu"></div></div>

   <div class="col-md-12 ct-chart_net"><center><h2>Network Usage</h2></center><div id="chart-net"></div></div>

   <div class="col-md-12 ct-chart_hdd"><center><h2>HDD Usage</h2></center><div id="chart-hdd"></div></div>

   <?php $Bacon = generateBacon($id,$timeframe); ?>

   <script>
   var server_timestamp = [<?php echo implode(',',$Bacon['server_timestamp']); ?>];
   var memory_free = <?php echo json_encode($Bacon['memory_free']); ?>;
   var memory_cached = <?php echo json_encode($Bacon['memory_cached']); ?>;
   var memory_buffer = <?php echo json_encode($Bacon['memory_buffer']); ?>;
   var memory_used = <?php echo json_encode($Bacon['memory_used']); ?>;
   var memory_active = <?php echo json_encode($Bacon['memory_active']); ?>;
   var memory_inactive = <?php echo json_encode($Bacon['memory_inactive']); ?>;
   var cpu_load = <?php echo json_encode($Bacon['cpu_load']); ?>;
   var cpu_steal = <?php echo json_encode($Bacon['cpu_steal']); ?>;
   var io_wait = <?php echo json_encode($Bacon['io_wait']); ?>;
   var hdd_usage = <?php echo json_encode($Bacon['hdd_usage']); ?>;
   var hdd_total = <?php echo json_encode($Bacon['hdd_total']); ?>;
   var server_tx_diff = <?php echo json_encode($Bacon['server_tx_diff']); ?>;
   var server_rx_diff = <?php echo json_encode($Bacon['server_rx_diff']); ?>;
   </script>
   <script src="../js/mem.js"></script>
   <script src="../js/cpu.js"></script>
   <script src="../js/net.js"></script>
   <script src="../js/hdd.js"></script>
 <?php

}

 ?>
</div>
