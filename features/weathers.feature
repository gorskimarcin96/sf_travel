Feature: Searcher
  In order to get weathers
  As a user
  I need to use data in display got data

  Background:
    Given the database is clean
    And there are searches
      | id | nation | place     | from       | to         | created_at |
      | 1  | grecja | zakynthos | 2000-01-01 | 2000-01-07 | 2000-01-01 |
      | 2  | grecja | rodos     | 2000-01-01 | 2000-01-07 | 2000-01-01 |
    And there are weathers
      | id | date       | temperature_2m_mean | precipitation_hours | precipitation_sum | source | search_id |
      | 1  | 2000-01-01 | 10                  | 0                   | 0                 | xyz    | 1         |
      | 2  | 2000-01-02 | 10.5                | 2                   | 1                 | abc    | 1         |
      | 3  | 2000-01-03 | 10.8                | 2.2                 | 1.2               | abc    | 1         |

  Scenario: Get weathers
    When I send a "GET" request to "/weather"
    Then I get response 200 status code
    And I get response 3 elements
    And I get response body:
"""json
[
  {
    "id": 1,
    "date": "2000-01-01T00:00:00+00:00",
    "temperature2mMean": 10.0,
    "precipitationHours": 0.0,
    "precipitationSum": 0.0,
    "source": "xyz"
  },
  {
    "id": 2,
    "date": "2000-01-02T00:00:00+00:00",
    "temperature2mMean": 10.5,
    "precipitationHours": 2.0,
    "precipitationSum": 1.0,
    "source": "abc"
  },
  {
    "id": 3,
    "date": "2000-01-03T00:00:00+00:00",
    "temperature2mMean": 10.8,
    "precipitationHours": 2.2,
    "precipitationSum": 1.2,
    "source": "abc"
  }
]
"""

  Scenario: Get weathers with filters
    When I send a "GET" request to "/weather?search=1&source=abc"
    Then I get response 200 status code
    And I get response 2 elements
    And I get response body:
"""json
[
  {
    "id": 2,
    "date": "2000-01-02T00:00:00+00:00",
    "temperature2mMean": 10.5,
    "precipitationHours": 2.0,
    "precipitationSum": 1.0,
    "source": "abc"
  },
  {
    "id": 3,
    "date": "2000-01-03T00:00:00+00:00",
    "temperature2mMean": 10.8,
    "precipitationHours": 2.2,
    "precipitationSum": 1.2,
    "source": "abc"
  }
]
"""
