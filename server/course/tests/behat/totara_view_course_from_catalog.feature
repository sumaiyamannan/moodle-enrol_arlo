@totara @core @core_course @totara_catalog @javascript
Feature: View course from catalog
  Scenario: View course by a student
    Given I am on a totara site
    And I log in as "admin"
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And I am on "Course 1" course homepage
    And I navigate to "Enrolment methods" node in "Course administration > Users"
    And I click on "Enable" "link" in the "Guest access" "table_row"
    And I log out
    And I log in as "student1"
    And I am on course index
    And I click on "div[title=\"Course 1\"]" "css_element"
    Then I should see "Course 1"