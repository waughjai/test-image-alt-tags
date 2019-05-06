<?php

    // Turn off all errors.
    ini_set( 'display_errors', 0 );
    ini_set( 'display_startup_errors', 0 );
    error_reporting( E_NONE );

    require_once( 'vendor/autoload.php' );
	use Enrise\Uri;
    use WaughJ\WebpageLinksList\WebpageLinksList;
    use WaughJ\ImageAltTag\ImageAltTagList;
    use function WaughJ\TestHashItem\TestHashItemExists;

    $loader = new \Twig\Loader\FilesystemLoader( 'templates' );
    $twig = new \Twig\Environment
    (
        $loader,
        [ 'cache' => '.cache' ]
    );

    if ( TestHashItemExists( $_GET, 'url', null ) !== null )
    {
        $url = $_GET[ 'url' ];
		$uri = new URI( $url );
		if ( !$uri->getScheme() )
		{
			$uri->setScheme( 'https' );
		}
		$url = $uri->getUri();

        if ( !filter_var( $url, FILTER_VALIDATE_URL ) )
        {
            echo $twig->render( 'invalid_url.html.twig', [ 'url' => $url ] );
            return;
        }

        $links_list = new WebpageLinksList( $url, 250 );
        $links_data = $links_list->getData();

        $websites = [];
        foreach ( $links_data as $link_url => $data )
        {
            $alts = new ImageAltTagList( $data->raw_body );
            $alts = $alts->getImageAltTags();
            $websites[] = [ 'url' => $link_url, 'alts' => $alts ];
        }
        echo $twig->render( 'results.html.twig', [ 'home' => $url, 'websites' => $websites ] );
    }
    else
    {
        echo $twig->render( 'form.html.twig', [] );
    }
