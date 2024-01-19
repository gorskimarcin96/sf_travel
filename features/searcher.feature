Feature: Searcher
  In order to get searcher data
  As a user
  I need to use data in display searched data

  Background:
    Given the database is clean
    And there are searches
      | id | nation | place     | from       | to         | created_at |
      | 1  | grecja | zakynthos | 2000-01-01 | 2000-01-07 | 2000-01-01 |
    And there are flights
      | id | price   | from_airport | from_start       | from_end         | from_stops | to_airport | to_start         | to_end           | to_stops | url                 | source | search_id |
      | 1  | 500     | WAW          | 2000-01-01 10:00 | 2000-01-01 12:00 | 0          | ZTH        | 2000-01-07 10:00 | 2000-01-07 12:00 | 0        | https://example.org | xyz    | 1         |
      | 2  | 2999.99 | WAW          | 2000-01-01 14:00 | 2000-01-01 16:00 | 1          | ZTH        | 2000-01-07 14:00 | 2000-01-07 16:00 | 0        | https://example.org | abc    | 1         |
      | 3  | 2.30    | WAW          | 2000-01-01 18:00 | 2000-01-01 20:00 | 0          | ZTH        | 2000-01-07 18:00 | 2000-01-07 20:00 | 1        | https://example.org | abc    | 1         |
    And there are hotels
      | id | title   | address | stars | rate | price   | food          | description             | from       | to         | url                 | image               | source | search_id |
      | 1  | title 1 | City    | 2     | 2.5  | 500     | all-inclusive | description;description | 2000-01-01 | 2000-01-07 | https://example.org | https://example.jpg | xyz    | 1         |
      | 2  | title 2 | City    | 9     | 9.9  | 2999.99 | breakfast     |                         | 2000-01-01 | 2000-01-07 | https://example.org | https://example.jpg | abc    | 1         |
      | 3  | title 3 | City    | 3     | 0.3  | 2.30    | breakfast     | description             | 2000-01-01 | 2000-01-07 | https://example.org | https://example.jpg | abc    | 1         |
    And there are optional trips
      | id | title   | description             | url                 | image               | source | search_id |
      | 1  | title 1 | description;description | https://example.org | https://example.jpg | xyz    | 1         |
      | 2  | title 2 |                         | https://example.org | https://example.jpg | abc    | 1         |
      | 3  | title 3 | description             | https://example.org | https://example.jpg | abc    | 1         |
    And there are trip pages
      | id | url                 | map    | source | search_id |
      | 1  | https://example.org | string | xyz    | 1         |
      | 2  | https://example.org | string | abc    | 1         |
    And there are trip page articles
      | id | title   | images        | descriptions              | trip_page_id |
      | 1  | title 1 | image1;image2 | description1;description2 | 1            |
      | 2  | title 2 | image         |                           | 1            |
      | 3  | title 3 |               | description               | 2            |
    And there are trips
      | id | price  | title   | stars | rate | food          | image | from       | to         | url                 | source | search_id |
      | 1  | 500    | title 1 | 2     | 2.2  | all-inclusive | image | 2000-01-01 | 2000-01-07 | https://example.org | xyz    | 1         |
      | 2  | 499.99 | title 2 | 4     | 8.0  | breakfast     | image | 2000-01-01 | 2000-01-07 | https://example.org | abc    | 1         |
    And there are weathers
      | id | date       | temperature_2m_mean | precipitation_hours | precipitation_sum | source | search_id |
      | 1  | 2000-01-01 | 10                  | 0                   | 0                 | xyz    | 1         |
      | 2  | 2000-01-02 | 10.5                | 2                   | 1                 | abc    | 1         |
      | 3  | 2000-01-03 | 10.8                | 2.2                 | 1.2               | abc    | 1         |

  Scenario: Search data when data is exists in database
    When I send a "POST" request to "/search"
"""json
{
  "nation": "grecja",
  "place": "zakynthos",
  "from": "2000-01-01",
  "to": "2000-01-07"
}
"""
    Then I get response 200 status code
    And I get response body:
"""json
{
  "id": 1,
  "nation": "grecja",
  "place": "zakynthos",
  "from": "2000-01-01T00:00:00+00:00",
  "to": "2000-01-07T00:00:00+00:00",
  "adults": 2,
  "children": 0,
  "hotelFoods": [],
  "services": [],
  "errors": [],
  "createdAt": "2000-01-01T00:00:00+00:00",
  "updatedAt": "@string@.isDateTime()",
  "finished": true,
  "countServices":{
    "xyz": 6,
    "abc": 10
  }
}
"""
    And Messenger bus is empty

  Scenario: Search data when data is exists in database but use force flag
    When I send a "POST" request to "/search"
"""json
{
  "nation": "grecja",
  "place": "zakynthos",
  "from": "2000-01-01",
  "to": "2000-01-07",
  "force": true
}
"""
    Then I get response 200 status code
    And I get response body:
"""json
{
  "id": "@integer@",
  "nation": "grecja",
  "place": "zakynthos",
  "from": "@string@.isDateTime()",
  "to": "@string@.isDateTime()",
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

  Scenario: Search data when data is not exists in database
    When I send a "POST" request to "/search"
"""json
{
  "nation": "grecja",
  "place": "rodos",
  "from": "2000-01-01",
  "to": "2000-01-07"
}
"""
    Then I get response 200 status code
    And I get response body:
"""json
{
  "id": "@integer@",
  "nation": "grecja",
  "place": "rodos",
  "from": "@string@.isDateTime()",
  "to": "@string@.isDateTime()",
  "adults": 2,
  "children": 0,
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

  Scenario: Search data when input is empty
    When I send a "POST" request to "/search"
"""json
{
  "nation": "",
  "place": "",
  "from": "2000-01-01",
  "to": "2000-01-07"
}
"""
    Then I get response 422 status code
