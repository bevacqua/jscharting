fetch('/tweets-about-pope')
.then(response => response.json())
.then(tweets => {
  var options = {
    targetElement: 'chart',
    type: 'map',
    titleLabelText: 'Tweets About the Pope in Washington and Surrounding Areas',
    legendPosition: 'CA:4,4',
    series: [{
      map: 'us.region:Northeast', name: 'US north-east'
    }, {
      map: 'us.region:South', name: 'US south'
    }, {
      type: 'marker',
      name: 'Tweet',
      defaultPointTooltip: '%text <br/>— <em>%when</em> by <strong>@%username</strong>',
      points: tweets.map(tweet => ({
        y: tweet.geo.coordinates[0],
        x: tweet.geo.coordinates[1],
        attributes: {
          when: moment(new Date(tweet.created_at)).fromNow(),
          text: tweet.text,
          username: tweet.user.screen_name
        }
      }))
    }]
  }
  var chart = new JSC.Chart(options)
})
