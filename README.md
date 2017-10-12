# ravepay.checkout
Magento 1.9.x for RavePay Payment Gateway - Powered By Flutterwave

Installation Guide

1 - Clone this project or copy/paste into the root of your Magento 1.9.x site installation directory

2 - Open ravecheckout.xml located at app/design/frontend/base/default/layout/ravecheckout.xml

Note : The content is shown below:

``` xml
<?xml version="1.0"?>
<layout version="0.1.0">
  <ravecheckout_payment_process>
    <reference name="head">
       <block type="core/text" name="flwpbf-inline">
        <action method="setText">
          <text>
              <!--This is the TEST MODE SCRIPT : Enabled by default, comment the line below to disable test mode -->
              <![CDATA[<script flwjs type="text/javascript" src="//flw-pms-dev.eu-west-1.elasticbeanstalk.com/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>]]> 
                <!--This is for the LIVE MODE SCRIPT : Disabled by default, ucomment the line below to enable live mode-->
              <!--<![CDATA[<script flwjs type="text/javascript" src="//api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>]]>-->
              <!--NOTE: Do not enable both script at the same time-->
          </text>
        </action>
      </block>
      <action method="addItem"><type>skin_js</type><name>js/checkout/flwpgate-inline.js</name></action>
    </reference>
  </ravecheckout_payment_process>
</layout>
``` 

3 - Configure as explained in the commented out instructions

4 - Login to your Magento site back end 

5 - Locate Rave Checkout by following : System -> Configuration -> Payment Methods

6 - Configure as shown in the sample configuration screen shot

7 - If you enable live or test mode, ensure your configuration matches the instructions in step 2.

8 - Enjoy payment integration with ease.

NOTE: For further assistance, email me at falodunosolomon@gmail.com