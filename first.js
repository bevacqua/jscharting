Promise
  .all(['bevacqua', 'substack', 'addyosmani', 'sindresorhus'].map(pull))
  .then(lines => {
    var options = {
      targetElement: 'chart',
      series: lines
    }
    var chart = new JSC.Chart(options)
  })

function pull (username) {
  return fetch(`https://api.github.com/users/${username}/events/public`)
    .then(response => response.json())
    .then(events => events
      .filter(event => event.type === 'PushEvent')
      .reduce(merge, {})
    )
    .catch(error => ({}))
    .then(counts => Object
      .keys(counts)
      .map(date => [new Date(date), counts[date]])
    )
    .then(points => ({ name: '@' + username, points }))
}

function merge (counts, push) {
  var date = push.created_at.slice(0, 10)
  if (date in counts) {
    counts[date]++
  } else {
    counts[date] = 1
  }
  return counts
}
