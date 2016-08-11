var chart = c3.generate({
  bindto: '#chart-memory-free',
  data: {
    columns: [
        ['Free', ...memory_free_total],
    ],
    types: {
        Free: 'line',
    },
},
size: {
  height: 200
},
point: {
     show: false
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
      label: 'MB'
  },
}
});
