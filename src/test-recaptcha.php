<?php

declare( strict_types = 1 );
namespace WAJAltTags;

use function WaughJ\TestHashItem\TestHashItemString;

const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

function testRecaptcha() : bool
{
    return TestHashItemString( $_POST, 'recaptcha_response', null ) !== null && testRecaptchaSuccess();
}

function testRecaptchaSuccess() : bool
{
    $responseKeys = generateResponseKeys();
    return $responseKeys[ 'success' ] && $responseKeys[ 'score' ] >= 0.5;
}

function generateResponseKeys()
{
    return json_decode( file_get_contents( VERIFY_URL, false, generateContext() ), true );
}

function generateContext()
{
    return stream_context_create( generateOptions() );
}

function generateOptions() : array
{
    return
    [
        'http' =>
        [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query( generateData() )
        ]
    ];
}

function generateData()
{
    return [ 'secret' => getSecretKey(), 'response' => getToken(), 'remoteip' => getIP() ];
}

function getSecretKey()
{
    return file_get_contents( '.gskey' );
}

function getToken()
{
    return $_POST[ 'recaptcha_response' ];
}

function getIP()
{
    return $_SERVER[ 'REMOTE_ADDR' ];
}
