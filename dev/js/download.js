( function()
{
    const homeURL = document.getElementById( 'home-url' ).innerHTML.replace( /http(s)?:\/\//g, '' );
    const header = document.getElementById( 'res-head' );
    const html = `<!DOCTYPE html>${ document.getElementById( 'html' ).outerHTML }`;
    const data = `data:application/octet-stream;charset=utf-16;base64,${ btoa( html ) }`;

    // Create Download Link Container
    const downloadLinkContainer = document.createElement( 'div' );
    downloadLinkContainer.setAttribute( 'class', 'res-download' );

    // Create Download Link
    const downloadLink = document.createElement( 'a' );
    downloadLink.setAttribute( 'href', data );
    downloadLink.setAttribute( 'download', `${ homeURL }.img-alt-tags.html` );
    downloadLink.setAttribute( 'class', 'res-download-link' );
    downloadLink.innerHTML = 'Download';

    downloadLinkContainer.appendChild( downloadLink );
    header.appendChild( downloadLinkContainer );
})();
