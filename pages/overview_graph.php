<?php

$server_ids = array();

$query = "SELECT id,server_name FROM servers WHERE user_id = ?";
$stmt = $database->prepare($query);
$stmt->bind_param('i', $USER_ID);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
  $data = generateBacon($row['id'],2);
  $server_ids[$row['server_name']] = $data;
}

 $last_key = "";
 ?>
 <meta http-equiv="refresh" content="60">
 <div class="container base-box">

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
