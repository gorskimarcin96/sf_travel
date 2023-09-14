Feature: Searcher
  In order to get tripe pages
  As a user
  I need to use data in display got data

  Background:
    Given the database is clean
    And there are searches
      | id | nation | place     | created_at |
      | 1  | grecja | zakynthos | 2000-01-01 |
      | 2  | grecja | rodos     | 2000-01-01 |
    And there are trip pages
      | id | url                 | map    | source | search_id |
      | 1  | https://example.org | string | xyz    | 1         |
      | 2  | https://example.org | string | abc    | 1         |
    And there are trip page articles
      | id | title   | images        | descriptions              | trip_page_id |
      | 1  | title 1 | image1;image2 | description1;description2 | 1            |
      | 2  | title 2 | image         |                           | 1            |
      | 3  | title 3 |               | description               | 2            |

  Scenario: Get trip pages
    When I send a "GET" request to "/trip_pages"
    Then I get response 200 status code
    And I get response 2 elements
    And I get response body:
    """json
[
  {
    "id": 1,
    "url": "https:\/\/example.org",
    "map": "string",
    "tripArticles": [
      {
        "id": 1,
        "title": "title 1",
        "descriptions": [
          "description1",
          "description2"
        ],
        "images": [
          "image1",
          "image2"
        ]
      },
      {
        "id": 2,
        "title": "title 2",
        "descriptions": [
          ""
        ],
        "images": [
          "image"
        ]
      }
    ],
    "source": "xyz"
  },
  {
    "id": 2,
    "url": "https:\/\/example.org",
    "map": "string",
    "tripArticles": [
      {
        "id": 3,
        "title": "title 3",
        "descriptions": [
          "description"
        ],
        "images": [
          ""
        ]
      }
    ],
    "source": "abc"
  }
]
    """

  Scenario: Get trip pages with filters
    When I send a "GET" request to "/trip_pages?id=1&source=abc"
    Then I get response 200 status code
    And I get response 1 elements
    And I get response body:
    """json
[
  {
    "id": 2,
    "url": "https:\/\/example.org",
    "map": "string",
    "tripArticles": [
      {
        "id": 3,
        "title": "title 3",
        "descriptions": [
          "description"
        ],
        "images": [
          ""
        ]
      }
    ],
    "source": "abc"
  }
]
    """
