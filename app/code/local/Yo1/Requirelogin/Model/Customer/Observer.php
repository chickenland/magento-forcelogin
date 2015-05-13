<?php

class Yo1_Requirelogin_Model_Customer_Observer extends Mage_Core_Model_Abstract {

    // lets have a whitelist of pages that you can visit without being logged in
    // this variable should be compatible with preg_match
    protected $whitelist = "#homepage|\/index\/login|terms-and-conditions|privacy-policy|contact-us#";

    public function requireLogin($observer)
    {

        // get current controller action
        /* @var $controllerAction Mage_Core_Controller_Front_Action */
        $controllerAction = $observer->getEvent()->getControllerAction();

        $requestString = $controllerAction->getRequest()->getRequestString();

        $session = Mage::getSingleton('customer/session');
        if (!$session->isLoggedIn()) {

            if (!preg_match($this->whitelist, $requestString)) {
                // allow the user to see the homepage we can't match this in the whitelist
                if ($requestString !== "/") {
                    // set redirect back to this page once they've logged in
                    $session->setBeforeAuthUrl( $requestString );
                    $controllerAction->getResponse()->setRedirect( Mage::getUrl( '/' ) );
                    $controllerAction->getResponse()->sendResponse();
                    die();
                }
            }

        }

        // additional logic - if the user is logged in, don't let them hit the homepage
         else {
            // I am logged in
            if ($requestString == "/") {
                // redirect to dashboard if logged in
                $controllerAction->getResponse()->setRedirect( Mage::getUrl( 'customer/account/' ) );
                $controllerAction->getResponse()->sendResponse();
                die();
            }

        }

    }

}