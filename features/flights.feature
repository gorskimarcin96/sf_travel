Feature: Searcher
  In order to get flights
  As a user
  I need to use data in display got data

  Background:
    Given the database is clean
    And there are searches
      | id | nation | place     | from       | to         | created_at |
      | 1  | grecja | zakynthos | 2000-01-01 | 2000-01-07 | 2000-01-01 |
      | 2  | grecja | rodos     | 2000-01-01 | 2000-01-07 | 2000-01-01 |
    And there are flights
      | id | price   | from_airport | from_start       | from_end         | from_stops | to_airport | to_start         | to_end           | to_stops | url                 | source | search_id |
      | 1  | 500     | WAW          | 2000-01-01 10:00 | 2000-01-01 12:00 | 0          | ZTH        | 2000-01-07 10:00 | 2000-01-07 12:00 | 0        | https://example.org | xyz    | 1         |
      | 2  | 2999.99 | WAW          | 2000-01-01 14:00 | 2000-01-01 16:00 | 1          | ZTH        | 2000-01-07 14:00 | 2000-01-07 16:00 | 0        | https://example.org | abc    | 1         |
      | 3  | 2.30    | WAW          | 2000-01-01 18:00 | 2000-01-01 20:00 | 0          | ZTH        | 2000-01-07 18:00 | 2000-01-07 20:00 | 1        | https://example.org | abc    | 1         |

  Scenario: Get flights
    When I send a "GET" request to "/flights"
    Then I get response 200 status code
    And I get response 3 elements
    And I get response body:
"""json
[
  {
    "id": 1,
    "fromAirport": "WAW",
    "fromStart": "2000-01-01T10:00:00+00:00",
    "fromEnd": "2000-01-01T12:00:00+00:00",
    "fromStops": 0,
    "toAirport": "ZTH",
    "toStart": "2000-01-07T10:00:00+00:00",
    "toEnd": "2000-01-07T12:00:00+00:00",
    "toStops": 0,
    "url": "https:\/\/example.org",
    "price": 500.0,
    "currency": "PLN",
    "source": "xyz"
  },
  {
    "id": 2,
    "fromAirport": "WAW",
    "fromStart": "2000-01-01T14:00:00+00:00",
    "fromEnd": "2000-01-01T16:00:00+00:00",
    "fromStops": 1,
    "toAirport": "ZTH",
    "toStart": "2000-01-07T14:00:00+00:00",
    "toEnd": "2000-01-07T16:00:00+00:00",
    "toStops": 0,
    "url": "https:\/\/example.org",
    "price": 2999.99,
    "currency": "PLN",
    "source": "abc"
  },
  {
    "id": 3,
    "fromAirport": "WAW",
    "fromStart": "2000-01-01T18:00:00+00:00",
    "fromEnd": "2000-01-01T20:00:00+00:00",
    "fromStops": 0,
    "toAirport": "ZTH",
    "toStart": "2000-01-07T18:00:00+00:00",
    "toEnd": "2000-01-07T20:00:00+00:00",
    "toStops": 1,
    "url": "https:\/\/example.org",
    "price": 2.3,
    "currency": "PLN",
    "source": "abc"
  }
]
"""

  Scenario: Get flights with filters
    When I send a "GET" request to "/flights?search=1&source=abc"
    Then I get response 200 status code
    And I get response 2 elements
    And I get response body:
"""json
[
  {
    "id": 2,
    "fromAirport": "WAW",
    "fromStart": "2000-01-01T14:00:00+00:00",
    "fromEnd": "2000-01-01T16:00:00+00:00",
    "fromStops": 1,
    "toAirport": "ZTH",
    "toStart": "2000-01-07T14:00:00+00:00",
    "toEnd": "2000-01-07T16:00:00+00:00",
    "toStops": 0,
    "url": "https:\/\/example.org",
    "price": 2999.99,
    "currency": "PLN",
    "source": "abc"
  },
  {
    "id": 3,
    "fromAirport": "WAW",
    "fromStart": "2000-01-01T18:00:00+00:00",
    "fromEnd": "2000-01-01T20:00:00+00:00",
    "fromStops": 0,
    "toAirport": "ZTH",
    "toStart": "2000-01-07T18:00:00+00:00",
    "toEnd": "2000-01-07T20:00:00+00:00",
    "toStops": 1,
    "url": "https:\/\/example.org",
    "price": 2.3,
    "currency": "PLN",
    "source": "abc"
  }
]
"""
