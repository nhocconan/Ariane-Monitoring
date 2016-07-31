var chart = c3.generate({
  bindto: '#chart-hdd',
  data: {
    columns: [
        ['Usage', ...hdd_usage],
        ['Total', ...hdd_total]
    ],
    types: {
        Usage: 'area',
        Total: 'area'
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
      label: 'GB'
  },
 }
});
