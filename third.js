Promise
  .all([
    'nodejs/node',
    'lodash/lodash',
    'facebook/react',
    'angular/angular'
  ].map(pull))
  .then(comparisonChart)

function pull (repo) {
  return fetch(`https://api.github.com/repos/${repo}/stats/commit_activity`)
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
    .then(points => ({
      name: repo,
      points: points
    }))
}

function comparisonChart (repositories) {
  var options = {
    targetElement: 'chart',
    type: 'areaSpline',
    legendPosition: 'CA:4,4',
    titleLabelText: 'Weekly GitHub Contribution Activity Comparison',
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
    series: repositories.map(repo => ({
      name: `Contributions to ${repo.name}`,
      points: repo.points
    }))
  }
  var chart = new JSC.Chart(options)
}
