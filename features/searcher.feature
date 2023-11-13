Feature: Searcher
  In order to get searcher data
  As a user
  I need to use data in display searched data

  Background:
    Given the database is clean
    And there are searches
      | id | nation | place     | from       | to         | created_at |
      | 1  | grecja | zakynthos | 2000-01-01 | 2000-01-07 | 2000-01-01 |

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
  "services": [],
  "errors": [],
  "createdAt": "2000-01-01T00:00:00+00:00",
  "updatedAt": "@string@.isDateTime()",
  "finished": true,
  "countServices": []
}
"""
    And Messenger bus is empty

  Scenario: Search data when data is exists in database but use force flag
    When I send a "POST" request to "/search"
"""json
{
  "nation": "grecja",
  "place": "zakynthos",
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
  "place": "rodos"
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
  "place": ""
}
"""
    Then I get response 422 status code
