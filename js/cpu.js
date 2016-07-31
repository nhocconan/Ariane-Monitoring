var chart = c3.generate({
  bindto: '#chart-cpu',
  data: {
    columns: [
        ['Usage', ...cpu_load],
        ['Steal', ...cpu_steal],
        ['I/O Wait', ...io_wait]
    ],
    types: {
        Usage: 'area',
        Steal :'area',
        'I/O Wait' : 'area'
    },
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
      label: '%'
  },
}
});
