flipCard: function(){
    $( '.flip-card__flipper' ).click( function( e ){
        e.preventDefault();
        $( e.currentTarget ).closest( '.flip-card' ).addClass( 'flip-card--flipped' );
    });
    $( '.flip-card__close' ).click( function( e ){
        e.preventDefault();
        $( e.currentTarget ).closest( '.flip-card' ).removeClass( 'flip-card--flipped' );
    });
}

changeCard: function(){
    $( '.change-card' ).click( function( e ){
        e.preventDefault();
        var transactionCard = $( e.currentTarget ).closest( '.transactions-container' ).find( '.recent-transactions-card' );
        var position = 'last';
        var sibling = 'prev';
        var oppPos = 'first';
        var location = 'before';
        if( $( e.currentTarget ).hasClass( 'next-card' ) ){
            position = 'first';
            sibling = 'next';
            oppPos = 'last';
            location = 'after';
        }
        transactionCard.not( '.flip-card--hidden' )[ position ]().addClass( 'flip-card--hidden' );
        transactionCard.not( '.flip-card--hidden' )[ oppPos ]()[ sibling ]().removeClass( 'flip-card--hidden flip-card--flipped' );
        if( ! transactionCard[ oppPos ]().hasClass( 'flip-card--hidden' ) ){
            transactionCard[ oppPos ]()[ location ]( transactionCard[ position ]() );
        }
    });

    $( '.transactions-container' ).each( function(){
        $( this ).find( '.recent-transactions-card' ).first().before( $( this ).find( '.recent-transactions-card' ).last() );
    });
}

$( function(){
    flipCard();
    changeCard();
});
