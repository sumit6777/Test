<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Context\TranslatableContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\Element;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Mink\WebAssert;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;


/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements SnippetAcceptingContext
{

	private $_vars = array();
    private $_parameters = array();
	
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(array $parameters)
    {
		$this->_parameters = $parameters;
		$this->_vars['rand'] = rand(10000000,99999999);
        echo "Setting rand to: " . $this->_vars['rand'] . "\n\n";
		
        $this->_vars['randstring'] = $randomString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
        echo "Setting randstring to: " . $this->_vars['randstring'] . "\n\n";
	 		
    }
     /**
     * @Transform /^(.+)$/
     */
    public function addRandomString($string)
    {
		//echo "robots in disguise\n";
		$value = str_replace('<rand>', $this->_vars['rand'], $string);
        $value = str_replace('<randstring>', $this->_vars['randstring'], $value);
		return $value;
    }
	
public function spin ($lambda)
{
	$i=1;
    while (true)
    {
        try {
            if ($lambda($this)) {
                return true;
				
            }
        } catch (Exception $e) {
             //do nothing
        }

        sleep(1);
		$i++;
		if($i==30)
		{
		 throw new Exception("Element is not visible");
         break; 		 
		}	
    }
}

	/** @AfterScenario */
 /**
     * Take screen-shot when step fails.
     *
     * @AfterStep
     * @param AfterStepScope $scope
     */
    public function takeScreenshotAfterFailedStep(AfterStepScope $scope)
    {
	 
        if (99 === $scope->getTestResult()->getResultCode())
		{
          $driver = $this->getSession()->getDriver();

            if (! is_dir($this->_parameters['screenshots_path'])) 
			{
                mkdir($this->_parameters['screenshots_path'], 0777, true);
            }

            $filename = sprintf(
               '%s_%s_%s.%s',
                $this->getMinkParameter('browser_name'),
                date('Ymd') . '-' . date('His'),
                uniqid('', true),
                'png'
            );
		     $this->saveScreenshot($filename, $this->_parameters['screenshots_path']);
        }
    }

	
	/**
     * To wait for a specific element 
     *
     * @When /^I wait for element "(?P<select>(?:[^"]|\\")*)"$/
     */
    public function iWaitForElement($arg1)
    {
       
	   $this->spin(function($context)use(&$arg1){
       return($context->getSession()->getPage()->find('xpath','//*[text()="'.$arg1.'" or @id= "'.$arg1.'" or @title= "'.$arg1.'" or @name = "'.$arg1.'"]')!=Null);
       });
	}
	
  /**
  * @When I click element :field
  */
 public function iClickElement($field)
 {
        
        $element = $this->getSession()->getPage()->find('xpath', "//*[contains(@name,'".$field."') or contains(@id,'".$field."') or contains(text(),'".$field."')]");
        if ($element) 
		{
           $element->click();
		}
        else 
		{
           throw new Exception("Element ".$field." is not visible");

		}
    }
	
	 /**
     * Fills in form field 
     *
     * @When /^(?:|I )fill in field "(?P<field>(?:[^"]|\\")*)" with "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function fillInputField($field, $value)
    {
	   $element = $this->getSession()->getPage()->find('xpath', "//input[contains(@name,'".$field."') or contains(@id,'".$field."') or contains(text(),'".$field."')]|//textarea[contains(@name,'".$field."') or contains(@id,'".$field."') or contains(text(),'".$field."')]");
	   if ($element) 
		{
           $element->setValue($value);
		}
        else 
		{
           throw new Exception("Element ".$field." is not visible");
		}
	}
	
	/**
	* @Then I should see message :arg1
    * @Then I should see field :arg1
    */
   public function iShouldSeeField($arg1)
   {
     $field= $this->getSession()->getPage()->find('xpath',"//*[contains(@name,'".$arg1."') or contains(@id,'".$arg1."') or contains(text(),'".$arg1."')]");
	 if($arg1 == null)
	 {
		throw new Exception('Field not found. '.$field); 	 
	 }	
   }
 }
 
