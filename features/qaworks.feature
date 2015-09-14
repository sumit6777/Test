Feature: Contact Us Page
As an end user
I want to access contact us page
So that I can find out more about QAWorks exciting services

  @regression @sanity @contactus @ut
  Scenario: Valid Submission of a user query on Contact Us Page
    Given I am on "http://qaworks.com"
	And I wait for element "Contact"
	When I click element "Contact"
	And I wait for element "ContactSend"
    And I fill in field "NameBox" with "j.Bloggs<randstring>"
 	And I fill in field "EmailBox" with "j.Bloggs@qaworks.com"
	And I fill in field "MessageBox" with "please contact me I want to find out more"
	And I click element "SendButton"
	And I wait for element "ContactSend"
	Then I should see field "NameBox"
	
    
  @regression @sanity @contactus @negative
  Scenario Outline: Verify error messages on Contact Page
    Given I am on "http://qaworks.com"
	And I wait for element "Contact"
	When I click element "Contact"
	And I wait for element "ContactSend"
    And I fill in field "NameBox" with "<Name>"
 	And I fill in field "EmailBox" with "<Email>"
	And I fill in field "MessageBox" with "<Text>"
	And I click element "SendButton"
	And I wait for element "ContactSend"
	Then I should see message "<Message>"

Examples:
|Name|Email|Text|Message|	
||Jo.Bloggs@jo.com|This is a test|Your name is required|
|Jo Bloggs||This is a test|An Email address is required|
|Jo Bloggs|Jo.Bloggs|This is a test|Invalid Email Address|
|Jo Bloggs|Jo.Bloggs@jo.com||Please type your message|
