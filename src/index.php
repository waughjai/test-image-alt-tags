<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once( 'vendor/autoload.php' );
    use WaughJ\WebpageLinksList\WebpageLinksList;
    use WaughJ\ImageAltTag\ImageAltTagList;

    $url = $_POST[ 'url' ];
    $links_list = new WebpageLinksList( $url );
    $links_data = $links_list->getData();

    $websites = [];
    foreach ( $links_data as $url => $data )
    {
        $alts = new ImageAltTagList( $data->raw_body );
        $alts = $alts->getImageAltTags();
        $websites[] = [ 'url' => $url, 'alts' => $alts ];
    }

    $loader = new \Twig\Loader\FilesystemLoader( 'templates' );
    $twig = new \Twig\Environment
    (
        $loader,
        [ 'cache' => '.cache' ]
    );
    echo $twig->render( 'index.html.twig', [ 'websites' => $websites ] );
