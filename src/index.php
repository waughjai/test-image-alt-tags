<?php

    const DEBUG = true;

    // Turn off all errors.
    if ( DEBUG )
    {
        ini_set( 'display_errors', 1 );
        ini_set( 'display_startup_errors', 1 );
        error_reporting( E_ALL );
    }
    else
    {
        ini_set( 'display_errors', 0 );
        ini_set( 'display_startup_errors', 0 );
        error_reporting( E_NONE );
    }

    chdir( '../src' );
    require_once( 'vendor/autoload.php' );
	use Enrise\Uri;
    use WaughJ\WebpageLinksList\WebpageLinksList;
    use WaughJ\ImageAltTag\ImageAltTagList;
    use function WaughJ\TestHashItem\TestHashItemExists;

    echo generateContent();
    function generateContent()
    {
        $twig = new \Twig\Environment( new \Twig\Loader\FilesystemLoader( '../src/templates' ), [] );

        if ( TestHashItemExists( $_POST, 'url', null ) !== null )
        {
            if ( TestHashItemExists( $_POST, 'recaptcha_response', null ) !== null && testRecaptchaSuccess( $_POST[ 'recaptcha_response' ] ) )
            {
                $url = $_POST[ 'url' ];
        		$uri = new URI( $url );
        		if ( !$uri->getScheme() )
        		{
        			$uri->setScheme( 'https' );
        		}
        		$url = $uri->getUri();

                if ( !filter_var( $url, FILTER_VALIDATE_URL ) )
                {
                    return $twig->render( 'invalid_url.html.twig', [ 'url' => $url ] );
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
                return $twig->render( 'results.html.twig', [ 'home' => $url, 'websites' => $websites ] );
            }
            else
            {
                return '';
            }
        }
        else
        {
            return $twig->render( 'form.html.twig', [] );
        }
    }

    function testRecaptchaSuccess( string $token ) : bool
    {
        $secretKey = file_get_contents( '.gskey' );
        $ip = $_SERVER[ 'REMOTE_ADDR' ];
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [ 'secret' => $secretKey, 'response' => $token ];
        $options =
        [
            'http' =>
            [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        $context = stream_context_create( $options );
        $response = file_get_contents( $url, false, $context );
        $responseKeys = json_decode( $response, true );
        var_dump( $responseKeys );
        return $responseKeys[ 'success' ] && $responseKeys[ 'score' ] >= 0.5;
    }
