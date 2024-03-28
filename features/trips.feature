Feature: Searcher
  In order to get trips
  As a user
  I need to use data in display got data

  Background:
    Given the database is clean
    And there are searches
      | id | nation | place     | from       | to         | created_at |
      | 1  | grecja | zakynthos | 2000-01-01 | 2000-01-07 | 2000-01-01 |
      | 2  | grecja | rodos     | 2000-01-01 | 2000-01-07 | 2000-01-01 |
    And there are trips
      | id | price  | title   | stars | rate | food          | image | from       | to         | url                 | source | search_id |
      | 1  | 500    | title 1 | 2     | 2.2  | all-inclusive | image | 2000-01-01 | 2000-01-07 | https://example.org | xyz    | 1         |
      | 2  | 499.99 | title 2 | 4     | 8.0  | breakfast     | image | 2000-01-01 | 2000-01-07 | https://example.org | abc    | 1         |

  Scenario: Get trips
    When I send a "GET" request to "/trips"
    Then I get response 200 status code
    And I get response 2 elements
    And I get response body:
    """json
    [
      {
        "id": 1,
        "title": "title 1",
        "url": "https:\/\/example.org",
        "stars": 2,
        "rate": 2.2,
        "food": "all-inclusive",
        "from": "2000-01-01T00:00:00+00:00",
        "to": "2000-01-07T00:00:00+00:00",
        "image": "image",
        "price": 500.00,
        "currency":"PLN",
        "source": "xyz"
      },
      {
        "id": 2,
        "title": "title 2",
        "url": "https:\/\/example.org",
        "stars": 4,
        "rate": 8.0,
        "food": "breakfast",
        "from": "2000-01-01T00:00:00+00:00",
        "to": "2000-01-07T00:00:00+00:00",
        "image": "image",
        "price": 499.99,
        "currency":"PLN",
        "source": "abc"
      }
    ]
    """

  Scenario: Get trips with filters
    When I send a "GET" request to "/trips?search=1&source=abc"
    Then I get response 200 status code
    And I get response 1 elements
    And I get response body:
    """json
    [
      {
        "id": 2,
        "title": "title 2",
        "url": "https:\/\/example.org",
        "stars": 4,
        "rate": 8.0,
        "food": "breakfast",
        "from": "2000-01-01T00:00:00+00:00",
        "to": "2000-01-07T00:00:00+00:00",
        "image": "image",
        "price": 499.99,
        "currency":"PLN",
        "source": "abc"
      }
    ]
    """
