Feature: Searcher
  In order to get searcher data
  As a user
  I need to use data in display searched data

  Background:
    Given the database is clean
    And there are searches
      | id | nation | place     | created_at |
      | 1  | grecja | zakynthos | 2000-01-01 |

  Scenario: Search data when data is exists in database
    When I send a "POST" request to "/search"
"""json
{
  "nation": "grecja",
  "place": "zakynthos"
}
"""
    Then I get response 200 status code
    And I get response body:
"""json
{
  "id": 1,
  "nation": "grecja",
  "place": "zakynthos",
  "services": [],
  "errors": [],
  "createdAt": "2000-01-01T00:00:00+00:00",
  "updatedAt": "@string@.isDateTime()",
  "finished": true
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
  "services": [],
  "errors": [],
  "createdAt": "@string@.isDateTime()",
  "updatedAt": "@string@.isDateTime()",
  "finished": false
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
  "services": [],
  "errors": [],
  "createdAt": "@string@.isDateTime()",
  "updatedAt": "@string@.isDateTime()",
  "finished": false
}
"""
    And Messenger bus has 1 records
