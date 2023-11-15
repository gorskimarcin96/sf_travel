Feature: Searcher
  In order to get tripe pages
  As a user
  I need to use data in display got data

  Background:
    Given the database is clean
    And there are searches
      | id | nation | place     | from       | to         | created_at |
      | 1  | grecja | zakynthos | 2000-01-01 | 2000-01-07 | 2000-01-01 |
      | 2  | grecja | rodos     | 2000-01-01 | 2000-01-07 | 2000-01-01 |
    And there are hotels
      | id | title   | address | rate | price   | description             | url                 | image               | source | search_id |
      | 1  | title 1 | City    | 2.5  | 500     | description;description | https://example.org | https://example.jpg | xyz    | 1         |
      | 2  | title 2 | City    | 9.9  | 2999.99 |                         | https://example.org | https://example.jpg | abc    | 1         |
      | 3  | title 3 | City    | 0.3  | 2.30    | description             | https://example.org | https://example.jpg | abc    | 1         |

  Scenario: Get hotels
    When I send a "GET" request to "/hotels"
    Then I get response 200 status code
    And I get response 3 elements
    And I get response body:
"""json
[
  {
    "id": 1,
    "title": "title 1",
    "url": "https:\/\/example.org",
    "image": "https:\/\/example.jpg",
    "address": "City",
    "descriptions": [
      "description",
      "description"
    ],
    "rate": 2.5,
    "money": {
      "id": 1,
      "price": 500.0,
      "currency": "PLN"
    },
    "source": "xyz"
  },
  {
    "id": 2,
    "title": "title 2",
    "url": "https:\/\/example.org",
    "image": "https:\/\/example.jpg",
    "address": "City",
    "descriptions": [
      ""
    ],
    "rate": 9.9,
    "money": {
      "id": 2,
      "price": 2999.99,
      "currency": "PLN"
    },
    "source": "abc"
  },
  {
    "id": 3,
    "title": "title 3",
    "url": "https:\/\/example.org",
    "image": "https:\/\/example.jpg",
    "address": "City",
    "descriptions": [
      "description"
    ],
    "rate": 0.3,
    "money": {
      "id": 3,
      "price": 2.3,
      "currency": "PLN"
    },
    "source": "abc"
  }
]
"""

  Scenario: Get hotels with filters
    When I send a "GET" request to "/hotels?search=1&source=abc"
    Then I get response 200 status code
    And I get response 2 elements
    And I get response body:
"""json
[
  {
    "id": 2,
    "title": "title 2",
    "url": "https:\/\/example.org",
    "image": "https:\/\/example.jpg",
    "address": "City",
    "descriptions": [
      ""
    ],
    "rate": 9.9,
    "money": {
      "id": 2,
      "price": 2999.99,
      "currency": "PLN"
    },
    "source": "abc"
  },
  {
    "id": 3,
    "title": "title 3",
    "url": "https:\/\/example.org",
    "image": "https:\/\/example.jpg",
    "address": "City",
    "descriptions": [
      "description"
    ],
    "rate": 0.3,
    "money": {
      "id": 3,
      "price": 2.3,
      "currency": "PLN"
    },
    "source": "abc"
  }
]
"""
