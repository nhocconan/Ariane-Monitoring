<?php

$server_ids = array();

$in = 0;
$out = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['timestamp'])) {
    if (is_numeric($_POST['timestamp'])) {
      $in = strtotime('-1 hour', $_POST['timestamp']);
      $out = strtotime('+1 hour', $_POST['timestamp']);
      $_SESSION['timestamp_overview'] = $_POST['timestamp'];
    }
  }
}

$query = "SELECT id,server_name FROM servers WHERE user_id = ?";
$stmt = $database->prepare($query);
$stmt->bind_param('i', $USER_ID);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
  if ($in != 0 AND $out != 0) {
    $data = generateBacon($row['id'],0,$in,$out);
  } elseif (!empty($_SESSION['timestamp_overview'])) {
    $in = strtotime('-1 hour', $_SESSION['timestamp_overview']);
    $out = strtotime('+1 hour', $_SESSION['timestamp_overview']);
    $data = generateBacon($row['id'],0,$in,$out);
  } else {
    $data = generateBacon($row['id'],2);
  }
  if (!empty($data)) {
      $server_ids[$row['server_name']] = $data;
  }
}

 $last_key = "";
 ?>
 <meta http-equiv="refresh" content="60">
 <div class="container base-box">

       <div class="container space-top">
        <div class="row">
            <div class='col-sm-3'>
              <form method="POST" action="index.php?page=overview" id="timestamp">
               <input type="hidden" name="timestamp" id="timestamp_box" value="" />
                  <div class="form-group">
                      <div class='input-group date' id='datetimepicker'>
                          <input type='text' class="form-control" />
                          <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                          <span class="input-group-btn">
                               <button class="btn btn-default" type="button" id="timestamp_submit">Go!</button>
                          </span>
                      </div>
                  </div>
                </form>
            </div>
            <script type="text/javascript">
                $(function () {
                    $('#datetimepicker').datetimepicker({
                       defaultDate: new Date(<?php $time = (empty($_SESSION['timestamp_overview']) ? time() : $_SESSION['timestamp_overview']); echo escape($time); ?>*1000),
                       format: 'DD/MM/YYYY HH:mm',
                    });
                });

                $('#datetimepicker').on('dp.change', function (e) {
                document.getElementById('timestamp_box').value = e.date.unix();
                });

                var form = document.getElementById("timestamp");
                document.getElementById("timestamp_submit").addEventListener("click", function () {
                    form.submit();
                });
            </script>
        </div>
    </div>

    <center><h3>CPU Load</h3></center>
    <div id="chart-cpu"></div>

    <center><h3>Network Inbound</h3></center>
    <div id="chart-network-in"></div>

    <center><h3>Network Outbound</h3></center>
    <div id="chart-network-out"></div>

    <script>
    var chart = c3.generate({
      bindto: '#chart-cpu',
      data: {
        columns: [
            <?php
            foreach ($server_ids as $key => $row) {
                $last_key = $key;
                echo "['".$key."', ".implode(',',$row['cpu_load'])."],";
            }
             ?>
        ],
    },
    point: {
         show: false
     },
    size: {
      height: 200
    },
    axis: {
      x: {
            type: 'category',
            categories: [<?php echo implode(',',$server_ids[$key]['server_timestamp']); ?>],
            tick: {
            width: 80,
                culling: {
                    max: 7
                }
              }
          },
      y: {
          label: '%'
      },
    }
    });
    </script>

    <script>
    var chart = c3.generate({
      bindto: '#chart-network-in',
      data: {
        columns: [
            <?php
            foreach ($server_ids as $key => $row) {
                echo "['".$key."', ".implode(',',$row['server_rx_diff'])."],";
            } ?>
        ],
        types: {
            <?php
            foreach ($server_ids as $key => $row) {
                echo $key.": 'area',";
            } ?>
        },
        groups: [[
          <?php
          foreach ($server_ids as $key => $row) {
              echo "'".$key."'".',';
          } ?>
        ]]
    },
    point: {
         show: false
     },
    size: {
      height: 200
    },
    axis: {
      x: {
            type: 'category',
            categories: [<?php echo implode(',',$server_ids[$key]['server_timestamp']); ?>],
            tick: {
            width: 80,
                culling: {
                    max: 7
                }
              }
          },
      y: {
          label: 'MB/s'
      },
    }
    });
    </script>

    <script>
    var chart = c3.generate({
      bindto: '#chart-network-out',
      data: {
        columns: [
            <?php
            foreach ($server_ids as $key => $row) {
                echo "['".$key."', ".implode(',',$row['server_tx_diff'])."],";
            } ?>
        ],
        types: {
            <?php
            foreach ($server_ids as $key => $row) {
                echo $key.": 'area',";
            } ?>
        },
        groups: [[
          <?php
          foreach ($server_ids as $key => $row) {
              echo "'".$key."'".',';
          } ?>
        ]]
    },
    point: {
         show: false
     },
    size: {
      height: 200
    },
    axis: {
      x: {
            type: 'category',
            categories: [<?php echo implode(',',$server_ids[$key]['server_timestamp']); ?>],
            tick: {
            width: 80,
                culling: {
                    max: 7
                }
              }
          },
      y: {
          label: 'MB/s'
      },
    }
    });
    </script>
</div>
