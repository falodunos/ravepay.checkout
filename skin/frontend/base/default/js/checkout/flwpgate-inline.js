//<![CDATA[
jQuery(document).ready(function () {
    var paymentInfoUrl = getPaymentDetailUrl();
    jQuery.post(paymentInfoUrl, {}, function (data) {
        var processed = false;
        var orderExists = data.order_id ? 1 : 0;
        var payment_url = data.payment_url;
        var cart_url = data.cart_url;

        var buildConfig = function () {
            return {
                amount: data.amount,
                country: data.country,
                currency: data.currency,
                custom_description: data.custom_description,
                custom_logo: data.custom_logo,
                custom_title: data.custom_title,
                customer_email: data.customer_email,
                customer_phonenumber: data.customer_phonenumber,
                customer_firstname: data.customer_firstname,
                customer_lastname: data.customer_lastname,
                pay_button_text: data.pay_button_text,
                PBFPubKey: data.PBFPubKey,
                txref: data.txref,
//                integrity_hash: data.integrity_hash,
                onclose: function () {
                    if (!processed) {
                        processed = true;
                        var h2 = document.getElementById('status-text');
                        var button = createDOMElement('button', {id: 'pay-button', class: 'button'});
                        var h2Container = h2.parentNode;

                        h2.innerHTML = 'Payment has not been received';
                        button.innerHTML = 'PAY NOW';
                        h2Container.appendChild(button);

                        button.addEventListener('click', function () {
                            processPayment();
                        });
                    }
                },
                callback: function (res) {
                    console.log(res);
                    sendOrderResponse(res);
                }
            };
        };

        var sendOrderResponse = function (res) {
            processed = true;
            var data = res.data.tx, tx = res.tx;
            var responseCode = '', flwRef = '';
            if (typeof (data) !== "undefined") {
                responsecode = (data.paymentType === 'account') ? data.acctvalrespcode : data.vbvrespcode;
                flwRef = data.flwRef;
            } else {
                responsecode = (tx.paymentType === 'account-internet-banking') ? tx.vbvrespcode : tx.acctvalrespcode;
                flwRef = tx.flwRef;
            }
            args = {
                responseCode: responseCode,
                flwRef: flwRef,
                orderId: data.order_id
            };
            submitOrderForm(args);

        };
        var submitOrderForm = function (data) {
            var form = createDOMElement('form', {method: 'post', action: payment_url});
            form.appendChild(createDOMElement('input', {name: 'flwRef', value: data.flwRef}));
            form.style.display = "";
            document.body.appendChild(form);
            form.submit();
        };

        var processPayment = function () {
            if (orderExists === 1) {
                var config = buildConfig();
                getpaidSetup(config);
            } else {
                location.href = cart_url;
            }
        };

        /** Creates element to be attached to the DOM */
        var createDOMElement = function (type, attrObj) {
            var elementAttr = attrObj || {};
            var element = document.createElement(type);

            if (Object.keys(elementAttr).length > 0) {
                for (var attr in elementAttr) {
                    if (elementAttr.hasOwnProperty(attr)) {
                        element.setAttribute(attr, elementAttr[attr]);
                    }
                }
            }

            return element;
        };
        
        initProcessPayment = function () {
            //if getpaidSetup is in scope, call process payment function
            if (typeof getpaidSetup != "undefined") {
                processPayment();
            } else {
                //keep checking until getpaidSetup is defined
                setTimeout(initProcessPayment, 500);
            }

        };
        initProcessPayment();
    });

    function getPaymentDetailUrl() {
        var url = window.location.href;
        var urlParts = url.split('/');
        newurl = urlParts[0] + urlParts[1] + '//' + urlParts[2] + '/'
                + urlParts[3] + '/ravecheckout/payment/getPaymentDetails';

        return newurl;
    }
});
//]]>