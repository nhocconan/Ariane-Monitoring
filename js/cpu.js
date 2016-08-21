var chart = c3.generate({
  bindto: '#chart-cpu',
  data: {
    columns: [
        ['User', ...cpu_load],
        ['System', ...cpu_load_sys],
        ['Steal', ...cpu_steal],
        ['I/O Wait', ...io_wait]
    ],
    types: {
        User: 'area',
        System: 'area',
        Steal :'area',
        'I/O Wait' : 'area'
    },
    groups: [['User' , 'System']]
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
