<?php
namespace IpaySecure;
// http://en.wikipedia.org/wiki/ISO_4217
return array(
    "100" => "Successful transaction",
    "101" => "The request is missing one or more required fields",
    "102" => "One or more fields in the request contains invalid data",
    "104" => "The merchant reference code for this authorization request matches the merchant reference code of another authorization request that you sent within the past 15 minutes",
    "110" => "Only a partial amount was approved",
    "150" => "General system failure",
    "151" => "The request was received but there was a server timeout",
    "152" => "The request was received, but a service did not finish running in time",
    "200" => "The authorization request was approved by the issuing bank but declined by CyberSource because it did not pass the Address Verification System (AVS) check",
    "201" => "The issuing bank has questions about the request",
    "202" => "Expired card",
    "203" => "General decline of the card",
    "204" => "Insufficient funds in the account",
    "205" => "Stolen or lost card",
    "207" => "Issuing bank unavailable",
    "208" => "Inactive card or card not authorized for card-not-present transactions",
    "209" => "The card has reached the credit limit",
    "210" => "Invalid CVN",
    "211" => "Invalid CVN",
    "221" => "The customer matched an entry on the processor’s negative file",
    "230" => "The authorization request was approved by the issuing bank but declined by CyberSource because it did not pass the CVN check",
    "231" => "Invalid account number",
    "232" => "The card type is not accepted by the payment processor",
    "233" => "General decline by the processor",
    "234" => "There is a problem with the information in your CyberSource account",
    "235" => "The requested capture amount exceeds the originally authorized amount",
    "236" => "Processor failure",
    "237" => "The authorization has already been reversed",
    "238" => "The authorization has already been captured",
    "239" => "The requested transaction amount must match the previous transaction amount",
    "240" => "The card type sent is invalid or does not correlate with the payment card number",
    "241" => "The request ID is invalid.",
    "242" => "You requested a capture, but there is no corresponding, unused authorization
record",
    "243" => "The transaction has already been settled or reversed",
    "246" => "The capture or credit is not voidable because the capture or credit information has already been submitted to your processor",
    "247" => "You requested a credit for a capture that was previously voided",
    "250" => "The request was received, but there was a timeout at the payment processor",
    "254" => "Stand-alone credits are not allowed",
    "256" => "Credit amount exceeds maximum allowed for your CyberSource account",
    "475" =>"The customer is enrolled in payer authentication",
    "476" => "The customer cannot be authenticated."
);

?>