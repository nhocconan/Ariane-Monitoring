var chart = c3.generate({
  bindto: '#chart-net',
  data: {
    columns: [
        ['TX', ...server_tx_diff],
        ['RX', ...server_rx_diff]
    ],
    types: {
        TX: 'area',
        RX: 'area'
    },
    groups: [['RX' , 'TX']]
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
        categories: [...server_timestamp],
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
