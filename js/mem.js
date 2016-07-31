var chart = c3.generate({
  bindto: '#chart-memory',
  data: {
    columns: [
        ['Free', ...memory_free],
        ['Cached', ...memory_cached],
        ['Buffer', ...memory_buffer],
        ['Used', ...memory_used],
        ['Active', ...memory_active],
        ['Inactive', ...memory_inactive]
    ],
    types: {
        Free: 'line',
        Cached: 'line',
        Buffer: 'line',
        Used: 'line',
        Active: 'line',
        Inactive: 'line',
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
