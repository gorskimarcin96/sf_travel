Feature: Searcher
  In order to get optional trips
  As a user
  I need to use data in display got data

  Background:
    Given the database is clean
    And there are searches
      | id | nation | place     | from       | to         | created_at |
      | 1  | grecja | zakynthos | 2000-01-01 | 2000-01-07 | 2000-01-01 |
      | 2  | grecja | rodos     | 2000-01-01 | 2000-01-07 | 2000-01-01 |
    And there are optional trips
      | id | title   | description             | url                 | image               | source | price | search_id |
      | 1  | title 1 | description;description | https://example.org | https://example.jpg | xyz    | 99.99 | 1         |
      | 2  | title 2 |                         | https://example.org | https://example.jpg | abc    | 99.99 | 1         |
      | 3  | title 3 | description             | https://example.org | https://example.jpg | abc    | 99.99 | 1         |

  Scenario: Get optional trips
    When I send a "GET" request to "/optional_trips"
    Then I get response 200 status code
    And I get response 3 elements
    And I get response body:
"""json
[
  {
    "id": 1,
    "title": "title 1",
    "description": [
      "description",
      "description"
    ],
    "url": "https:\/\/example.org",
    "image": "https:\/\/example.jpg",
    "source": "xyz",
    "price": 99.99,
    "currency": "PLN"
  },
  {
    "id": 2,
    "title": "title 2",
    "description": [
      ""
    ],
    "url": "https:\/\/example.org",
    "image": "https:\/\/example.jpg",
    "source": "abc",
    "price": 99.99,
    "currency": "PLN"
  },
  {
    "id": 3,
    "title": "title 3",
    "description": [
      "description"
    ],
    "url": "https:\/\/example.org",
    "image": "https:\/\/example.jpg",
    "source": "abc",
    "price": 99.99,
    "currency": "PLN"
  }
]
"""

  Scenario: Get optional trips with filters
    When I send a "GET" request to "/optional_trips?search=1&source=abc"
    Then I get response 200 status code
    And I get response 2 elements
    And I get response body:
"""json
[
  {
    "id": 2,
    "title": "title 2",
    "description": [
      ""
    ],
    "url": "https:\/\/example.org",
    "image": "https:\/\/example.jpg",
    "source": "abc",
    "price": 99.99,
    "currency": "PLN"
  },
  {
    "id": 3,
    "title": "title 3",
    "description": [
      "description"
    ],
    "url": "https:\/\/example.org",
    "image": "https:\/\/example.jpg",
    "source": "abc",
    "price": 99.99,
    "currency": "PLN"
  }
]
"""
