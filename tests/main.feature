Feature: Adding a product to the cart
  As an anonymous user
  I want to add a product to the cart
  In order to prepare my order

  Scenario: Add a product to the cart and proceed to checkout
    Given I am on the homepage
    When I click on the "#en_savoir_plus" button
    Then I am taken to the product page
    When I click on the "Add to cart" button
    And I click on the button to access the cart
    And I click on the button to proceed to checkout
    Then I fill in the email field with "user@example.com"
    And I fill in the password field with "test"