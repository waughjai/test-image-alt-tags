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

/*
    $captcha = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
    $secretKey = "6LdgGqIUAAAAAPkIgpUKT6X9O-PaWnPsuqd5A-lb";
    $ip = $_SERVER[ 'REMOTE_ADDR' ];
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array('secret' => $secretKey, 'response' => $captcha);
  $options = array(
    'http' => array(
      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
      'method'  => 'POST',
      'content' => http_build_query($data)
    )
  );
  $context  = stream_context_create($options);
  $response = file_get_contents($url, false, $context);
  $responseKeys = json_decode($response,true);
  header('Content-type: application/json');
  if($responseKeys["success"]) {
    echo json_encode(array('success' => 'true'));
  } else {
    echo json_encode(array('success' => 'false'));
  }*/



    $loader = new \Twig\Loader\FilesystemLoader( '../src/templates' );
    $twig = new \Twig\Environment
    (
        $loader,
        [ 'cache' => '../src/.cache' ]
    );

    if ( TestHashItemExists( $_POST, 'url', null ) !== null )
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
