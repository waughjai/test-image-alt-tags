<?php

declare( strict_types = 1 );
namespace WAJAltTags;

require_once( 'handle-errors.php' );
require_once( 'vendor/autoload.php' ); // Load composer stuff.
require_once( 'test-recaptcha.php' );
use Enrise\Uri;
use WaughJ\WebpageLinksList\WebpageLinksList;
use WaughJ\ImageAltTag\ImageAltTagList;
use function WaughJ\TestHashItem\TestHashItemExists;

chdir( '../src' );
echo generateContent();

function generateContent()
{
    if ( testURLIsNotSet() )
    {
        return generateTemplate( 'form.html.twig', [] );
    }
    else if ( testRecaptcha() )
    {
        $url = getFormattedURL();
        if ( testURLIsInvalid( $url ) )
        {
            return generateTemplate( 'invalid_url.html.twig', [ 'url' => $url ] );
        }
        return generateTemplate( 'results.html.twig', [ 'home' => $url, 'webpages' => generateWebPages( $url ) ] );
    }
    return '';
}

function generateWebPages( string $url ) : array
{
    $webpages = [];
    $links = generateLinks( $url );
    foreach ( $links as $link_url => $link_data )
    {
        $webpages[] = [ 'url' => $link_url, 'alts' => generateAltTagList( $link_data ) ];
    }
    return $webpages;
}

function generateLinks( string $url ) : array
{
    $links_list = new WebpageLinksList( $url, 250 );
    return $links_list->getData();
}

function generateAltTagList( $data )
{
    $alts = new ImageAltTagList( $data->raw_body );
    return $alts->getImageAltTags();
}

function testURLIsNotSet() : bool
{
    return TestHashItemExists( $_POST, 'url', null ) === null;
}

function getFormattedURL() : string
{
	$uri = new URI( $_POST[ 'url' ] );
	if ( !$uri->getScheme() )
	{
		$uri->setScheme( 'https' );
	}
	return $uri->getUri();
}

function testURLIsInvalid( string $url ) : bool
{
    return !filter_var( $url, FILTER_VALIDATE_URL );
}

function generateTemplate( string $temp, array $data )
{
    $twig = new \Twig\Environment( new \Twig\Loader\FilesystemLoader( '../src/templates' ), [] );
    return $twig->render( $temp, $data );
}
