( function()
{
    let currentState = 'showAllAltTags';

    const validAlts = document.getElementsByClassName( 'valid-alt' );
    const allValidTables = document.getElementsByClassName( 'res-i-all-valid' );

    const buttons =
    {
        showAllAltTags: document.getElementById( "btn-show-all" ),
        showOnlyMissingAltTags: document.getElementById( "btn-show-only-errors" )
    };

    const changeElementDisplay = function( e, display )
    {
        e.setAttribute( 'style', `display:${ display }` );
    };

    const generateListener = function( state )
    {
        return function()
        {
            if ( currentState !== state )
            {
                currentState = state;
                const display = ( state === 'showOnlyMissingAltTags' ) ? 'none' : 'table-row';
                for ( const e of allValidTables )
                {
                    changeElementDisplay( e, display );
                }
                for ( const e of validAlts )
                {
                    changeElementDisplay( e, display );
                }
            }
        };
    };

    Object.keys( buttons ).forEach
    (
        function( state )
        {
            buttons[ state ].addEventListener( 'click', generateListener( state ) );
        }
    );
})();
