import express from 'express'
import serveStatic from 'serve-static'

var app = express()
var port = process.env.PORT || 9000

app.use(serveStatic('./'))
app.get('/tweets-about-pope', queried)
app.listen(port, () => console.log('app listening on port ' + port))

import Twit from 'twit'
import {parse as querystring} from 'omnibox/querystring'

function queried (req, res, next) {
  tweetsAboutPope(function gotTweets (err, tweets) {
    if (err) {
      res.status(500).json(err.message)
    } else {
      res.json(tweets)
    }
  })
}

function tweetsAboutPope (done) {
  // note: never expose your application secrets like this. this is only for demo purposes
  var t = new Twit({
    consumer_key:        '5XXdudRSGuZWoy3i9fDwNB8OQ',
    consumer_secret:     'UF8oIi531IopXZnrfvEMh34ez6DxzIaOZI8hWhWa208j6tjQeM',
    access_token:        '329661096-zxVpzWt8fngW51j0VIgx3WBSWp4AUFrZu157Slzj',
    access_token_secret: 'dpI3VJFUm3wGQNdmcuqnrdjjwfS188hcq1ppeRQtRApjO'
  })

  var pages = 0
  var query = 'pope francis washington'
  var geocode = '38.6743001,-76.2242026,500km' // Washington, DC and surrounding areas

  more()

  function more (metadata = { next_results: '' }) {
    var qs = querystring(metadata.next_results.slice(1))
    var parameters = {
      geocode,
      q: query,
      count: 100,
      max_id: qs.max_id // pick up where the last page left off
    }
    t.get('search/tweets', parameters, pager)
  }

  function pager (err, payload) {
    if (err) {
      done(err)
      return
    }
    // even when you asked for geolocated tweets, not every tweet has geolocation data
    tweets.push(...payload.statuses.filter(status => status.geo))
    if (payload.search_metadata.next_results && pages++ < 10) {
      more(payload.search_metadata) // pull a few pages worth of tweets
    } else {
      done(null, tweets)
    }
  }
}
