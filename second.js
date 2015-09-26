var repo = 'nodejs/node'

fetch(`https://api.github.com/repos/${repo}/stats/commit_activity`)
  .then(response => response.json())
  .then(weeks => weeks
    .map((week, i) => ({
      x: moment().subtract(52 - i, 'weeks').toDate(),
      y: [
        Math.min(...week.days),
        Math.max(...week.days)
      ]
    }))
  )
  .catch(error => [])
  .then(points => {
    var options = {
      targetElement: 'chart',
      type: 'areaSpline',
      legendPosition: 'CA:4,4',
      titleLabelText: `
        Weekly GitHub Contribution Activity on ${repo} repo.
        Range: %min to %max commits, Average: %average commits`,
      titlePosition: 'full',
      xAxis: {
        formatString: 'MMM dd',
        scaleIntervalUnit: 'week'
      },
      yAxis: {
        labelText: 'Commits',
        scaleRangeMin: 0,
        markers: [{
          value: [0, 5],
          labelText: 'Infrequent Commits',
          labelAlign: 'center',
          color: ['#fcc348', 0.6]
        }]
      },
      defaultPointTooltip: `
        <b>Week of %xValue</b>
        <br/>High: <b>%yValue Commits</b>
        <br/>Low: <b>%yStart Commits</b>`,
      series: [{
        name: 'Daily Contributions',
        points: points
      }]
    }
    var chart = new JSC.Chart(options)
  })
