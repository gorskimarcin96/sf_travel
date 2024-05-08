Feature: Last minute
  In order to get last minute data
  As a user
  I need to use data in display last minute data

  Background:
    Given the database is clean
    And there are last minutes
      | id | adults | children | created_at |
      | 1  | 2      | 0        | 2000-01-01 |
    And there are trips
      | id | price  | title   | stars | rate | food          | image | from       | to         | url                 | source | last_minute_id |
      | 1  | 500    | title 1 | 2     | 2.2  | all-inclusive | image | 2000-01-01 | 2000-01-07 | https://example.org | xyz    | 1              |
      | 2  | 499.99 | title 2 | 4     | 8.0  | breakfast     | image | 2000-01-01 | 2000-01-07 | https://example.org | abc    | 1              |

  Scenario: Last minute data when data is exists in database
    When I send a "POST" request to "/last-minute"
"""json
{
  "adults": 2,
  "children": 0
}
"""
    Then I get response 200 status code
    And I get response body:
"""json
{
  "id": 1,
  "adults": 2,
  "children": 0,
  "hotelFoods": [],
  "services": [],
  "errors": [],
  "createdAt": "2000-01-01T00:00:00+00:00",
  "updatedAt": "@string@.isDateTime()",
  "finished": true,
  "countServices":{
    "xyz": 1,
    "abc": 1
  }
}
"""
    And Messenger bus is empty

  Scenario: Last minute data when data is exists in database but use force flag
    When I send a "POST" request to "/last-minute"
"""json
{
  "adults": 2,
  "children": 0,
  "force": true
}
"""
    Then I get response 200 status code
    And I get response body:
"""json
{
  "id": "@integer@",
  "adults": 2,
  "children": 0,
  "hotelFoods": [],
  "services": [],
  "errors": [],
  "createdAt": "@string@.isDateTime()",
  "updatedAt": "@string@.isDateTime()",
  "finished": false,
  "countServices": []
}
"""
    And Messenger bus has 1 records

  Scenario: Last minute data when data is not exists in database
    When I send a "POST" request to "/last-minute"
"""json
{
  "adults": 2,
  "children": 1
}
"""
    Then I get response 200 status code
    And I get response body:
"""json
{
  "id": "@integer@",
  "adults": 2,
  "children": 1,
  "hotelFoods":[],
  "services": [],
  "errors": [],
  "createdAt": "@string@.isDateTime()",
  "updatedAt": "@string@.isDateTime()",
  "finished": false,
  "countServices": []
}
"""
    And Messenger bus has 1 records
