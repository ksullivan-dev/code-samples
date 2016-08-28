tabbedList: function(){
	$( '.values-list' ).each( function(){
        $( this ).find( '.values-list__content-item' ).first().addClass( 'js-values-list__content-item--active' );
		$( this ).find( '.values-list__link' ).click( function( e ){
			e.preventDefault();
			$( $( e.currentTarget ).attr( 'href' ) ).siblings().removeClass( 'js-values-list__content-item--active' );
			$( $( e.currentTarget ).attr( 'href' ) ).addClass( 'js-values-list__content-item--active' );
		});
	});
}

$( function(){
    tabbedList();
});
