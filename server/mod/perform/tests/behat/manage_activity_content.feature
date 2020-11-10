@totara @perform @mod_perform @perform_element @javascript @vuejs
Feature: Adding, Updating, Removing activity elements.

  Background:
    Given the following "activities" exist in "mod_perform" plugin:
      | activity_name        | create_section | create_track | activity_status |
      | Add Element Activity | true           | true         | Draft           |

  Scenario: Save multiple elements to activity content.
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    # Add multiple elements
    When I click on "Add Element Activity" "link"
    And I click on "Content" "link" in the ".tui-tabs__tabs" "css_element"
    And I click on "Edit content elements" "button"
    And I add a "Short text" activity content element
    Then the focused element is "[name=rawTitle]" "css_element"

    When I set the following fields to these values:
      | rawTitle   | Question 1   |
      | identifier | Identifier 1 |
    And I save the activity content element
    Then I should see "Element saved." in the tui success notification toast
    When I close the tui notification toast
    And I add a "Short text" activity content element
    Then the focused element is "[name=rawTitle]" "css_element"

    When I set the following fields to these values:
      | rawTitle   | Question 2   |
      | identifier | Identifier 2 |
    And I save the activity content element
    And I add a "Short text" activity content element
    Then the focused element is "[name=rawTitle]" "css_element"

    When I set the following fields to these values:
      | rawTitle | Question 3 |
    And I save the activity content element
    Then I should see "Element saved." in the tui success notification toast
    When I close the tui notification toast
    And I close the tui modal
    And I click on "Edit content elements" "button"
    Then I should see "Question 1"
    And I should see "Question 2"
    And I should see "Question 3"
    When I click on the Reporting ID action for question "Question 1"
    Then I should see "Identifier 1"
    And I close popovers
    And I should not see the Reporting ID action for question "Question 3"

    # Update multiple elements and save.
    When I click on the Edit element action for question "Question 1"
    Then the focused element is "[name=rawTitle]" "css_element"

    And the following fields match these values:
      | rawTitle   | Question 1   |
      | identifier | Identifier 1 |
    When I set the following fields to these values:
      | rawTitle   | Test 1       |
      | identifier | Identifier A |
    And I save the activity content element
    And I click on the Edit element action for question "Question 2"
    Then the focused element is "[name=rawTitle]" "css_element"

    When I set the following fields to these values:
      | rawTitle   | Test 2 |
      | identifier |        |
    And I save the activity content element
    And I click on the Edit element action for question "Question 3"
    Then the focused element is "[name=rawTitle]" "css_element"

    When I set the following fields to these values:
      | rawTitle   | Test 3       |
      | identifier | Identifier C |
    And I save the activity content element
    And I close the tui notification toast
    And I close the tui modal
    And I click on "Edit content elements" "button"
    Then I should see "Test 1"
    And I should see "Test 2"
    And I should see "Test 3"
    When I click on the Reporting ID action for question "Test 1"
    Then I should see "Identifier A"
    And I close popovers
    And I should not see the Reporting ID action for question "Test 2"

    # Deletion confirmation modal.
    When I click on the Delete element action for question "Test 1"
    Then I should see "Confirm delete element" in the tui modal
    And I should see "This cannot be undone." in the tui modal
    When I close the tui modal
    Then I should not see "Element deleted."
    And I click on the Delete element action for question "Test 1"
    And I confirm the tui confirmation modal
    Then I should see "Element deleted." in the tui success notification toast
    And I close the tui notification toast

    # Unsaved changes dialog should not be triggered
    And I close the tui modal
    And I click on "Edit content elements" "button"
    Then I should not see "Test 1"
    And I should see "Test 2"
    And I should see "Test 3"

    # Delete using icon when not in edit mode
    When I click on the Delete element action for question "Test 2"
    Then I should see "Confirm delete element" in the tui modal
    And I should see "This cannot be undone." in the tui modal
    And I confirm the tui confirmation modal
    Then I should see "Element deleted." in the tui success notification toast
    And I close the tui notification toast

    # Only one element should remain
    Then I should see "Test 3"
    And I should not see "Test 1"
    And I should not see "Test 2"

    # Confirmation should be shown when closing whilst still editing
    When I click on the Edit element action for question "Test 3"
    And I close the tui modal
    Then I should see "Unsaved changes will be lost" in the tui modal
    And I should see "You currently have unsaved changes that will be lost if you close this content editor. Cancel to go back and save individual content elements. Close to discard the changes." in the tui modal
    When I close the tui modal
    Then I should see "Add element"
    When I close the tui modal
    And I confirm the tui confirmation modal
    Then I should not see "Add element"

    # Changes should be permanent
    And I click on "Edit content elements" "button"
    Then I should see "Test 3"
    And I should not see "Test 1"
    And I should not see "Test 2"

  Scenario: Reorder elements in a section
    Given I log in as "admin"
    And I navigate to the manage perform activities page

    # Add multiple elements
    When I click on "Add Element Activity" "link"
    And I click on "Content" "link" in the ".tui-tabs__tabs" "css_element"
    And I click on "Edit content elements" "button"
    And I add a "Short text" activity content element
    Then the focused element is "[name=rawTitle]" "css_element"
    When I set the following fields to these values:
      | rawTitle   | Question 1   |
      | identifier | Identifier 1 |
    And I save the activity content element
    Then  I should not see drag icon visible in the question "Question 1"

    When I add a "Short text" activity content element
    And the focused element is "[name=rawTitle]" "css_element"
    And I set the following fields to these values:
      | rawTitle   | Question 2   |
      | identifier | Identifier 2 |
    And I save the activity content element
    Then I should see drag icon visible in the question "Question 1"
    And I should see drag icon visible in the question "Question 2"

    When I add a "Short text" activity content element
    And the focused element is "[name=rawTitle]" "css_element"

    And I set the following fields to these values:
      | rawTitle | Question 3 |
    And I save the activity content element
    Then I should see drag icon visible in the question "Question 1"
    And I should see drag icon visible in the question "Question 2"
    And I should see drag icon visible in the question "Question 3"

